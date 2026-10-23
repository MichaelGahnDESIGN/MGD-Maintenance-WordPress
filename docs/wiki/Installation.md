# Installation und erste Schritte

[Zur Übersicht](Home.md) · [Weiter: Design und Vorschau](Design-und-Vorschau.md)

## Vorbereiten

Prüfe WordPress- und PHP-Version unter **Werkzeuge → Website-Zustand → Bericht**. Benötigt werden WordPress 6.5+ und PHP 8.1+. Sichere vor der produktiven Installation Dateien und Datenbank. Eine Testwebsite mit denselben wichtigen Plugins, demselben Theme und Cache macht Konflikte früh sichtbar.

Wenn bereits ein Wartungsplugin läuft, plane den Wechsel bewusst. MG Maintenance Studio zeigt für einige bekannte Namensmuster einen Konflikthinweis, deaktiviert aber keine anderen Plugins. Diese Erkennung ist nicht vollständig.

## Das richtige ZIP installieren

1. Das [neueste Release](https://github.com/MichaelGahnDESIGN/MGD-Maintenance-WordPress/releases/latest) öffnen.
2. Unter **Assets** das Plugin-ZIP laden, zum Beispiel `mg-maintenance-studio-0.3.2.zip`.
3. In WordPress **Plugins → Installieren → Plugin hochladen** wählen.
4. ZIP hochladen, installieren und aktivieren.
5. **Einstellungen → Maintenance Studio** öffnen.

Die automatisch angebotenen GitHub-Dateien „Source code“ enthalten das Entwicklungsprojekt und sind nicht das vorgesehene Installationspaket. Das richtige ZIP enthält direkt den Ordner `mg-maintenance-studio/` mit der gleichnamigen PHP-Hauptdatei.

## Erste Einrichtung

Im Designbereich die eigenen Texte und gegebenenfalls Medien einrichten. Rechte und IP-Freigaben prüfen. Danach den Entwurf speichern und die Vorschau öffnen. Eigene Rechtstexte sind selbst einzutragen; es werden keine passenden Rechtstexte für ein Unternehmen automatisch erzeugt.

Erst die bestätigte Veröffentlichung übernimmt diese Werte in den öffentlichen Wartungsstand. Die Aktivierung des Wartungsmodus ist eine eigene bestätigte Aktion in der Übersicht.

**Erstaktivierung:** Wartung bleibt aus. **Reaktivierung:** Der zuvor gespeicherte Wartungsstatus gilt weiter. Prüfe deshalb vor dem Reaktivieren, welcher Status zuletzt eingestellt war.

## Besuchersicht abnehmen

Ein angemeldeter Administrator sieht wegen seiner Freigabe möglicherweise die normale Website. Deshalb auch ausgeloggt oder in einem privaten Fenster prüfen. Die normale Wartungsantwort verwendet HTTP 503; zusätzlich sollten Kontakt und Rechtslinks, kleine Bildschirme und die Cache-Ausgabe geprüft werden.

Freigaben nur für benötigte Personen und Netze vergeben. Erst danach auf der echten Website einsetzen. [Betrieb und Rückfall](Betrieb-und-Wiederherstellung.md) beschreibt den Ablauf bei Problemen.
