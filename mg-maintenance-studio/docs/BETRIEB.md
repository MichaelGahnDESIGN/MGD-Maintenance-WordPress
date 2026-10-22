# Betrieb, Backup und Rückfall

## Strikte lokale Grenze dieses Projekts

Die Entwicklung und Tests verwenden ausschließlich eine neu angelegte WordPress-Installation, erreichbar über 127.0.0.1. Kundeninstallationen, vorhandene Datenbanken, Themes und Plugins wurden nicht verändert. Es existiert kein Git-Remote und es wurde nichts hochgeladen oder veröffentlicht. Eine lokale ZIP-Datei stellt keine Installationsfreigabe dar.

## Vor späterem Live-Einsatz getrennt bestätigen

1. Zielwebsite und autorisierte Person eindeutig festlegen.
2. Vollständiges Backup von Dateien und Datenbank bestätigen und an erlaubtem Ort ablegen.
3. Wiederherstellung dieses Backups in isolierter Umgebung nachweisen.
4. Testumgebung mit demselben Theme, Sicherheitsplugin, Cache, PHP und WordPress abnehmen.
5. Konkrete Live-Installation und anschließende Aktivierung ausdrücklich freigeben. Das sind getrennte Entscheidungen.

Bestehende Wartungsplugins nicht automatisch deaktivieren. Die Konfliktwarnung ist ein Prüfhinweis, keine Freigabe zum Austausch. Login, Mail, Cron und Sicherheitsmechanismen nicht umgehen oder eigenmächtig umstellen.

## Rückfall im Plugin

Ein vorheriger Entwurf bzw. veröffentlichter Stand ist über die bestätigten Wiederherstellungsaktionen erreichbar. Der veröffentlichte Rückfall wirkt bei aktivem Wartungsmodus sofort. Diese Funktion ist kein vollständiges Backup: Sie enthält keine Medien, Benutzer oder anderen WordPress-Daten. Status wird dabei nicht geändert. Eine Wiederherstellung verbraucht den gespeicherten Stand nicht; es gibt keine unbegrenzte Versionshistorie.

Im Fehlerfall Wartungsmodus im Backend bewusst deaktivieren. Bei Plugin-Deaktivierung wird dessen Ausgabe beendet, der gespeicherte Status bleibt erhalten. Reaktivieren kann daher eine zuvor bewusst eingeschaltete Wartungsseite wieder ausgeben. Ist das Backend nicht erreichbar, kann eine autorisierte Person den Plugin-Ordner umbenennen; dies umgeht keine Anmeldung und erfordert eigene Serverberechtigung. Anschließend Cache leeren und anonym testen. Kein automatischer Notfall-Schalter oder Timer.

## Deinstallation

Standardmäßig bleiben Einstellungen bei WordPress-Deinstallation erhalten. Für endgültige Löschung vorher „Bei Deinstallation Plugin-Einstellungen löschen“ im Entwurf aktivieren und speichern. Dann deaktivieren und löschen. Die Löschroutine entfernt pro Website die fünf eigenen Optionswerte; bei Multisite wird die jeweilige Löschentscheidung berücksichtigt. Vorschau-Metadaten werden immer entfernt. Medien und andere Plugins bleiben erhalten.

## Cache und Infrastruktur

Nach Aktivieren, Deaktivieren, Veröffentlichung und Rückfall alle relevanten Caches kontrolliert leeren. Anonym in einem frischen Browser prüfen: tatsächlicher HTTP-Status, Header, Inhalt, Login und Rechtslinks. IP-Freigaben zusätzlich aus einem nicht freigegebenen Netz testen. CDN/WAF kann Header und Client-IP beeinflussen. Keine pauschalen Proxy-Netze eintragen.

Die lokale Testdatenbank ist flüchtig (Docker-tmpfs). Beim Entfernen der eigens angelegten Testcontainer geht nur deren Testdatenbestand verloren. Quellcode, Git-Historie und Testberichte bleiben im Projekt. Es wurde kein Backup einer Kundenwebsite benötigt oder erstellt, weil keine vorhandene Website verändert wurde. Für Live-Einsatz ist ein verifiziertes vollständiges Backup Pflicht.
