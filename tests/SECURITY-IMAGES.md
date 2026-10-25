# Testimage-Update vom 25.10.2026

Nur für neue, wegwerfbare lokale Tests und CI. Keine Shopinstallation wird aktualisiert. PHP 8.1 bleibt die Mindestanforderung des Plugins; die neue Haupt-Testumgebung ist kein Nachweis für diese Mindestversion.

Das nicht mehr unterstützte PHP-8.1-Testimage wird durch das offizielle `wordpress:7.1.3-php8.3-apache` ersetzt. WordPress- und Datenbankimage sind nun per SHA-256 fixiert. Die MariaDB-Identität bleibt unverändert in der 11.4-Familie; es findet keine Datenbankmigration statt.

## Nachweise

Trivy 0.76.1, Linux/amd64, vollständiger Datenbestand vom 24.10.2026 19:09 UTC:

- Altes WordPress/PHP-8.1-Image: 9.434 Paketbefunde.
- Neuer Kandidat: 2.253 Paketbefunde, davon 160 hoch und 2 kritisch. Nicht mit eindeutigen CVEs oder bestätigter Ausnutzbarkeit gleichsetzen.
- Zwei kritische Restmeldungen ohne ausgewiesene Trixie-Korrektur: libxml2/CVE-2026-6653 und linux-libc-dev/CVE-2026-93221. Header sind kein laufender Kernel. Kein Wechsel zu unstable und keine Unterdrückung.
- MariaDB 11.4.13/Noble: weiterhin 90 Paketbefunde, darunter eine kritische Meldung zur Go-Standardbibliothek in `/usr/local/bin/gosu`. Gleicher Digest wie bisher, keine vorgetäuschte Behebung durch Umbenennung. Das alternative offizielle UBI9-Image hat 501 Befunde und wird verworfen.

`bash tests/container-smoke.sh` hat lokal eine frische Installation mit WordPress 7.1.3 und PHP 8.3.36 eingerichtet und alle 30 Integrationstests bestanden (Aktivierung, IP-/Rollenfreigaben, Injektionsabwehr, Zustandserhalt und Entwurfs-Rollback). Ein neuer CI-Job wiederholt den Funktionstest. Er bestätigt die Kompatibilität, keine Sicherheitsfreiheit des Images.

Der Test nutzt ein neues internes Netz, keine Hostports, eine neue flüchtige Datenbank und neu erzeugte Testzugänge. Secrets werden nicht ausgegeben. Nur die selbst angelegten Container/Volumes werden am Ende entfernt. Bestehende Test-/Kundendaten bleiben unangetastet.

## Freigabe

Vorbereiteter Teilfortschritt; nicht als vollständig sicherheitsbereinigtes Image freigeben. Vor einer erweiterten Freigabe erneuter vollständiger Scan und Herstellerkorrekturen bzw. belastbare Einzelbewertung erforderlich. Ein serverseitiger Einsatz wird damit nicht beauftragt. Rückfall für die Konfiguration: vorheriger Git-Stand; keine vorhandene DB auf einen älteren Server zurücksetzen.

Quellen: [offizielles WordPress-Image](https://hub.docker.com/_/wordpress), [MariaDB-Image](https://hub.docker.com/_/mariadb), [Debian libxml2](https://security-tracker.debian.org/tracker/CVE-2026-6653), [Debian Kernelmeldung](https://security-tracker.debian.org/tracker/CVE-2026-93221), [gosu 1.19](https://github.com/tianon/gosu/releases/tag/1.19).
