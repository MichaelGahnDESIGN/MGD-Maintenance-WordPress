# Plugin Check – aktualisierter lokaler Lauf

Stand: 23.10.2026. Kandidat 0.5.5; WordPress 7.1.3, PHP 8.3, offizieller Plugin Check 2.1.0 mit Runtime-Bootstrap.

**0 Fehler, 5 Warnungen.** Alle Prüfgruppen aktiv, keine Fehlercodes ausgeschlossen. Vollständiger Bericht: `plugin-check.txt`.

- `WordPress.Security.NonceVerification.Recommended`: 3
- `WordPress.Security.NonceVerification.Missing`: 1
- `WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound`: 1

Die CI läuft zusätzlich mit einer eigenen wp-env-Installation und kann andere Laufzeit-/Performance-Hinweise liefern. Die Gesamtübergabe bewahrt beide Rohberichte getrennt auf. Ein grüner Lauf ist keine WordPress.org-Zulassung.

## Prüfung der Resthinweise

Die drei empfohlenen Nonce-Hinweise lesen ausschließlich feste Meldungskennungen. Die private Redirect-Hilfe liest die feste Rückkehrseite nach dem geschützten Handler und begrenzt sie auf eine Liste. `DONOTCACHEPAGE` ist absichtlich die etablierte Cache-Konstante; ein Plugin-Präfix würde ihre Funktion aufheben. AN/AUS wurde mit gültiger Nonce (503/200) und ungültiger Nonce (403) im Browser geprüft.

## Weitere Belege

Sieben Backends tatsächlich im Browser geöffnet, keine JavaScript-Ausnahmen; echte Screenshots mit künstlichen Daten. 22 lokale Integrationstests zu Formularschutz, Herstellerlink, Entwurf, Menü und privaten/öffentlichen Daten. PHP-Syntax und Verzeichnis-Paketvertrag geprüft. Kein Produktivsystem verändert und keine Einreichung ausgelöst.
