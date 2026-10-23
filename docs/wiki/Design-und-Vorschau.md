# Design und geschützte Vorschau

[Zur Übersicht](Home.md) · [Weiter: Rechtstexte](Rechtstexte-und-Kontakt.md)

## Vier Vorlagen und Layouts

| Vorlage | Charakter |
|---|---|
| Ruhiges Atelier | Naturtöne und geteilte Ansicht mit zwei Bereichen |
| Elegant Gold | Heller redaktioneller Aufbau mit goldenen Akzenten |
| Minimalistisch Hell | Zentrierte, reduzierte Gestaltung mit Systemschrift |
| Nachtstudio | Dunkles Poster mit prägnanter Mitteilung |

Die Vorlagen setzen Designwerte im Formular. Texte und Zugangsregeln bleiben erhalten. Die Rückgängig-Funktion bezieht sich auf Formularänderungen; für gespeicherte Stände gibt es die gesonderte Wiederherstellung.

![Beispiel Ruhiges Atelier](../images/wartungsseite-atelier.png)

## Was sich gestalten lässt

Markenname, kurze Einordnung, Überschrift, Beschreibung und Zusatzhinweise bilden die Hauptmitteilung. Dazu kommen Farben für Hintergrund, Text, Akzente und Schaltflächen, maximale Breite, Ausrichtung, Rahmen, Rundung, optionaler Schatten sowie getrennte Innenabstände für Desktop, Tablet und Smartphone.

Logo und Hintergrundbild stammen aus der WordPress-Mediathek. Unterstützt werden JPEG, PNG, WebP, GIF und AVIF. Die Datei muss denselben Ursprung wie die Website verwenden: Protokoll, Hostname und gegebenenfalls Port müssen übereinstimmen. Ausgelagerte CDN-Medien werden damit nicht akzeptiert.

Systemschriften funktionieren ohne Download. Für eine eigene Schrift kann ein bereits erlaubter lokaler WOFF2-Anhang verwendet werden. Das Plugin erweitert weder Uploadrechte noch erlaubte WordPress-Dateitypen. Bild- und Schriftlizenzen liegen beim Betreiber.

## Die Vorschau verwenden

Unter **Maintenance Studio → Layouts & Design** öffnet sich die geschützte Vorschau automatisch. Zuerst einen Entwurf speichern, wenn die Änderungen dauerhaft bleiben sollen. Die Freigabe benötigt eine Administrator-Sitzung, eine WordPress-Nonce und einen zusätzlichen Einmal-Token. Dieser muss innerhalb von zwei Minuten eingelöst werden. Ein neuer Editoraufruf ersetzt die bisherige Freigabe desselben Kontos.

| Änderung | In der geöffneten Vorschau |
|---|---|
| Texte wie Titel, Beschreibung und Hinweise | Unmittelbare Aktualisierung |
| Farben, Abstände, Ausrichtung, Schriftwahl und Container | Unmittelbare Aktualisierung |
| Logo und Hintergrundbild | Direkt nach Auswahl in der Mediathek sichtbar |
| Kontaktlinks, Countdown und Rechtstexte | Speichern und Vorschau neu öffnen |

Die Vorschau veröffentlicht nichts. Eine schon geöffnete Vorschau bleibt sichtbar, auch wenn ihr Token danach verbraucht ist. Das Abschalten der Vorschau verhindert neue Aufrufe, schließt aber keine bereits geladene Browseransicht.

## Lesbarkeit und Bedienung

Der Editor weist auf bestimmte Textkontraste unter 4,5:1 hin. Prüfe trotzdem die eigene Kombination aus Hintergrundbild, Text, Schriftgröße und Links. Animation ist standardmäßig aus und berücksichtigt reduzierte Bewegung. Dialoge und Links sind per Tastatur bedienbar; eine vollständige Screenreader-Abnahme bleibt eine eigene Prüfung.

Der Unterstützerhinweis „supported by Michael Gahn DESIGN“ kann ausgeblendet oder mit einem Ziel versehen werden. Ein leeres Linkziel ergibt normalen Text.
