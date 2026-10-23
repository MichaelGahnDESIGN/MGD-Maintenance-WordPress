# GitHub-Updates und Releases

[Zur Übersicht](Home.md) · [Betrieb und Wiederherstellung](Betrieb-und-Wiederherstellung.md)

## Updates einschalten

1. **Maintenance Studio → Updates** öffnen.
2. Die vorbelegte Quelle `MichaelGahnDESIGN/MGD-Maintenance-WordPress` prüfen.
3. Die GitHub-Prüfung einschalten und **Speichern und jetzt prüfen** wählen.
4. Neuere Versionen anschließend unter **Plugins** oder **Dashboard → Aktualisierungen** installieren.

Die Einstellung ist zunächst aus. Ein GitHub-Konto oder Zugriffstoken ist auf der Website nicht erforderlich. Private Repositories werden nicht unterstützt. Automatische Installationen werden separat über WordPress gesteuert.

Erfolgreiche Prüfungen werden zwölf Stunden zwischengespeichert, Fehler fünfzehn Minuten. „Speichern und jetzt prüfen“ leert diesen Cache. Ein API-Ausfall erzeugt kein neues Paketangebot; die vorhandene Installation bleibt nutzbar.

## Was als Update akzeptiert wird

Die Quelle muss zu Michaels GitHub-Konto gehören. Stabile Tags haben das Format `v0.5.0` oder `0.5.0`. Das zugehörige Asset muss exakt `mg-maintenance-studio-0.5.0.zip` heißen und zur Version, zum Repository und zum Tag passen. Entwürfe, Vorabversionen, fremde Downloadhosts und zusätzliche URL-Parameter werden nicht als passende Metadaten akzeptiert.

WordPress übernimmt Download und Installation. Die API-Metadatenabfrage ist auf 256 KiB begrenzt und folgt keinen Umleitungen. Der eigentliche HTTPS-Paketdownload kann GitHubs übliche Asset-Umleitungen verwenden.

## Vertrauen und Grenzen

Das Plugin speichert keine GitHub-Zugangsdaten. Es prüft bislang aber keine unabhängige Paketsignatur und gleicht beim Installieren auch keinen eigenen SHA-256-Wert ab. Eine veröffentlichte Prüfsumme hilft beim manuellen Vergleich; sie schützt nicht unabhängig vor einem kompromittierten Release-Konto.

GitHub-Konto und Release-Rechte mit geeigneter Mehrfaktor-Anmeldung und möglichst wenigen berechtigten Personen absichern. Fehler oder Kompromittierungen vor einem Update prüfen. [Sicherheitskonzept](Sicherheit.md)

## Eine neue Version veröffentlichen

Versionsnummern in Plugin-Hauptdatei, Konstante, WordPress-readme und Paketbau synchron halten. Changelog und Tests aktualisieren. Danach `python3 scripts/build-package.py` ausführen und das ZIP auf Version, Inhalt und Secrets prüfen.

Den geprüften Commit veröffentlichen, dessen CI abwarten und das passend benannte ZIP an ein stabiles Release hängen. Alte Releases nicht überschreiben. Das automatisch generierte Quellcodearchiv ist kein Ersatz für das Installationspaket.

Auf einer isolierten Website eine ältere Version aktualisieren und insbesondere Pluginpfad, veröffentlichte Einstellungen, Wartungsstatus und anonymen Zugriff prüfen. Das Paket enthält keine Laufzeitabhängigkeiten aus Composer oder npm.

## Wenn das Update nicht erscheint

Eine bereits installierte identische Version ist kein Update. Bei einer neueren Version zuerst Quelle, aktivierte Prüfung, stabiles Release und exakten Assetnamen kontrollieren. Dann manuell neu prüfen. Bei Hosting-Firewall oder GitHub-Limit später erneut versuchen; keine Tokens oder Zugangsdaten in URL-Felder eintragen.
