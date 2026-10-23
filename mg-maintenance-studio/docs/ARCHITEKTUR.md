# Architektur und Sicherheit

## Datenfluss

`Schema` definiert Felder und Standardwerte. `Settings` prüft vollständige Eingaben; bei ungültigen Werten wird nichts gespeichert. `Admin` verwaltet Entwurf, explizite Veröffentlichung, Status und Wiederherstellung. `Access` trifft Benutzerentscheidungen, `IP` wertet Netzwerkfreigaben aus. `Preview` stellt angemeldeten Administratoren eine einmalige Vorschau bereit. `Renderer` und die Frontend-Vorlage liefern ein eigenes Dokument ohne Theme-Hooks aus. `Plugin` registriert die WordPress-Hooks.

Es gibt keine eigene Datenbanktabelle und keine Schema-Migration. Die Options-API speichert Entwurf (`mgms_draft`), Veröffentlichung (`mgms_published`), Status (`mgms_enabled`) und jeweils einen vorherigen Stand. Alle werden ohne Autoload angelegt. Neue Felder erhalten beim Lesen Standardwerte. Größere spätere Datenmodelländerungen benötigen eigene versionierte Migrationen und Rückfalltests.

Fehlerseiten verwenden getrennte Optionen `mgms_error_draft` und `mgms_error_published`. `Error_Pages` validiert die zwei festen Statuscodes und bindet die 404-Vorlage über `template_include` ein. Der `wp_die_handler` ersetzt nur öffentliche HTML-403-Antworten von WordPress. Server-/CDN-403, Anmeldung, Admin, REST und XML-RPC bleiben außerhalb dieses Bereichs. Die Fehlerseiten senden den passenden Status und noindex-Header; der Wartungsmodus hat Vorrang.

## Schreibschutz

Jeder Admin-POST prüft `manage_options`, HTTP-Methode und eine aktionsbezogene WordPress-Nonce. Vorschauen benötigen zusätzlich einen zufälligen Token mit 256 Bit, gespeichert als SHA-256-Hash, gebunden an Benutzer und Session, gültig für zwei Minuten. Bedingtes Löschen des passenden Benutzermetadatensatzes macht ihn einmalig. Die Vorschau setzt keine Freigabecookies und verändert keine Zugriffsregeln. Es gibt keinen anonymen Preview-Handler und keinen AJAX-Schreibendpunkt.

Eine Nonce allein ist keine Berechtigungsprüfung. Beide Prüfungen sind getrennt implementiert, entsprechend der [WordPress-Dokumentation zu check_admin_referer](https://developer.wordpress.org/reference/functions/check_admin_referer/).

Der Editor vergleicht eine Revision mit dem aktuell gespeicherten Entwurf, um veraltete Browser-Tabs abzuweisen. Dies ist kein vollständiges Datenbank-Locking für exakt gleichzeitige Schreibvorgänge mehrerer Administratoren. Für parallele Redaktion muss eine spätere Version atomare Konfliktauflösung ergänzen.

## Ausgabe und Ressourcen

Texte werden kontextbezogen escaped; Rechtstext-HTML erlaubt nur Formatierung und Links. Keine iframes, Skripte, eingebetteten Bilder, Shortcodes oder fremde Styles in Rechtstexten. Farben und Größen haben feste Validierungsregeln. Bilder und Fonts müssen gültige Mediathek-Anhänge gleicher Herkunft sein. Die Content Security Policy der Wartungsseite erlaubt Skripte und Ressourcen nur lokal, blockiert Netzwerk-APIs, Formulare und fremde Frames; Inline-CSS ist für Designwerte erlaubt.

Live-Vorschau-Nachrichten prüfen Ursprung und sendendes Elternfenster. Sie verwenden Textzuweisung und geprüfte CSS-Werte, kein frei übergebenes HTML. Der Renderer ruft weder `wp_head()` noch `wp_footer()` auf; damit werden keine Theme-/Plugin-Ressourcen automatisch geladen. Sicherheits- oder Consent-Plugins, die ausschließlich diese Hooks benötigen, erscheinen folglich ebenfalls nicht in der Wartungsansicht.

## SEO, Crawler und Grenzen

Anonyme Frontend-Seiten einschließlich Feed- und Sitemap-Anfragen werden durch Wartung ersetzt. 503 ist Standard, Retry-After liegt zwischen 60 und 86400 Sekunden. Meta und X-Robots-Tag setzen noindex, nofollow, noarchive. 200 und 403 sind bewusst wählbar, können aber SEO-Nachteile haben. Virtuelle robots.txt-Regeln sind optional; physische Dateien werden nicht verändert. Disallow verhindert möglicherweise das Lesen von noindex. Regeln sind kein zuverlässiger Schutz vor KI-Crawlern oder unkooperativen Bots. User-Agent-Listen werden nicht eingesetzt.

Anonyme REST-Anfragen werden während Wartung mit 503 zurückgewiesen, außer eine IP-Freigabe greift. Angemeldete REST-Anfragen bleiben für WordPress-Editor und Integrationen unter deren Berechtigungen verfügbar. Login, Backend, Cron und AJAX behalten ihre eigenen Schutzmaßnahmen. XML-RPC, direkte Dateien und Serverdienste werden nicht abgeschirmt. Bei späterem Einsatz Webhooks und externe Integrationen ausdrücklich prüfen: anonyme REST-Webhooks können während Wartung blockiert werden.

## Kompatibilität

WordPress 6.5+, PHP 8.1+. Keine Änderungen an Core, Theme oder anderen Plugins. Keine REST-Routen für den Editor. Netzwerkaktivierung wird abgewiesen; in Multisite pro Website einzeln einrichten. Vorschau-Freigaben im gemeinsamen Benutzerkonto können sich zwischen Websites gegenseitig ersetzen (sicherer Fehler: neue Vorschau erforderlich).

Eine konservative Namensprüfung meldet bekannte Wartungsplugin-Muster in aktiven normalen und Netzwerkplugins. Sie kann nicht jedes Plugin erkennen und schaltet keines ab. Page Cache, CDN und statische Dateien können PHP umgehen: `DONOTCACHEPAGE` und [nocache_headers](https://developer.wordpress.org/reference/functions/nocache_headers/) sind Hinweise, keine garantierte Cache-Löschung.

## Version 0.3.0

`class-updates.php` enthält Releasevalidierung, Cache, WordPress-Updatefilter und den
separaten POST-Endpunkt. `templates/updates.php` rendert die Verwaltung ohne eigene
Netzwerkanfrage. `class-presentation.php` ergänzt Einstellungen-Link, Details und
lokales Pluginlisten-Icon. `assets/icon.svg` ist eine eigenständige Vektorgrafik.
Updatekonfiguration bleibt getrennt von Entwurf, veröffentlichtem Design und Wartungsstatus.
