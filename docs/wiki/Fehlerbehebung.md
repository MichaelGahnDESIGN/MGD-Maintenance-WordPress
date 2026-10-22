# Fehlerbehebung

[Zur Übersicht](Home.md) · [Betrieb und Rückfall](Betrieb-und-Wiederherstellung.md)

## Ich sehe die normale Website statt der Wartungsseite

Zuerst ausgeloggt prüfen. Administrator-, Rollen- oder IP-Ausnahmen können gewollt die normale Website zeigen. Danach den tatsächlichen Wartungsschalter und den veröffentlichten Einstellungsstand kontrollieren. Ein gespeicherter Entwurf allein aktiviert keine Wartung. Auch ein altes Cache-Ergebnis kann die Ursache sein.

## Besucher sehen nach dem Abschalten noch Wartung

Den Status in der Übersicht prüfen und relevante Server-/CDN-Caches kontrolliert leeren. In einem frischen Browserfenster erneut testen. Falls ein anderes Wartungsplugin aktiv ist, dessen Status getrennt ansehen. Das Deaktivieren eines Plugins schaltet nicht automatisch andere Schutzmechanismen aus.

## Die Vorschau ist abgelaufen

Den Editor neu laden, den Entwurf gegebenenfalls erneut speichern und eine neue Vorschau öffnen. Eine Freigabe gilt nur zwei Minuten und nur einmal. Ein weiterer Editoraufruf desselben Kontos kann die vorherige Freigabe ersetzen. Vorschau-Tokens nicht kopieren oder als öffentliche Links teilen.

## Der Entwurf lässt sich nicht speichern

Bei einer Revisionsmeldung hat ein anderer Tab oder eine andere Sitzung den gespeicherten Entwurf verändert. Eigene noch nicht gespeicherte Texte zuerst lokal sichern, dann den Editor neu laden. Bei Validierungsfehlern die benannten Felder korrigieren; der gesamte Speichervorgang wird verworfen.

## Bilder oder Schriften werden abgelehnt

Prüfen, ob die Datei wirklich ein Mediathek-Anhang mit erlaubtem Dateityp ist. Die Adresse muss dieselbe Herkunft wie `home_url()` verwenden. Ein CDN-Hostname, abweichendes HTTP/HTTPS oder ein anderer Port kann zur Ablehnung führen. SVG-Uploads und WOFF2-Uploadrechte werden nicht zusätzlich freigeschaltet.

## Ein Webhook erhält 503

Anonyme REST-Anfragen werden während Wartung grundsätzlich blockiert, sofern keine IP-Ausnahme greift. Den konkreten Dienst und dessen Authentifizierung prüfen. Keine pauschale globale Freigabe einrichten, nur um die Fehlermeldung zu beseitigen. Authentifizierte REST-Aufrufe behalten die normalen WordPress-Berechtigungen.

## Das GitHub-Update fehlt

Prüfung eingeschaltet? Richtige Quelle? Stabiles Release mit exakt benanntem Plugin-ZIP? Ist die Release-Version wirklich neuer als die installierte? Dann über **Speichern und jetzt prüfen** erneut anfragen. Hosting-Firewalls und GitHub-Limits können die Anfrage verhindern. Details: [Updates](Updates-und-Releases.md).

## Was in einen Fehlerbericht gehört

Plugin-, WordPress- und PHP-Version, die betroffene Funktion, nachvollziehbare Schritte und erwartetes/tatsächliches Verhalten nennen. Keine Passwörter, Tokens, Cookies, vollständigen Datenbankexporte oder personenbezogenen Kundendaten anhängen. Screenshots vor Veröffentlichung prüfen. Sicherheitsprobleme entsprechend der [Security Policy](../../SECURITY.md) melden.
