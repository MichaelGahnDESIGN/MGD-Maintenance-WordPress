=== MG Maintenance Studio ===
Requires at least: 6.5
Tested up to: 7.1.3
Requires PHP: 8.1
Stable tag: 0.4.0
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.html

Lokale Entwicklungsversion eines eigenständigen Wartungsplugins. Noch keine Freigabe für Kundenwebsites.

== Beschreibung ==
Getrennter Entwurf, Veröffentlichung und Wartungsstatus. Vier lokale Designvorlagen,
geschützte Vorschau, eigene Rechtstexte, Kontaktlinks, Benutzer- und IP-Freigaben.
Eigene 404- und 403-Fehlerseiten mit je drei Vorlagen, Entwurf und Veröffentlichung.
Keine externen Ressourcen auf der Wartungsseite, kein Tracking, kein Kontaktformular.
Optionale GitHub-Updateprüfung nach ausdrücklicher Einrichtung im Backend.

== Installation ==
Nur in einer neuen lokalen Testinstallation aktivieren. Maintenance Studio > Übersicht.
Die erste Plugin-Aktivierung schaltet den Wartungsmodus nicht ein.
Vollständige deutsche Dokumentation im Unterordner docs/.

== Hinweise ==
Keine Firewall. Cache, CDN, direkte Dateien und XML-RPC benötigen eigene Prüfung.
Vor späterem Live-Einsatz: Backup, Wiederherstellung, Testumgebung und Freigabe getrennt bestätigen.
Bei Reaktivierung bleibt der zuvor bewusst gesetzte Wartungsstatus erhalten.

== Changelog ==
= 0.4.0 =
Dashboard, Hauptmenü mit Untermenüs, vier Wartungsseiten-Layouts und direkte Live-Vorschau.

= 0.3.2 =
Separates Untermenü für 404- und 403-Fehlerseiten mit Vorlagen, Texten, Farben und Vorschau.

= 0.3.0 =
GitHub-Updates, eigenes lokales Icon, Plugin-Details und Updateverwaltung.

= 0.2.0 =
Ruhigere Wartungsseite und WordPress-nahe Administration; ECC-Designprüfung.

= 0.1.0 =
Lokale Erstimplementierung. Details in CHANGELOG.md und docs/TESTBERICHT.md.
