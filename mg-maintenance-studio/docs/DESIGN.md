# Gestaltungsüberarbeitung 0.2.0

## Dashboard und Layouts 0.5.0

Die Administration erhält eine eigene Navigation und eine klare Reihenfolge: Status ansehen, Wartungsseite bearbeiten, Layout gestalten, Zugriffe prüfen, Fehlerseiten konfigurieren und Updates verwalten. Die Übersicht verwendet dunkles Grün und warme helle Flächen; der eigentliche Editor bleibt gut lesbar und an WordPress-Bedienelemente angebunden. Status und Veröffentlichungsaktionen sind getrennt.

Vier Seitenlayouts teilen dieselben validierten Inhalts- und Farbwerte: Editorial, zentriert, geteilt und Poster. Die vier Vorlagen zeigen jeweils einen anderen Ausgangspunkt. Auch ohne Logo und Hintergrundbild bleibt die Seite vollständig. Bei alten Installationen bleibt Editorial der Standard. Die geschützte Vorschau öffnet in den Editor-Untermenüs automatisch und zeigt Änderungen an Text, Farbe, Layout sowie neu ausgewähltem Logo und Hintergrund sofort. Desktop und Mobil können im Editor umgeschaltet werden. Externe Grafiken oder Schriften werden nicht nachgeladen.

Lokale Browserabnahme: Dashboard und Designeditor bei 1440 × 900 sowie Dashboard bei 390 × 844 ohne horizontale Überbreite; Vorlagenwechsel und Medienvorschau im isolierten WordPress geprüft. Screenshots und ECC-Prüfdatensatz liegen im lokalen, nicht veröffentlichten Ordner `artifacts/`.

## Gestaltungsauftrag nach ECC Frontend Design Direction

Besucher müssen erkennen, dass die Website vorübergehend nicht erreichbar ist, und Kontakt oder Rechtstexte finden. Die Wartungsseite wird deshalb wie eine kurze redaktionelle Mitteilung aufgebaut: Marke, Anlass, Titel und Erklärung, anschließend Kontakt und Rechtliches. Keine Schmucksterne, Werbeslogans, großen runden Karten oder automatischen Animationen als Standard.

Die lokale Ausgangsansicht zeigte eine übergroße zentrierte Karte mit Stern, Gold auf Grün und austauschbarer Atelier-Sprache. Diese Elemente hatten keinen Bezug zur Aufgabe. Die neue Fassung verwendet einen hellen Papiergrund, einen sparsamen Goldton und eine linksbündige Satzordnung. Atelier und Minimalistisch erhalten abgestimmte Alternativen. Bilder sind optional, für eine sachliche Statusmitteilung nicht notwendig.

Administratoren sollen Status erkennen, Inhalte bearbeiten und die Auswirkungen ihrer Aktionen verstehen. Das Backend verwendet deshalb WordPress-nahe Farben, kompakte Zeilen statt Kennzahlenkarten, schlichte Tabs und native Buttons. Die Aktion zum Einschalten bleibt von Entwurf und Veröffentlichung getrennt.

Prüfbare Vorgaben: 390×844, 768×1024 und 1440×900; keine horizontale Überbreite; lange Texte umbrechen; Kontrast nach WCAG AA automatisch prüfen; Fokus und Tabs per Tastatur; Rechtstextdialog und Anker ohne JavaScript. Kein neuer externer Dienst und keine neue Laufzeitabhängigkeit.

Zustände: Die serverseitige Seite erscheint ohne asynchrones Laden. Fehlende Rechtstexte werden im Backend benannt. Validierungsfehler dürfen nichts veröffentlichen. Erfolg wird nach Speichern bestätigt. Vorschau benötigt weiterhin Administratorberechtigung; abgelaufene Freigaben geben einen erklärenden Fehler aus. Teilweise konfigurierte Inhalte (ohne Bild, ohne Kontakt) müssen ohne leere Karten funktionieren.

Vorher-Bilder liegen lokal unter artifacts/redesign/before. Neue Bilder und ECC-Prüfdatensatz werden unter artifacts/redesign abgelegt. Der historische Stand ist zusätzlich in Git und im Paket 0.1.0 erhalten. Bestehende gespeicherte Einstellungen werden nicht migriert; neue Standardwerte gelten für neue Installationen, Vorlagen für bewusste Auswahl.

## Erweiterung 0.3.0: Verwaltung und Produktdarstellung

Referenz: MichaelGahnDESIGN/MGD-AI-Kennzeichnung-WordPress, Stand 0416ebc.
Übernommen wird das Bedienmuster: native WordPress-Aktualisierungen, lokale
Produktgrafik in der Pluginliste, Detailfenster und ruhige weiße Einstellungsflächen.
Die Umsetzung ist eigenständig; das Symbol zeigt ein Browserfenster mit Pausezeichen.
Keine Animation, keine externe Grafik und keine Änderung am Besucherdesign.
Der neue Reiter Updates trennt Versionspflege vom Entwurf und Wartungsstatus.
Prüfgrößen: 1440 × 900, 768 × 1024, 390 × 844. Zustände: unkonfiguriert,
Prüfung aus, gültiges Release, kein neueres Release, Netzwerkfehler.
