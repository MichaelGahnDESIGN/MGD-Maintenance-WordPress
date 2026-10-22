# MG Maintenance Studio

Eigenständiges WordPress-Plugin für einen gestaltbaren Wartungsmodus. **Version 0.3.1 – vor Kundeneinsatz auf einer Testinstallation abnehmen.** WordPress ab 6.5, PHP ab 8.1. Lizenz: GPL-2.0-or-later.

Das Plugin wurde ausschließlich in einer neu angelegten lokalen WordPress-Testinstallation entwickelt. Kein Kundenserver wurde verbunden, kein Remote-Repository eingerichtet, nichts veröffentlicht.

## Was enthalten ist

- Separate Schritte für Entwurf, ausdrücklich bestätigte Veröffentlichung und ausdrücklich bestätigten Wartungsstatus.
- Gestaltungseditor mit geschützter Live-Vorschau; drei lokale Vorlagen: Elegant Gold, Ruhiges Atelier und Minimalistisch Hell.
- Lokale Mediathek-Bilder, Logo, Systemschriften und bereits freigegebene WOFF2-Dateien, Farben, Container, Abstände für drei Bildschirmgrößen und optionale Animation.
- Eigene Rechtstexte mit HTML-Grundformatierung, Sortierung, Footerlinks, tastaturbedienbaren Dialogen und Ankerlinks ohne JavaScript.
- Kontaktlinks ohne Formular oder Datenerfassung; Countdown als reiner Hinweis ohne automatische Umschaltung.
- IPv4/IPv6/CIDR-Freigaben, explizite Proxy-Vertrauensstellung, Benutzer- und Rollenregeln, dezenter Wartungshinweis für freigegebene eingeloggte Besucher.
- 503/Retry-After, Robots-Meta und HTTP-Header, optionale virtuelle robots.txt, keine externen Ressourcen auf der eigenständig gerenderten Wartungsseite.
- Ein vorheriger Entwurf und veröffentlichter Stand zur Wiederherstellung; optionale Datenlöschung bei Deinstallation.

## Projektaufbau

`mg-maintenance-studio/` ist das installierbare Plugin-Verzeichnis. `includes/` trennt Felddefinitionen, Validierung, Zugriff, IP-Auflösung, Vorschau, Admin und Ausgabe. `templates/` enthält die beiden Oberflächen; `assets/` lokales CSS/JavaScript, `presets/` die drei Vorlagen. `tests/` enthält lokale Integrations- und Browserprüfungen. `mg-maintenance-studio/docs/` enthält die deutschsprachigen Betriebs- und Sicherheitsinformationen. `.local/`, Zugangsdaten, Werkzeuge und Testartefakte sind von Git ausgeschlossen.

## Gestaltung

Version 0.2.0 ersetzt die große Schmuckkarte durch eine ruhige Wartungsmitteilung. Das Backend orientiert sich an WordPress-Einstellungen. Die Gestaltung und lokalen ECC-Nachweise sind in [DESIGN.md](mg-maintenance-studio/docs/DESIGN.md) dokumentiert. Gespeicherte Betreiberwerte werden nicht automatisch ersetzt.

## Dokumentation

- [Installation und Bedienung](mg-maintenance-studio/docs/BEDIENUNG.md)
- [Architektur und Sicherheit](mg-maintenance-studio/docs/ARCHITEKTUR.md)
- [Datenschutz und Aufbewahrung](mg-maintenance-studio/docs/DATENSCHUTZ.md)
- [Backup, Wiederherstellung und Deinstallation](mg-maintenance-studio/docs/BETRIEB.md)
- [Testbericht und bekannte Grenzen](mg-maintenance-studio/docs/TESTBERICHT.md)
- [Manuelle Betreiberabnahme](mg-maintenance-studio/docs/ABNAHME.md)
- [Änderungsprotokoll](CHANGELOG.md)

## Lokale Prüfungen

PHP-Syntax: `find mg-maintenance-studio -name '*.php' -exec php -l {} \;`

JavaScript-Syntax: `node --check mg-maintenance-studio/assets/admin.js` entsprechend für frontend.js und preview.js.

Composer installiert nur Entwicklungswerkzeuge. Wegen langsamer NAS-Dateioperationen kann `composer.json` in einen separaten lokalen Werkzeugordner kopiert werden. Coding Standards und PHP-Kompatibilität werden mit PHPCS ausgeführt. Der konkrete Prüfstand steht im Testbericht.

Die Integrationstests verweigern die Ausführung ohne `MGMS_TEST=isolated-local` und erwarten die eigens erstellte Docker-Testinstallation. Sie ändern absichtlich nur deren Testdaten. Browserprüfungen verwenden `http://127.0.0.1:18865`, lesen den lokal erzeugten Zugang aus `.local/login.json` und geben ihn nicht aus. Nicht gegen eine bestehende WordPress-Installation ausführen.

## Wichtige Grenze

Der Wartungsmodus ist keine Firewall und kein Schutz vertraulicher Dateien. Webserver, CDN und Cache können WordPress umgehen. Anmeldung, Backend, Cron, AJAX und authentifizierte REST-Anfragen behalten ihre eigenen WordPress-Berechtigungen. Direkte Upload-URLs und XML-RPC werden nicht abgeschirmt. Für eine spätere Live-Installation sind Backup, Wiederherstellung, Testumgebung und ausdrückliche Freigabe separat zu bestätigen.

## GitHub-Updates und Produktdarstellung

Version 0.3.0 ergänzt ein eigenes lokales Icon, Plugin-Details und den Reiter Updates. Das öffentliche Repository wird einmal eingerichtet; anschließend erscheinen passende Releases in den normalen WordPress-Aktualisierungen. Updatequelle: `MichaelGahnDESIGN/MGD-Maintenance-WordPress`. Die Prüfung ist zunächst ausgeschaltet. Ablauf und Grenzen: [UPDATES.md](mg-maintenance-studio/docs/UPDATES.md).
