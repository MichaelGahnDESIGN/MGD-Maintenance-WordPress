"""Lokales Plugin-Paket ohne Werkzeuge, Testzugänge oder private Dateien bauen."""
from pathlib import Path
from hashlib import sha256
from zipfile import ZipFile, ZipInfo, ZIP_DEFLATED
import json
import re

root = Path(__file__).resolve().parents[1]
plugin = root / 'mg-maintenance-studio'
output = root / 'artifacts'
output.mkdir(exist_ok=True)
# Version aus Plugin-Kopf
version = re.search(r'^\s*\*\s*Version:\s*(\S+)', (plugin / 'mg-maintenance-studio.php').read_text(encoding='utf-8'), re.M).group(1)
archive = output / f'mg-maintenance-studio-{version}.zip'
files = sorted(p for p in plugin.rglob('*') if p.is_file() and not any(part.startswith('.') for part in p.relative_to(plugin).parts))
allowed = {'.php', '.css', '.js', '.json', '.md', '.txt', '.svg', '.png', '.webp'}
assert files and all(not p.is_symlink() and (p.suffix in allowed or p.name == 'LICENSE') for p in files)
manifest = {}
with ZipFile(archive, 'w', ZIP_DEFLATED) as bundle:
    for path in files:
        name = str(path.relative_to(root))
        content = path.read_bytes()
        # Rechte 644 sonst 403 bei Icons
        info = ZipInfo(name, date_time=(2026, 1, 1, 0, 0, 0))
        info.compress_type = ZIP_DEFLATED
        info.external_attr = 0o100644 << 16
        bundle.writestr(info, content)
        manifest[name] = sha256(content).hexdigest()
with ZipFile(archive) as bundle:
    assert bundle.testzip() is None
    assert set(bundle.namelist()) == set(manifest)
    for name, digest in manifest.items():
        assert sha256(bundle.read(name)).hexdigest() == digest
(output / 'package-manifest.json').write_text(json.dumps(manifest, indent=2) + '\n', encoding='utf-8')
(output / 'SHA256SUMS.txt').write_text(sha256(archive.read_bytes()).hexdigest() + '  ' + archive.name + '\n', encoding='utf-8')
print(f'{archive.name}: {len(files)} Dateien, Inhalt und Prüfsummen bestätigt')
