# 404- und 403-Fehlerseiten

Unter **Einstellungen → Fehlerseiten** lassen sich „Seite nicht gefunden“ (404) und „Zugriff verweigert“ (403) getrennt gestalten. Beide sind nach Installation und Update ausgeschaltet. Die normalen WordPress- und Theme-Seiten bleiben sichtbar, bis Sie eine Seite aktivieren und die Einstellungen ausdrücklich veröffentlichen.

## Gestaltung

Für jeden Status stehen drei Vorlagen bereit: **Klar**, **Editorial** und **Dunkel**. Überschrift, Beschreibung und Buttontext können pro Seite geändert werden. Der Button führt zur Startseite. Für die beiden hellen Vorlagen lassen sich Hintergrund, Akzent und Textfarbe einstellen. Das Plugin lehnt Farbkombinationen mit zu geringem Kontrast ab. Die dunkle Vorlage verwendet feste, lesbare Farben. Auf der 404-Seite kann zusätzlich eine WordPress-Suche eingeblendet werden.

1. Eine Vorlage und passende Texte auswählen.
2. **Entwurf speichern**. Die Vorschau öffnet den gespeicherten Entwurf in einem neuen Tab und ist nur mit Administratorrechten und gültiger WordPress-Nonce erreichbar.
3. Erst wenn die Seite öffentlich angezeigt werden soll, **Eigene 404-Seite aktivieren** oder **Eigene 403-Seite aktivieren** anhaken und **Fehlerseiten veröffentlichen** ausdrücklich bestätigen.
4. Im privaten Browserfenster eine unbekannte URL aufrufen. Die Antwort muss HTTP 404 liefern und darf nicht von einem vorgeschalteten Cache stammen.

Die neue Fehlerseite ist eigenständig und lädt keine Theme-Hooks oder externen Schriften. Es werden keine Formulare mit personenbezogenen Daten gespeichert. Die optionale Suche verwendet die normale WordPress-Suche.

## Grenzen und Rückfall

Bei aktivem Wartungsmodus hat die Wartungsseite Vorrang. Die 403-Gestaltung betrifft nur WordPress-generierte HTML-403-Antworten im öffentlichen Bereich. Webserver, CDN, Sicherheitsplugins vor WordPress, Anmeldung, Administration und API können weiterhin ihre eigene 403-Seite ausgeben. Das schützt bestehende Zugangssperren.

Für den Rückfall beide Häkchen entfernen und **Fehlerseiten veröffentlichen**. Die ursprüngliche Theme-404 und die WordPress-403 erscheinen danach wieder. Es gibt keine Datenbankmigration; die zwei Optionen werden bei einer Deinstallation nur gelöscht, wenn die bestehende Option zur vollständigen Datenlöschung gesetzt ist. Vor Einsatz auf einer produktiven Website Dateien und Datenbank sichern und den Rückfall in einer Testumgebung prüfen.
