# Testbericht vom 22.10.2026

## Nachprüfung der Gestaltung 0.2.0

Erneut lokal unter WordPress 7.1.3 / PHP 8.1.34 geprüft: 30 Integrationstests, 19 Browserabläufe und 11 Sicherheitsfälle bestanden. Drei Vorlagen jeweils bei 390×844, 768×1024 und 1440×900 ohne Überlauf oder automatisch gefundene WCAG-2.2-AA-Verstöße. Admin-Übersicht mit axe, Tastatur-Tabs und Designformular bei drei Breiten geprüft. Lange Texte und leere optionale Bereiche zusätzlich geprüft. Kein externer Ressourcenabruf oder JavaScript-Laufzeitfehler in der geprüften Wartungsansicht. PHP-Lint und PHP-WPCS-Projektprofil fehlerfrei.

Ein um die Rahmenbreite zu breites Vorschaufenster wurde mit border-box korrigiert. Lange Admin-Aktionsnamen können umbrechen. Der ECC-Nachweis liegt lokal unter artifacts/redesign/evidence.json; die Bilder wurden zusätzlich angesehen. Der Checker prüft die Vollständigkeit des Nachweises und ersetzt kein visuelles Urteil.

ECC Browser QA: Funktion und Gestaltung lokal geprüft. Automatische Pixelregression bleibt INCONCLUSIVE, weil keine eingecheckte Screenshot-Baseline vorliegt. Vorher-/Nachher-Bilder wurden lokal verglichen. Vollständiger Screenreader-Test und Core-Web-Vitals-Feldmessung (insbesondere INP) sind nicht erfolgt. Keine Live-Freigabe. Die Mindestversions- und Lebenszyklusprüfungen im folgenden historischen Abschnitt gehören zu 0.1.0; die Überarbeitung ändert keine WordPress-API- oder Zugriffslogik.

## Historischer Prüfstand 0.1.0: Umfang und Umgebung

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

## Erweiterung 0.3.0 – GitHub-Updates und Verwaltung

Lokal mit WordPress 7.1.3 und PHP 8.1.34 geprüft:

- 18 Updateprüfungen: Zustimmung, Versions-/Paketvalidierung, Entwurf/Vorabversion,
  fremde URLs, Zwischenspeicherung, WordPress-Update-URI-Hook, gleiche Version und Ausfall.
- Tatsächlicher `Plugin_Upgrader` installiert das 0.3.0-ZIP über eine mit 0.2.0
  gekennzeichnete Wegwerfkopie. Pluginpfad, veröffentlichte Werte und Wartungsstatus
  bleiben erhalten. Der Download wird durch eine lokale ZIP-Kopie ersetzt.
- Sechs Browserprüfungen: Updates bei drei Bildschirmgrößen mit axe und Überlaufprüfung,
  fehlende Nonce, separates Speichern sowie Icon und natives Detailfenster.
- Bestehende 30 Integrationsprüfungen, 19 Browserabläufe, 11 Sicherheitsfälle und
  11 Prüfungen von Vorlagen/Verwaltung erneut erfolgreich.
- PHP-Lint, WordPress-Coding-Standards im Projektprofil und JavaScript-Syntax erfolgreich.
- Paketbau prüft Inhalt, ZIP-Integrität und SHA256 je Datei.

Screenshots: `artifacts/updates/settings-{1440,768,390}.png` und `plugin-list.png`.
Desktop und Smartphone zusätzlich visuell geprüft. Die Tabnavigation bleibt auf
kleinen Geräten umgebrochen; es gibt keinen horizontalen Überlauf. Keine Aussage
über vollständige WCAG-Konformität oder einen Screenreader-Praxistest.

Kein echtes GitHub-Release dieses Plugins veröffentlicht. GitHub-Antworten wurden
in den Updatetests simuliert. Ein Ende-zu-Ende-Test einschließlich öffentlichem
GitHub-Download bleibt nach Veröffentlichung erforderlich. Keine Kundenwebsite
verbunden, keine Live-Installation und keine Veröffentlichung vorgenommen.

## Öffentlicher Stand 0.3.1 und ergänzende Sicherheitsprüfung

Release 0.3.1 ist seit 22.10.2026 öffentlich unter MichaelGahnDESIGN/MGD-Maintenance-WordPress verfügbar. Die tatsächliche öffentliche Release-Abfrage und der Paketdownload wurden aus einer lokalen WordPress-Installation geprüft. Die zuvor genannten Aussagen „nicht veröffentlicht“ beziehen sich ausschließlich auf die historischen Prüfstände.

Die erneute Sicherheitsprüfung, Secret-Scans und aktuellen Testgrenzen sind im [Sicherheitsaudit](https://github.com/MichaelGahnDESIGN/MGD-Maintenance-WordPress/blob/main/docs/SECURITY-AUDIT.md) dokumentiert. Dieser Link bezieht sich auf den Quellcode im Repository; das Handbuch ist auch online über die Projekt-README erreichbar.
