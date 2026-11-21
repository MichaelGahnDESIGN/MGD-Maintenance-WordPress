# Design: Eigenständige Szenen für die Wartungsvorlagen (0.6.0)

## Warum

Michael: Die fünf Vorlagen sehen sich zu ähnlich. Sie sollen unterschiedlicher, besser und eindrucksvoller werden.
Sie sollen **mit und ohne eigenes Bild** gut aussehen. Den Weg hat Michael mit „sei kreativ“ an mich übergeben.

## Ziele

- Jede der fünf Vorlagen bekommt einen eigenen Aufbau („Szene“), nicht nur andere Farben.
- Jede Szene hat einen eigenen Look ohne Bild (Verlauf, Formen, Typografie).
- Neuer Regler „Bildwirkung“ (dezent / ausgewogen / kräftig).

## Nicht-Ziele / No-Gos

- Bestehende Seiten dürfen sich beim Update nicht verändern: Standard-Layout bleibt `editorial`, Standard-Bildwirkung bleibt `subtle` (wie bisher 12 % Deckkraft).
- Keine externen Schriften, Skripte oder Bilder (CSP bleibt `img-src 'self' data:`).
- Keine Animation bei „Bewegung reduzieren“.
- Kein neues Seitentemplate pro Vorlage (Weg C verworfen).

## Ansätze

- A: nur Optik der 4 Layouts auffrischen. Verworfen, Aufbau bleibt gleich.
- **B: fünf neue Szenen-Layouts + Bildwirkung, alte Layouts bleiben wählbar. Gewählt.**
- C: eigenes Template je Vorlage. Verworfen, zu viel Pflege, Live-Vorschau müsste neu gebaut werden.

## Szenen

| Vorlage | Layout-Schlüssel | Mit Bild | Ohne Bild |
|---|---|---|---|
| Elegant Gold | `cinema` | Vollbild, Verlauf von unten, große Serifenschrift unten links | dunkler Goldschimmer, feine Linien |
| Ruhiges Atelier | `atelier` | Bild füllt linke Hälfte, Text rechts | Musterfläche links mit großem Kreis |
| Minimalistisch Hell | `swiss` | riesige Überschrift, Bild als versetzt gerahmtes Rechteck | großer Linienkreis, Raster |
| Nachtstudio | `glass` | Bild mit dunklem Schleier, Glas-Karte | langsam wandernde Leuchtflächen |
| Verspielter Moment | `bloom` | weiche Karte auf Bild | bunte schwebende Farbkleckse |

## Technik

- Bild bleibt das vorhandene `.background-image`; jede Szene platziert es per CSS.
- Body-Klassen `has-image` / `no-image` und `mgms-image-{subtle|balanced|vivid}`.
- Ein Deko-Element `.mgms-decor` (aria-hidden) für Formen ohne Bild.
- Live-Vorschau (`preview.js`) kennt die neuen Layouts, die Bildwirkung und schaltet `has-image` um.
