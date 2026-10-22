# MG Maintenance Studio · Handbuch

Eine Wartungsseite soll zur Website passen und ihren Zweck klar erfüllen. Dieses Handbuch führt von der ersten Installation bis zum sicheren Betrieb. Es beschreibt den Funktionsstand **0.3.1**.

[Plugin herunterladen](https://github.com/MichaelGahnDESIGN/MGD-Maintenance-WordPress/releases/latest) · [Projektübersicht](../../README.md) · [Sicherheitsprüfung](../SECURITY-AUDIT.md)

## Hier beginnen

| Aufgabe | Seite |
|---|---|
| Zum ersten Mal einrichten | [Installation](Installation.md) |
| Texte, Farben und Medien gestalten | [Design und Vorschau](Design-und-Vorschau.md) |
| Zugriff für Personen und Netze festlegen | [Zugriffe](Zugriffe.md) |
| Rechtstexte und Kontakt zeigen | [Rechtstexte und Kontakt](Rechtstexte-und-Kontakt.md) |
| GitHub-Updates einrichten | [Updates und Releases](Updates-und-Releases.md) |
| Wartung beenden oder Einstellungen zurückholen | [Betrieb und Wiederherstellung](Betrieb-und-Wiederherstellung.md) |
| Etwas funktioniert anders als erwartet | [Fehlerbehebung](Fehlerbehebung.md) |
| Schutzmaßnahmen verstehen | [Sicherheit](Sicherheit.md) |
| Speicherung und externe Verbindungen nachvollziehen | [Datenschutz](Datenschutz.md) |
| Entwickeln und prüfen | [Entwicklung und Tests](Entwicklung-und-Tests.md) |

## Der wichtigste Ablauf

**Entwurf speichern → Vorschau prüfen → Einstellungen veröffentlichen → Wartungsmodus bewusst einschalten.**

Das Speichern allein verändert die öffentliche Ausgabe nicht. Die Veröffentlichung verändert die Wartungsseite, wenn Wartung bereits aktiv ist. Der Schalter entscheidet, ob Besucher diese Wartungsseite oder die normale Website erhalten.

## Voraussetzungen und Grenzen

WordPress ab 6.5, PHP ab 8.1 und ein Konto mit `manage_options` sind erforderlich. Updates benötigen zusätzlich `update_plugins`. Die Oberfläche ist deutsch. Netzwerkaktivierung wird abgewiesen; Multisite ist je Website einzurichten und in einer passenden Testumgebung abzunehmen.

Das Plugin ist öffentlich verfügbar, aber nicht pauschal für jede Hosting-, Cache- oder Theme-Kombination abgenommen. Direkte Dateien und Serverdienste benötigen eigene Schutzmaßnahmen. Ein Wartungsmodus ersetzt keine Firewall.
