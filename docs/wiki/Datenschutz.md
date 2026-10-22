# Daten und externe Verbindungen

[Zur Übersicht](Home.md) · [Sicherheit](Sicherheit.md)

## Was gespeichert wird

| Daten | Ort und Aufbewahrung |
|---|---|
| Design, Texte, Kontaktangaben, Rechtstexte, Rollen und IP-Freigaben | WordPress-Optionen für Entwurf und Veröffentlichung; bis zur Änderung bzw. bewussten Löschung |
| Je ein vorheriger Entwurf und veröffentlichter Stand | Eigene Optionen; durch nächste Speicherung/Veröffentlichung ersetzt oder bewusst gelöscht |
| Wartungsstatus und Updatekonfiguration | Eigene WordPress-Optionen |
| Vorschau-Token-Hash, Ablaufzeit und Sitzungs-Hash | Benutzermetadaten; bei Verwendung gelöscht, sonst ersetzt oder bei Bereinigung entfernt |
| GitHub-Release-Metadaten | Transient-Cache; zwölf Stunden bei Erfolg, fünfzehn Minuten bei Fehlern |
| Bilder und Schriftdateien | Normale WordPress-Mediathek; das Plugin speichert nur Anhang-IDs |

Es gibt keine eigene Datenbanktabelle, kein Besucherprofil und kein personenbezogenes Änderungsprotokoll. Besucher-IPs werden für die Freigabe im Arbeitsspeicher verglichen und vom Plugin nicht geloggt. Eingetragene Freigabe-IPs können dennoch personenbezogene Daten sein.

Abgelaufene Vorschau-Metadaten erlauben keinen Zugriff. Es gibt keinen eigenen Cron-Löschlauf; ungenutzte Einträge werden beim nächsten Erzeugen überschrieben oder bei Bereinigung, Abschalten der Vorschau, Deaktivierung und Deinstallation gelöscht.

## Was die Wartungsseite lädt

Die eigenständig gerenderte Wartungsseite nutzt lokale CSS-/JavaScript-Dateien, Icons und freigegebene Medien gleicher Herkunft. Sie enthält keine Analyse-, Werbe-, Font- oder Bibliotheks-CDNs und setzt keine eigenen Besucher-Cookies.

WordPress-Anmeldung, Hoster, CDN und andere Plugins können unabhängig davon Daten verarbeiten. Als Ausnahme freigegebene normale WordPress-Rechtsseiten verwenden das Theme mit dessen eigenen Ressourcen. Externe Links führen erst beim Aufrufen zu ihrem Ziel.

## Optionale GitHub-Prüfung

Erst nach Einschalten verbindet sich der Server mit `api.github.com`. GitHub erhält technisch die Server-IP, das angefragte Repository und die Pluginversion im User-Agent. Seiteninhalte, Besucher-IP-Adressen, Konten und Zugangsdaten werden durch diese Plugin-Abfrage nicht übertragen.

Downloads verwenden GitHub und dessen Asset-Infrastruktur. Eine Verarbeitung außerhalb der EU kann stattfinden. Die Updateprüfung kann wieder ausgeschaltet werden; die Wartungsseite benötigt diese Verbindung nicht.

## Entfernen und Aufbewahrung

Sensible oder nicht mehr benötigte Einträge im Entwurf entfernen, speichern und veröffentlichen. Danach die vorherigen Stände löschen, damit alte Kontakt- oder IP-Angaben nicht dort verbleiben. Website-Backups haben davon unabhängige Aufbewahrungsfristen.

POST-Body-Aufzeichnungen in Debugging- oder Sicherheitssoftware können Vorschau-Tokens enthalten und sollten vermieden werden. Keine Debug-Ausgaben oder Testzugänge veröffentlichen. Für die vollständige Deinstallation gilt [Betrieb und Wiederherstellung](Betrieb-und-Wiederherstellung.md).

Dies beschreibt die technischen Datenflüsse. Es ist kein fertiger Datenschutzerklärungstext und ersetzt keine Prüfung des konkreten Websitebetriebs.
