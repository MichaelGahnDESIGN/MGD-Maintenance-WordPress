# Fast-Dev 0.5.3

Ziel: separate Update-Prüfung auf der WordPress-Aktualisierungsseite und im Plugin, wiederherstellbare Repository-Quelle und letzter Menüplatz.

Die gemeinsame, eigenständig mitgelieferte Admin-Komponente registriert einen Prüfknopf für aktive MGD-Plugins. Nonce und `update_plugins` schützen den POST-Aufruf. Nur eigene Release-Caches werden geleert; die native WordPress-Prüfung übernimmt das Ergebnis. Es werden keine Pakete installiert. Share und Notification verwenden feste GitHub-Repositories mit exakt passenden Release-Assets.

Maintenance Studio merkt sich die letzte Quelle erst nach einer erfolgreichen Paketvalidierung. Wiederherstellung verlangt zusätzlich `manage_options` und erhält die bestehende HTTP-Zustimmung. Die Originalquelle bleibt im Code verfügbar. Der Menüfilter verschiebt nur `mgms-dashboard` ans Ende und erhält die Reihenfolge anderer Einträge.

Geprüft: PHP-Syntax, zwölf isolierte Funktionstests mit simuliertem WordPress, Paketprüfung. Die Tests decken Berechtigungen, Nonce, Paket-URL, Vorabversionen, gemeinsame Registrierung, Cache-Grenzen, lokale Details, Menü und Wiederherstellung ab. Eine vollständige echte WordPress-Installation war lokal nicht verfügbar. Kein Live-Deployment.

Rückfall: GitHub-Release 0.5.2. Vor Installation eine Datenbanksicherung durchführen. Einstellungen werden nicht durch die reine Update-Prüfung verändert.
