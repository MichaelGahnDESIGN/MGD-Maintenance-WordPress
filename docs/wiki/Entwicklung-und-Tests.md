# Entwicklung und Tests

[Zur Übersicht](Home.md) · [Tests reproduzieren](../../tests/README.md)

## Aufbau

Das installierbare Plugin liegt vollständig in `mg-maintenance-studio/`. Es hat keine mitzuliefernden Composer- oder npm-Laufzeitpakete.

| Klasse | Zuständigkeit |
|---|---|
| `Schema` | Felder und sichere Standardwerte |
| `Settings` | Daten lesen, validieren und Medienherkunft prüfen |
| `Admin` | Editor, Speichern, Veröffentlichung, Status und Wiederherstellung |
| `Access` / `IP` | Benutzerregeln, Netzwerkvergleich und Proxy-Auswertung |
| `Preview` | Einmalige sitzungsgebundene Vorschau |
| `Renderer` | HTTP-Header und eigenständige Wartungsausgabe |
| `Updates` | GitHub-Metadaten, WordPress-Updateangebot und Updateeinstellungen |
| `Presentation` | Plugin-Icon und Detaildarstellung |
| `Plugin` | Registrierung und WordPress-Lebenszyklus |

Die Wartungsansicht ruft keine Theme-Hooks `wp_head()` oder `wp_footer()` auf. Das verhindert automatische Fremdressourcen, bedeutet aber auch, dass ausschließlich dort eingebundene Consent-/Sicherheitswerkzeuge nicht in dieser Ansicht erscheinen.

## Datenmodell

Es gibt keine eigene Tabelle und in 0.3.1 keine Datenbankmigration. WordPress-Optionen speichern Entwurf, Veröffentlichung, Status, zwei vorherige Stände und Updatekonfiguration. Vorschau-Freigaben liegen in Benutzermetadaten, Release-Informationen in einem Transient. Neue Felder erhalten beim Lesen Standardwerte. Spätere Datenmodelländerungen brauchen eine eigene Migrations- und Rückfallplanung.

## Lokal prüfen

```bash
php tests/login-errors.php
python3 scripts/check-docs.py
find mg-maintenance-studio -name '*.php' -exec php -l {} \;
node --check mg-maintenance-studio/assets/admin.js
node --check mg-maintenance-studio/assets/frontend.js
node --check mg-maintenance-studio/assets/preview.js
python3 scripts/build-package.py
```

Für WordPress Coding Standards ist `phpcs.xml.dist` maßgeblich. Die dokumentierten Ausnahmen sind Teil dieses Profils; ein Bestehen bedeutet nicht automatisch die Einhaltung jeder verfügbaren WordPress-Regel für jede Dateitypgruppe.

Die Integrationstests benötigen eine frische, isolierte WordPress-Installation und `MGMS_TEST=isolated-local`. Sie verändern Testkonten, Optionen und Pluginstatus. Niemals auf einer existierenden Kundenwebsite ausführen. Vollständige Schritte stehen in [tests/README.md](../../tests/README.md).

## Sicherheitsprüfung wiederholen

Gitleaks prüft die Git-Historie mit `--log-opts=--all`. Den aktuellen freizugebenden Dateistand getrennt exportieren und mit `gitleaks dir` prüfen. Auch das tatsächlich heruntergeladene Release-ZIP entpacken und scannen. Berichte ausschließlich mit `--redact` erzeugen; mögliche Geheimnisse nicht ins Terminal, in Issues oder in die Dokumentation kopieren.

Die CI ergänzt einen Secret-Scan und eine Prüfung lokaler Dokumentationslinks. Die vollständigen Browser-/WordPress-Tests bleiben eigenständige lokale Abnahmen und werden nicht allein durch den grünen CI-Status belegt.

## Wiki pflegen

Die führenden Handbuchdateien liegen unter `docs/wiki/`. `scripts/export-wiki.py` exportiert sie mit für GitHub-Wikis passenden Links in einen angegebenen Zielordner. Die erste Seite eines separaten GitHub-Wikis muss vorher im angemeldeten GitHub-Browser angelegt werden. Danach kann das Wiki-Repository normal per Git aktualisiert werden.

Vor Veröffentlichung fachliche Angaben gegen den Code, Links gegen vorhandene Dateien und Testergebnisse gegen echte Ausgaben prüfen. Die derzeitigen Grenzen stehen im [Sicherheitsaudit](../SECURITY-AUDIT.md) und im [historischen Testbericht](../../mg-maintenance-studio/docs/TESTBERICHT.md).
