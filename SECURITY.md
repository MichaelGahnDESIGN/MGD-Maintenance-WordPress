# Sicherheitsprobleme melden

Sicherheitsmeldungen zu MG Maintenance Studio bitte über [GitHubs private Schwachstellenmeldung](https://github.com/MichaelGahnDESIGN/MGD-Maintenance-WordPress/security/advisories/new) einreichen. So werden technische Details nicht vor einer möglichen Korrektur öffentlich sichtbar.

Keine echten Passwörter, Zugriffstokens, Session-Cookies, privaten Kundendaten oder vollständigen Datenbankexporte anhängen. Für einen Nachweis synthetische Daten und eine isolierte Testinstallation verwenden. Falls versehentlich ein echtes Secret veröffentlicht wurde, muss es beim zuständigen Dienst widerrufen oder rotiert werden; bloßes Löschen aus der aktuellen Datei reicht nicht.

## Was eine hilfreiche Meldung enthält

- Betroffene Plugin-, WordPress- und PHP-Version.
- Voraussetzungen und benötigte Rechte, etwa anonymer Besucher oder angemeldetes Konto.
- Reproduzierbare Schritte mit harmlosen Beispieldaten.
- Erwartetes und tatsächliches Verhalten sowie mögliche Auswirkungen.
- Falls möglich ein kleiner Test oder eine Beschreibung der betroffenen Datei.

Bitte keine produktiven fremden Websites prüfen. Keine zeitliche Reaktions- oder Vergütungszusage: Meldungen werden vom Projektbetreiber bewertet.

## Versionsstand

Die aktuelle veröffentlichte Linie ist **0.3.x**, zuletzt **0.3.1**. Sicherheitskorrekturen werden für den aktuellen Stand vorbereitet; eine gesonderte Pflege älterer Versionen wird nicht zugesagt. Vor einem Update passende Sicherungen erstellen und die konkrete Website prüfen.

## Prüfungen und Grenzen

Der [Sicherheitsbericht](docs/SECURITY-AUDIT.md) nennt geprüften Stand, Verfahren, Ergebnisse und Einschränkungen. Das [Sicherheitskapitel](docs/wiki/Sicherheit.md) beschreibt Berechtigungen, Vorschau und Vertrauensgrenzen.

Ein fehlerfreier Scan beweist keine vollständige Sicherheit. Wartungsmodus, Suchmaschinenhinweise und IP-Freigaben ersetzen keine Serverabsicherung. GitHub-Release-Rechte und WordPress-Administratorkonten gehören zur Vertrauensbasis; der Updater prüft derzeit keine unabhängige Paketsignatur.
