#!/usr/bin/env bash
# Frische Wegwerf-Datenbank, internes Netz, keine Hostports und keine vorhandenen Volumes.
set -euo pipefail
root=$(cd "$(dirname "$0")/.." && pwd)
prefix="mgms-security-${RANDOM}-$$"
fixture=$(mktemp -d)
cleanup() {
  docker rm -fv "$prefix-wp" "$prefix-db" >/dev/null 2>&1 || true
  docker network rm "$prefix" >/dev/null 2>&1 || true
  rm -f "$fixture/db.env" "$fixture/wp.env"
  rmdir "$fixture" 2>/dev/null || true
}
trap cleanup EXIT
python3 - "$fixture" "$prefix-db" <<'PY'
import pathlib,secrets,sys
p=pathlib.Path(sys.argv[1]);password=secrets.token_hex(24)
values={'db.env':f'MARIADB_RANDOM_ROOT_PASSWORD=yes\nMARIADB_DATABASE=maintenance_test\nMARIADB_USER=maintenance_test\nMARIADB_PASSWORD={password}\n','wp.env':f'WORDPRESS_DB_HOST={sys.argv[2]}\nWORDPRESS_DB_USER=maintenance_test\nWORDPRESS_DB_PASSWORD={password}\nWORDPRESS_DB_NAME=maintenance_test\n'}
for name,text in values.items():
 file=p/name;file.touch(mode=0o600);file.write_text(text)
PY
docker network create --internal "$prefix" >/dev/null
docker run -d --name "$prefix-db" --platform linux/amd64 --network "$prefix" --env-file "$fixture/db.env" --tmpfs /var/lib/mysql mariadb:11.4.13-noble@sha256:250e1609e93c0e6aecf64bfbab2d84d5e89d2481540fe77447ff6c855c3b1873 >/dev/null
ready=0
for attempt in $(seq 1 45); do
  if docker exec "$prefix-db" healthcheck.sh --connect --innodb_initialized >/dev/null 2>&1; then ready=1;break;fi
  sleep 1
done
test "$ready" = 1
docker run -d --name "$prefix-wp" --platform linux/amd64 --network "$prefix" --env-file "$fixture/wp.env" --mount "type=bind,src=$root/mg-maintenance-studio,dst=/var/www/html/wp-content/plugins/mg-maintenance-studio,readonly" wordpress:7.1.3-php8.3-apache@sha256:d42f63cb89ad0eab272c4b393164e63830d0df270009907919f7dcce52b1a20b >/dev/null
ready=0
for attempt in $(seq 1 45); do
  if docker exec "$prefix-wp" test -f /var/www/html/wp-config.php; then ready=1;break;fi
  sleep 1
done
test "$ready" = 1
for script in install-local integration; do
  docker cp "$root/tests/$script.php" "$prefix-wp:/tmp/mgms-$script.php" >/dev/null
  docker exec -e MGMS_TEST=isolated-local "$prefix-wp" php "/tmp/mgms-$script.php"
done
