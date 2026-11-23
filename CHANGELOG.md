# Änderungsprotokoll

## 0.10.0 – 24.11.2026

- Nachtstudio flackerte: Die Lichtkreise („Bokeh“) sprangen mit jedem Bild ein Stück weiter. Jetzt schweben sie ruhig.
- Rechtstexte: Standard ist jetzt ein Link im Footer, der den Text in einem Fenster (Modal) öffnet. Alternativ „Als Text zeigen“: Der Text steht zentriert unter dem Inhalt. Die Haken „Im Footer“ und „Als Modal“ sind durch die Auswahl „Anzeige“ ersetzt. Alte Einstellungen werden übernommen. Aktive Rechtstexte ohne Inhalt werden nicht mehr angezeigt.
- Hintergrundbild: Darstellung einstellbar (Ausschnitt, gestreckt, ganz sichtbar, wiederholen, Originalgröße) und Ausrichtung (Mitte, oben, unten, links, rechts und die Ecken). Gilt für alle Designs. Standard bleibt Ausschnitt in der Mitte, bestehende Seiten sehen gleich aus. Die Pro-Szene „Collage“ zeigt das Bild bewusst als Polaroid und behält ihre eigene Darstellung.

## 0.9.3 – 23.11.2026

- Wenn eine Pro-Szene ohne gültige Lizenz auf eine Free-Szene zurückfällt, bleibt der Text jetzt lesbar. Passt die Textfarbe der Pro-Szene nicht zum Hintergrund, wird automatisch eine helle oder dunkle Schrift verwendet.
- Betrifft vor allem „Märchenbuch“, das auf „Atelier“ zurückfällt. Eigene Farben in Free-Szenen bleiben unverändert.

## 0.9.2 – 23.11.2026

- Baukasten aktualisiert: Der Knopf „Installieren“ unter „Weitere Plugins“ wird jetzt vom Baukasten selbst angemeldet. Dateinamen für Slider und MichaTheme-Plugin werden erkannt.
- Keine sichtbaren Änderungen, Pro-Szenen unverändert.

## 0.9.1 – 22.11.2026

- „Weitere Plugins“ im neuen Design der MGD Plugin-Kollektion: Bilder, Kategorien, Suche, Filter und Details-Fenster. Kostenlose Plugins lassen sich mit einem Klick installieren (nie automatisch aktiviert).
- Die alte Seite „Weitere Plugins“ ist entfernt. Die Liste steht jetzt auf der Seite **Info und Updates**.
- Pro-Szenen unverändert.

## 0.9.0 – 21.11.2026

- **Pro-Szenen:** 8 zusätzliche Szenen (Märchenbuch, Nordlicht, Neon, Countdown-Bühne, Zeitung, Weltall, Zen, Collage) mit einer Pro-Lizenz von theme.michael-gahn.de.
- Lizenzfeld auf der Seite **Info und Updates**.
- Die Pro-Szenen kommen geschützt vom Server: digital unterschrieben, an die Domain gebunden, höchstens 7 Tage gültig. Danach holt das Plugin sie automatisch neu.
- Ohne Lizenz erscheinen die Pro-Szenen als Karte mit Schloss.
- Endet die Lizenz, werden die Pro-Dateien gelöscht und eine veröffentlichte Pro-Szene wechselt automatisch auf eine ähnliche Free-Szene. Die Wartungsseite bleibt immer erreichbar.

## 0.8.0 – 21.11.2026

- **Brückenversion:** Updates kommen jetzt von theme.michael-gahn.de (geprüft mit Größe und SHA-256), nicht mehr direkt von GitHub.
- Neue Seite **Info und Updates** mit Version, Update-Stand, „Weitere Plugins“ und dem Schalter „Installation melden“.
- Der alte GitHub-Updater und die alte Plugin-Liste sind abgeschaltet. Alte Update-Zwischenspeicher werden einmal aufgeräumt.
- Beim Löschen meldet sich das Plugin bei theme.michael-gahn.de ab.
- Alle Einstellungen der Wartungsseite bleiben erhalten.

## 0.7.0 – 21.11.2026

- Neue **Effekte** für die Wartungsseite: Regen, Schnee, Lichter (Glühwürmchen), Partikel (Sternenstaub), Lichtkugeln, Funken und Nebel.
- Stärke einstellbar: leicht, mittel oder stark. Die Live-Vorschau zeigt den Effekt sofort.
- Die Effekte laufen nur im Browser, ohne fremde Dateien. Sie pausieren, wenn der Tab nicht sichtbar ist, und sind bei „Bewegung reduzieren“ aus.
- Drei Vorlagen bringen passende Effekte mit: Elegant Gold (Funken), Nachtstudio (Lichtkugeln), Verspielter Moment (Sternenstaub).
- Standard bleibt „Kein Effekt“, bestehende Seiten ändern sich nicht.

## 0.6.0 – 21.11.2026

- Fünf neue, klar unterschiedliche Szenen für die Vorlagen:
  - **Elegant Gold → Kino:** Bild als Vollbild-Bühne, großer Titel wie im Film.
  - **Ruhiges Atelier → Atelier:** Bild füllt die linke Hälfte, Text rechts.
  - **Minimalistisch Hell → Raster:** riesige Überschrift, Bild versetzt gerahmt.
  - **Nachtstudio → Glas:** Glaskarte über langsam wandernden Leuchtflächen.
  - **Verspielter Moment → Bloom:** runde Karte, schwebende Farbkleckse.
- Jede Szene sieht auch ohne eigenes Bild gut aus (Verläufe, Formen, Muster).
- Neue Einstellung **Bildwirkung**: dezent, ausgewogen oder kräftig.
- Die bisherigen vier Layouts bleiben wählbar. Bestehende Seiten sehen nach dem Update unverändert aus.
- Bei „Bewegung reduzieren“ im System laufen keine Animationen.

## 0.5.9 – 21.11.2026

- Fehler behoben: Ein SVG-Logo aus der Mediathek führte beim Speichern zur Fehlerseite „nur passende lokale Mediathek-Dateien sind erlaubt“. SVG ist jetzt für Logo, Hintergrund und Gimmick erlaubt. Die Datei wird nur als Bild eingebunden.
- Die Mediathek zeigt bei der Bildauswahl nur noch Formate, die auch gespeichert werden können (JPG, PNG, WebP, GIF, AVIF, SVG).
- Die Fehlermeldung nennt jetzt die erlaubten Formate.

## 0.5.8 – 27.10.2026

- Icons und Hintergründe der Karten unter „Weitere Plugins“ kommen jetzt aus theme.michael-gahn.de. Der Server holt sie einmal und speichert sie lokal; der Browser lädt nichts extern. Ohne Bild bleibt das mitgelieferte Motiv.
- Versionen werden ohne führendes „v“ angezeigt.

## 0.5.7 – 25.10.2026

- Produktname im Backend auf **MGD Maintenance Studio** vereinheitlicht.
- Vorlage „Verspielter Moment“ ergänzt; die fünf Vorlagen unterscheiden sich sichtbar in Layout, Farbe und Stil.
- Optionales Gimmick-Bild aus der lokalen Mediathek mit Position und sanfter Animation ergänzt. Bei reduzierter Bewegung bleibt es statisch.

## 0.5.6 – 24.10.2026

- Sieben eigene Kartenmotive, lokal als kleine WebP-Dateien eingebunden.
- Lesbare Kategorie-/Versionsschilder, dekorative Bilder ohne Screenreader-Dopplung und Systemfarben-Unterstützung.
- Neue Katalogansicht auch bei zuerst geladenen älteren MGD-Plugins.

## 0.5.4 – 23.10.2026

- Neue Plugin-Kollektion mit eigenen Icons, Produktkarten, lokaler Suche und Filtern.
- Details im tastaturbedienbaren Dialog; bewusste Installation ohne automatische Aktivierung.
- Vollständiger Ersatzkatalog bei Feed-Ausfall; alle sieben bekannten WordPress-Plugins werden berücksichtigt.
- Installationsstatus frisch ermitteln, ZIP-Assets gezielt prüfen und native WordPress-Updateaktionen verwenden.
- Lokale Browser-, Rechte-, Nonce-, Paket- und Installationstests dokumentiert; kein Kunden-Deployment.

## 0.5.2 – 23.10.2026

- Die WordPress-Anmeldeseite zeigt bei Fehlern einheitlich „Die Anmeldung war nicht möglich. Bitte prüfe deine Eingaben.“
- Konkrete Hinweise auf unbekannte E-Mail-Adressen, Benutzernamen oder falsche Passwörter entfallen. Die Funktion gilt bei aktivem Plugin unabhängig vom Wartungsstatus.
- Authentifizierung, interne Fehlercodes, Sicherheits-Sperren und Informationsmeldungen bleiben unverändert; keine zusätzlichen Einstellungen oder Datenspeicherung.

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
