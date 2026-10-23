# Änderungsprotokoll

## 0.5.1 – 23.10.2026

- Die Statusplakette auf der öffentlichen Website ist für Administratoren jetzt ein klarer AN/AUS-Schalter am selben Platz.
- Die Änderung läuft ohne Seitenwechsel über einen WordPress-Nonce und prüft weiterhin `manage_options`; Besucher und normale Konten sehen keine Steuerung.
- JavaScript-Prüfung um die neue Statussteuerung ergänzt.

## 0.5.0 – 23.10.2026

- Wartungsseite unterstützt im Markenbereich nur Text, nur Logo oder Text und Logo.
- Logo und Hintergrundbild werden im Editor als Bildauswahl mit Vorschau dargestellt; technische Attachment-IDs bleiben verborgen.
- Einheitlicher Wartungsmodus-Schalter oben rechts auf Übersicht, Editor und Fehlerseiten. Das Hauptmenü steht am Ende der WordPress-Navigation.
- Vier Vorlagen erklären und zeigen eigenständige Layoutformen. Der Verlassen-Hinweis erscheint nicht mehr beim Speichern.

## 0.4.0 – 23.10.2026

- Eigenes WordPress-Hauptmenü mit Übersicht, Wartungsseite, Layouts & Design, Zugriffen, Fehlerseiten und Updates.
- Dashboard mit Wartungsstatus, Entwurfsstand und klar getrennten Aktionen.
- Vier Wartungsseiten-Layouts, vier Vorlagen, Farb-, Logo- und Hintergrundeinstellungen.
- Geschützte Live-Vorschau mit Desktop-/Mobilansicht und direkter Medienaktualisierung.
- Bestehende Einstellungen behalten das bisherige Editorial-Layout.


## 0.3.2 – 23.10.2026

- Getrennte 404- und 403-Fehlerseiten unter Einstellungen ergänzt.
- Drei Vorlagen, individuelle Texte und Farben, 404-Suche, Entwurf und bewusste Veröffentlichung.
- Isolierte lokale WordPress-Prüfung mit echten HTTP-Statuscodes 404 und 403.


## Dokumentation und Sicherheitsprüfung – 22.10.2026

- README mit Projektbild, Schnellstart, Funktionsübersicht und Handbuchnavigation neu aufgebaut.
- Elf Handbuchseiten mit Wiki-Export, Security Policy und nachvollziehbarem Sicherheitsbericht ergänzt.
- Historische Aussagen zur Veröffentlichung korrigiert.
- CI um Dokumentationslinks und Secret-Scans für Historie und Quellstand erweitert.
- Plugin-Laufzeitcode unverändert; veröffentlichtes Release bleibt 0.3.1.


## 0.3.1

- Öffentliches Repository MGD-Maintenance-WordPress als Updatequelle vorbelegt.
- Lokale PNG-Icons für Pluginliste und WordPress-Updateinformationen ergänzt.
- GitHub-Prüfung bleibt bis zur bewussten Aktivierung ausgeschaltet.

## 0.3.0

- Optionale GitHub-Releases über native WordPress-Aktualisierungen.
- Getrennte Updateeinstellungen, manuelle Prüfung, Fehler- und Versionsanzeige.
- Lokales Wartungs-Icon in Kopfzeile und Pluginliste, Einstellungen-Link und Detailfenster.
- Ruhige Kartenoberfläche nach dem Vorbild der MGD AI Kennzeichnung.
- Strenge Release- und URL-Prüfung, positive und negative Zwischenspeicherung.

## 0.2.0 – 22.10.2026

Gestaltung nach ECC Frontend Design Direction überarbeitet: linksbündige Wartungsmitteilung, direkte Standardtexte, zurückhaltende Farben und neue Vorlagenwerte. Schmuckstern, Werbeslogan und große Standardkarte entfernt. Backend an WordPress-Einstellungen angelehnt; Statuszeilen statt Kennzahlenkarten. Tabellenbreiten, Tastatur-Tabs, Farbkontrast und lange Texte geprüft. Bestehende gespeicherte Einstellungen und Sicherheitsregeln bleiben erhalten. Lokale Screenshots und ECC-Prüfdatensatz ergänzen die Dokumentation. Kein Live-Einsatz.

## 0.1.0 – 22.10.2026

Lokale Erstimplementierung von MG Maintenance Studio mit getrenntem Entwurfs-, Veröffentlichungs- und Wartungsstatus. Designeditor, drei lokale Vorlagen, geschützte Vorschau, Rechtstextdialoge, Kontaktlinks, Countdown, IP-/Proxy- und Benutzerregeln, Crawlerhinweise, Rückfall- und Löschfunktionen. Neue lokale Integrations- und Browserprüfungen sowie deutschsprachige Betreiber- und Datenschutzdokumentation. Kein Live-Deployment, keine Veröffentlichung, keine Kundenverbindung.
