# GitHub-Updates

## Einmalige Einrichtung

Dieses Plugin wird als ZIP verteilt. Es ist nicht im WordPress.org-Verzeichnis veröffentlicht.
Unter Einstellungen → Maintenance Studio → Updates das eigene öffentliche Repository
im Format `MichaelGahnDESIGN/Repository` eintragen und die GitHub-Prüfung einschalten.
Ein Zugriffstoken ist nicht erforderlich und wird nicht gespeichert. Private Repositories
werden in dieser Version nicht unterstützt. Die Voreinstellung ist ausgeschaltet; `MichaelGahnDESIGN/MGD-Maintenance-WordPress` ist als eigene Quelle vorbelegt.

„Speichern und jetzt prüfen“ leert den Cache und prüft erneut. Neue Versionen erscheinen
unter Plugins beziehungsweise Dashboard → Aktualisierungen. WordPress übernimmt die
Installation. Automatische Updates können dort gesondert eingeschaltet werden;
das Plugin erzwingt sie nicht. Ausschalten der Prüfung löscht das bekannte Updateangebot.

## Release erstellen

1. Versionsnummer in Hauptdatei, Konstante, readme.txt, Paketbau und Änderungsprotokoll anheben.
2. Lokale Tests und Paketprüfung ausführen: `python3 scripts/build-package.py`.
3. Erst nach Freigabe Quellcode in das gewählte GitHub-Repository veröffentlichen.
4. Stabiles GitHub-Release mit Tag `v0.3.2` (oder `0.3.1`) anlegen.
5. `mg-maintenance-studio-0.3.2.zip` als Release-Datei anhängen. Nicht das automatisch
   erzeugte GitHub-Quellcodearchiv verwenden. Das ZIP enthält nur den Ordner
   `mg-maintenance-studio` mit der gleichnamigen Hauptdatei.
6. Auf einer separaten Testinstallation eine ältere Version auf die neue aktualisieren.
   Aktivierung, Wartungsstatus, Einstellungen und anonymen Zugriff danach prüfen.

## Sicherheitsgrenzen

Nur HTTPS und exakt passende Versions-, Tag-, Datei- und Repositorypfade werden
akzeptiert. Entwürfe, Vorabversionen, fremde Hosts und zusätzliche URL-Parameter
werden verworfen. Update URI verhindert die Zuordnung zu einem zufällig gleichnamigen
WordPress.org-Plugin. Berechtigungen `manage_options` und `update_plugins`, POST
und WordPress-Nonce schützen Änderungen an der Quelle.

Die Quelle ist auf Michaels GitHub-Konto begrenzt. Die API-Antwort wird auf 256 KiB
begrenzt; Metadatenabfragen folgen keinen Umleitungen. Erfolgreiche Ergebnisse werden
zwölf Stunden, Fehler fünfzehn Minuten zwischengespeichert. Ein API-Ausfall liefert
kein Updatepaket. Versionsvergleiche übernimmt WordPress. Die normale Updateprüfung
von WordPress muss funktionieren, damit das Angebot übernommen wird.

Release-ZIPs werden über WordPress und dessen HTTPS-Download bezogen. GitHubs
Downloadumleitungen auf seine Asset-Server sind dabei üblich. Es gibt bisher keine
zusätzliche kryptografische Paketsignatur; GitHub-Konto und Release-Rechte sind daher
Teil der Vertrauensgrenze. Die lokale SHA256-Datei dokumentiert das gebaute Paket,
ist aber keine unabhängige Signaturprüfung im Updater.

## Rückfall

Vor einem späteren Kundenupdate Dateien und Datenbank sichern. Bei Problemen die
vorherige geprüfte ZIP-Version wiederherstellen. Ein Dateirückfall verändert gespeicherte
Optionen nicht. In 0.3.1 gibt es keine Datenmigration. Der Wartungsstatus bleibt gespeichert.

## Technische Referenzen

Bedienmuster geprüft am eigenen Referenzplugin
[MGD AI Kennzeichnung WordPress](https://github.com/MichaelGahnDESIGN/MGD-AI-Kennzeichnung-WordPress),
Stand `0416ebc0bf4254267653544a1d8599b08c605a9b`.
Eigenständige Implementierung mit dem offiziellen
[Update-URI-Filter](https://developer.wordpress.org/reference/hooks/update_plugins_hostname/).
