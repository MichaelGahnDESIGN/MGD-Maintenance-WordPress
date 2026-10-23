# Lokale Tests reproduzieren

Voraussetzungen: Docker, PHP 8.1+, Node.js, Playwright mit installiertem Chrome, axe-core und Composer für WPCS/PHPCompatibilityWP. Sämtliche Testskripte ausschließlich in einer neuen wegwerfbaren Installation nutzen. Sie verändern Testkonten, Optionen und Pluginstatus. Keine Verbindung zu einer vorhandenen WordPress-Datenbank herstellen.

1. Unter `.local/` eine Datei `runtime.env` mit zufällig erzeugtem `MGMS_DB_PASSWORD` anlegen (Berechtigung 0600). Keine echten Zugangsdaten verwenden oder ausgeben.
2. `docker compose --env-file .local/runtime.env -p mgms-local -f tests/compose.yaml up -d` startet nur an Loopback auf Port 18865. Die Datenbank bleibt intern und flüchtig.
3. Für reproduzierbare Versionen das gewünschte offizielle WordPress-Archiv (getestet: 6.5 und 7.1.3) im Testcontainer über die Core-Dateien entpacken; `wp-content` und `wp-config.php` unverändert lassen. Das Basistag `wordpress:php8.1-apache` ist beweglich und war bei der Erstprüfung noch 6.9.
4. `tests/install-local.php` als `/tmp/mgms-install.php` in den neuen WordPress-Container kopieren und dort mit `MGMS_TEST=isolated-local php /tmp/mgms-install.php` ausführen. Ein zufälliger Testzugang wird unter `/tmp/mgms-login.json` angelegt, nicht ausgegeben. Mit `docker cp` nach `.local/login.json` kopieren und lokal auf 0600 setzen.
5. `tests/integration.php` nach `/tmp/mgms-tests.php` kopieren und mit derselben Umgebungsvariable ausführen. Der Ausgangszustand muss ein gerade aktiviertes Plugin mit ausgeschaltetem Wartungsmodus sein. Die Tests richten danach absichtlich eine aktive Wartungsseite ein.
6. `node tests/browser.cjs` ausführen. Playwright muss im Node-Suchpfad liegen, axe-core unter `.local/node_modules/axe-core/`. Ergebnisse werden unter `.local/` gespeichert, Zugangsdaten nicht protokolliert. Diese Suite schaltet Wartung am Ende wieder aus.
7. `tests/security-fixture.php` nach `/tmp/mgms-security.php` kopieren, danach `node tests/security.cjs`. Die Suite erzeugt nur einen zufälligen Leser-Testzugang und testet negative Berechtigungsfälle sowie die echte IP-Freigabe.
8. `tests/visual-fixture.php` nach `/tmp/mgms-visual.php` kopieren, danach `node tests/visual.cjs`. Die veröffentlichten Testwerte werden anschließend wiederhergestellt. Vorschaubilder liegen in `artifacts/previews/`.
9. `tests/lifecycle.php` nach `/tmp/mgms-lifecycle.php` kopieren. Nacheinander als **separate PHP-Prozesse** mit Argumenten `preserve`, `delete`, `restore` und `MGMS_TEST=isolated-local` ausführen. WordPress lädt uninstall.php nur einmal pro Request; zwei Deinstallationen im selben Prozess wären kein gültiger Test.
10. Nach Sichtprüfung nur die eigens angelegten Testcontainer mit demselben Compose-Projekt entfernen. Die Testdatenbank geht dabei verloren. Lokale Testzugänge und Runtime-Konfiguration anschließend entfernen.

Für den Mindestversionstest wurde ein zweites Projekt `mgms-min` auf Port 18866 verwendet. Browser-Suite unterstützt dafür `MGMS_TEST_URL=http://127.0.0.1:18866` und `MGMS_LOGIN_FILE=.local/login-min.json`. Die Security- und Visual-Suiten sind bewusst nur auf das Haupt-Testprojekt festgelegt.

## Statische Prüfungen

- `phpcs --standard=phpcs.xml.dist` prüft die PHP-Dateien gegen WordPress mit dokumentierter Ausnahme für verpflichtende PHPDoc-Blöcke. Einzelfall-Ausnahmen stehen direkt an den betreffenden Zeilen (eigener Renderer, bereits escapte Attribute, zentrale Validierung).
- `phpcs --standard=PHPCompatibilityWP --runtime-set testVersion 8.1- --extensions=php mg-maintenance-studio` prüft bekannte PHP-Kompatibilitätsregeln.
- PHP-Lint auf alle Plugin-PHP-Dateien, `node --check` auf die drei ausgelieferten JavaScript-Dateien.
- Die Browser-Suiten ergänzen tatsächliche Ausgabe, HTTP-Header und Verhalten. Sie ersetzen keine manuelle Screenreader- oder Kundenumgebungsprüfung.

## Gestaltung 0.2.0

`node tests/visual.cjs` legt neue Aufnahmen unter `artifacts/redesign/` an und prüft drei Bildschirmbreiten. Anschließend `node tests/design-states.cjs` für lange Texte und leere optionale Bereiche ausführen. Veränderungen durch diesen Stresstest bleiben im Browser; die ursprünglichen Testoptionen werden wiederhergestellt.

## Updates 0.3.0

`updates.php` im lokalen Container als `/tmp/mgms-updates.php` ablegen und mit
`MGMS_TEST=isolated-local` ausführen. Alle HTTP-Antworten sind Testdaten; es erfolgt
kein GitHub-Zugriff. `node tests/update-ui.cjs` verwendet dieselben lokalen
Browserabhängigkeiten wie `browser.cjs` und erzeugt Screenshots unter artifacts/updates.

Für `update-install.php` eine wegwerfbare Kopie des Pluginordners unter
`/tmp/mgms-upgrade/plugins/mg-maintenance-studio` im Testcontainer anlegen und nur
in deren Hauptdatei die Version auf 0.2.0 setzen. Das aktuelle gebaute ZIP nach
`/tmp/mgms-package.zip` kopieren. Das Skript mit `MGMS_TEST=isolated-local` starten.
Es definiert einen getrennten Pluginpfad und verwendet den echten WordPress-Upgrader;
der Download wird lokal ersetzt. Niemals auf einer bestehenden Website ausführen.

## Fehlerseiten 0.3.2

`php tests/error-pages.php` prüft Standardzustand, Validierung, Kontrast, Vorrang des Wartungsmodus sowie die Ausschlüsse für Anmeldung und Admin ohne Datenbank. Eine echte WordPress- und Theme-Prüfung ist vor produktivem Einsatz zusätzlich nötig.

## Markenmodus und Medienvorschau 0.5.0

`tests/branding.php` in den isolierten Container kopieren und mit `MGMS_TEST=isolated-local php /tmp/mgms-branding.php` ausführen. Der Test legt ausschließlich ein lokales Ein-Pixel-Testbild an, prüft die drei Markenmodi und schreibt danach einen veröffentlichten Testentwurf für die Browseransicht. Die Wegwerf-Datenbank wird beim Entfernen des Compose-Projekts vollständig verworfen.
