# Testbericht vom 22.10.2026

## Umfang und Umgebung

Ausschließlich neue lokale Docker-Projekte `mgms-local` und `mgms-min`, ohne Kundenverbindung. Portbindung nur an 127.0.0.1, eigene MariaDB-11.4-Testdatenbanken im flüchtigen Speicher, Plugin-Verzeichnis schreibgeschützt eingebunden. Testkonten wurden zufällig lokal erzeugt und nie ausgegeben. Kein Remote-Repository, kein Upload, keine Live-Installation.

Geprüfte WordPress-Versionen: **6.5 und 7.1.3**, jeweils mit **PHP 8.1.34**. Zusätzlich anfänglicher vollständiger Durchlauf mit WordPress 6.9. Die offizielle Versionsschnittstelle bestätigte während der Prüfung 7.1.3 als aktuelle stabile Version. PHP-Lint auch auf dem Host mit 8.5.10. Browser: installierter Google Chrome, automatisiert über Playwright.

## Bestandene Prüfungen

| Prüfung | Ergebnis |
|---|---|
| PHP-Lint | Alle 12 Plugin-PHP-Dateien unter PHP 8.1 und 8.5 ohne Syntaxfehler |
| JavaScript-Syntax | Alle drei ausgelieferten Skripte bestehen `node --check` |
| WordPress Coding Standards | PHP-Projektprofil `phpcs.xml.dist`: keine Fehler oder Warnungen |
| PHPCompatibilityWP | Prüfung ab PHP 8.1 ohne gemeldete Verstöße |
| Integration | 30 Prüfungen je WordPress-Testversion bestanden |
| Browserabläufe | 19 Prüfungen je WordPress-Testversion bestanden |
| Zusätzliche Sicherheitsfälle | 11 Prüfungen mit WordPress 7.1.3 bestanden |
| Design und Barrierearmut | Drei Presets jeweils bei 1440 und 390 Pixeln: kein Überlauf, keine axe-WCAG-2.1-AA-Verstöße; Admin-Oberfläche ebenfalls geprüft |
| Lebenszyklus | Daten erhalten / Daten löschen / inaktive Neuaktivierung / Testdaten zurückspielen: vier Prüfungen bestanden |
| Paket | ZIP-Inhalte, CRC und SHA-256 gegen lokale Quelldateien geprüft; keine Testzugänge oder Entwicklungswerkzeuge enthalten |

Die 30 Integrationstests prüfen IPv4/IPv6 und Präfixgrenzen, ungültige Netze, gefälschte Forwarded-Header, Proxy-Ketten, Standardwerte, zu große Freigaben, JavaScript-URLs, CSS-Injektionen, Rechtstext-Filterung, Admin-/Abonnenten-/Rollenregeln, Statusverhalten bei Plugin-Deaktivierung/Reaktivierung und exakte Entwurfswiederherstellung in der Datenbank.

Die Browserprüfungen umfassen tatsächliche 503-/Retry-After-/Robots-/Cache-Header, anonym fehlenden Badge, normalen Admin-Zugriff mit Badge, Dialogfokus, Tab-Falle, Escape, Fokusrückgabe, Rechtstexte ohne JavaScript, reduzierte Bewegung, REST-Sperre, fehlende Nonce, einmalige Vorschau, Live-Textänderung, Preset-Rücknahme, Trennung von Entwurf und Veröffentlichung, veröffentlichten Rückfall und bestätigtes Abschalten. Geprüfte Breiten: 390, 768 und 1440 Pixel. Keine externen Ressourcen oder JavaScript-Laufzeitfehler auf der getesteten Wartungsseite.

Weitere Negativtests: eingeloggter Abonnent bleibt gesperrt, fremde/anonyme Vorschau, abgelaufener Vorschau-Token, Status-/Veröffentlichungsversuch ohne Bestätigung, Schreibversuch ohne Administratorberechtigung, veraltete Entwurfsrevision, tatsächliche IP-Freigabe und deren Rücknahme, separate Vorschauabschaltung.

## Während der Prüfung korrigiert

Die erste Deinstallations-Testanordnung rief WordPress zweimal im selben Prozess auf. Da WordPress die Deinstallationsdatei nur einmal lädt, wurde der Test auf getrennte Prozesse korrigiert. Mit dieser realitätsnahen Anordnung bestehen beide Löschvarianten.

Die neue Tab-Auswahl zeigte bei Hover/Fokus zeitweise zu wenig Kontrast. Die Zustandsfarben sind jetzt ausdrücklich gesetzt, Farbtransitionen abgeschaltet und anschließend erneut mit axe geprüft.

Der ungefilterte WordPress-Standard meldete anfangs auch PHPDoc- und JavaScript-Formatregeln. Das eingecheckte Profil prüft PHP und nimmt verpflichtende PHPDoc-Blöcke zugunsten des gewünschten kurzen deutschen Kommentarstils aus. Lokale Renderer-Ausnahmen sind direkt im Code begründet. JavaScript wird mit Syntax- und echten Browsertests geprüft. Es wird kein uneingeschränktes Bestehen sämtlicher WPCS-Regeln für sämtliche Dateitypen behauptet.

## Offene Abnahme und Grenzen

- Keine Screenreader-Prüfung mit VoiceOver/NVDA, kein vollständiges manuelles WCAG-Audit, kein automatisches Rechtskonformitätsversprechen.
- Kein realer Kunden-Theme-, Divi-, Shield-, CDN- oder Proxy-Test. Multisite-Löschschleife implementiert, aber nicht in einem realen Multisite-Netzwerk getestet.
- Upload/Anzeige eigener Logo-, Hintergrund- und WOFF2-Dateien sowie deren Lizenzen bleiben Betreiberabnahme. Mediathek-Auswahl und serverseitige Herkunfts-/Typprüfung sind implementiert.
- Countdown, Zeitzone, echte Kontaktlinks und echte Rechtstexte benötigen inhaltliche Betreiberprüfung.
- Live-Vorschau aktualisiert Texte und Design unmittelbar; strukturelle Änderungen brauchen Speichern und erneutes Öffnen.
- Nur Deutsch; keine fertigen Übersetzungen. Keine temporären IP-Freigaben, keine personenbezogenen Änderungsprotokolle.
- Entwurfsrevision verhindert veraltete Tabs, aber kein vollständiges Locking exakt gleichzeitiger Schreibvorgänge.
- Wartung ist keine Firewall. Direkte Dateien, XML-RPC, Caches, authentifizierte REST-Aufrufe, AJAX und externe Dienste benötigen separate Bewertung.
- Ein vorheriger Stand ersetzt kein vollständiges Dateien-/Datenbankbackup.

Die manuelle Checkliste steht in ABNAHME.md. Lokale Tests sind keine Live-Freigabe.
