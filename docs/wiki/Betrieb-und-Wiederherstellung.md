# Betrieb, Wiederherstellung und Deinstallation

[Zur Übersicht](Home.md) · [Fehlerbehebung](Fehlerbehebung.md)

## Wartungsarbeiten beginnen und beenden

Vor Beginn den veröffentlichten Inhalt und die benötigten Freigaben prüfen. Wartung bewusst aktivieren, gegebenenfalls Caches leeren und die Gastansicht kontrollieren. Ein angemeldeter Administrator allein ist kein ausreichender Nachweis.

Zum Beenden den Wartungsmodus in der Übersicht deaktivieren und bestätigen. Danach die normale Website ohne Anmeldung prüfen. CDN, Servercache und Browsercache können ältere Antworten weiterliefern. Der Countdown schaltet nichts automatisch aus.

## Vorherigen Stand zurückholen

Das Plugin hält jeweils einen vorherigen Entwurf und einen vorherigen veröffentlichten Stand vor. Die entsprechenden Wiederherstellungsaktionen sind bestätigt auszuführen. Ein wiederhergestellter veröffentlichter Stand wirkt bei aktiver Wartung sofort; der Wartungsschalter selbst bleibt unverändert.

Es handelt sich nicht um eine vollständige Versionshistorie oder ein Website-Backup. Medien, Benutzerkonten, Theme-Dateien und andere Plugins sind darin nicht enthalten. Für umfangreiche Änderungen oder Updates weiterhin Dateien und Datenbank sichern und den Rückweg kennen.

## Plugin deaktivieren oder zurücksetzen

Bei Deaktivierung endet die eigene Wartungsausgabe. Der gespeicherte Wartungsstatus bleibt erhalten und gilt nach Reaktivierung wieder. Wenn das Backend wegen eines Pluginfehlers nicht erreichbar ist, kann eine dazu berechtigte Person den Pluginordner über den regulären Serverzugang umbenennen. Das ist ein Notfallweg mit derselben Wirkung wie eine Deaktivierung, kein Ersatz für Zugriffsschutz.

Ein Rückfall auf ein vorheriges Release sollte über dessen geprüftes Plugin-ZIP erfolgen. Version 0.3.1 enthält keine Datenbankmigration. Bereits gespeicherte Einstellungen werden durch bloßen Dateiaustausch nicht entfernt.

## Daten bewusst löschen

Die Aktion **Alte Stände und Vorschau-Freigaben löschen** entfernt die beiden vorherigen Stände und Vorschau-Metadaten. Aktueller Entwurf, Veröffentlichung und Wartungsstatus bleiben erhalten.

Bei normaler Deinstallation bleiben Einstellungen standardmäßig gespeichert. Für vollständige Entfernung zuerst **Bei Deinstallation Plugin-Einstellungen löschen** im Entwurf einschalten und speichern. Danach das Plugin deaktivieren und über WordPress löschen.

Die Löschoption entfernt sechs eigene Optionswerte einschließlich Updatekonfiguration. Der Release-Cache und Vorschau-Metadaten werden bei Deinstallation immer entfernt. Mediathek-Dateien bleiben erhalten. In Multisite wird die jeweilige gespeicherte Löschentscheidung pro Website berücksichtigt; ein realer Multisite-Betriebstest steht noch aus.

## Kundeneinsatz

Für jede konkrete Website sind Backup, Wiederherstellung, PHP-/WordPress-Version, Theme, Sicherheitsplugin, Cache, Webhooks und externe Dienste zu prüfen. Die lokalen Projekttests ersetzen diese Abnahme nicht. Bestehende Wartungs- und Sicherheitsmechanismen nicht versehentlich abschalten.
