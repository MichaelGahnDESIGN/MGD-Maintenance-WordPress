# Sicherheitskonzept und bekannte Grenzen

[Zur Übersicht](Home.md) · [Prüfbericht](../SECURITY-AUDIT.md) · [Sicherheitsproblem melden](../../SECURITY.md)

## Berechtigungen vor Änderungen

Einstellungen und Statusaktionen verlangen `manage_options`, POST und eine aktionsbezogene WordPress-Nonce. Änderungen der Updatequelle verlangen zusätzlich `update_plugins`. Eine Nonce verhindert bestimmte fremdausgelöste Anfragen, ersetzt aber keine Berechtigungsprüfung. Diese Trennung folgt dem [WordPress-Sicherheitsmodell](https://developer.wordpress.org/apis/security/nonces/).

Veröffentlichen und Umschalten benötigen zusätzlich eine bewusste Bestätigung. Eine Entwurfsrevision weist veraltete Browser-Tabs ab. Sie ist kein vollständiges Datenbank-Locking für exakt gleichzeitig eintreffende Änderungen.

## Geschützte Vorschau

Die Freigabe kombiniert Administrator-Anmeldung, Nonce und einen mit `random_bytes` erzeugten Token mit 256 Bit. Gespeichert werden nur Hash, Ablaufzeit und Sitzungs-Hash. Der Token wird im POST-Body übertragen und muss innerhalb von zwei Minuten benutzt werden. Bedingtes Löschen verhindert seine erneute Verwendung.

Die Live-Vorschau akzeptiert Nachrichten nur vom eigenen Elternfenster mit gleichem Ursprung. Texte werden als Text gesetzt; Designwerte werden begrenzt geprüft. Frei übergebenes HTML wird nicht ausgeführt.

## Eingaben und Ausgabe

Serverseitige Feldprüfung begrenzt Zahlen, Farbcodes, Optionen und Linkprotokolle. Mediathek-Dateien müssen einen erlaubten MIME-Typ und denselben Ursprung haben. Rechtstexte werden auf eine kleine Menge HTML-Elemente reduziert. Die Ausgabe nutzt kontextbezogenes Escaping.

Die Wartungsantwort verwendet unter anderem `Cache-Control: no-store`, `X-Content-Type-Options: nosniff`, `Referrer-Policy: no-referrer` und eine begrenzte Content Security Policy. Externe Skripte, Netzwerk-APIs und Formulare werden darin blockiert. Inline-CSS ist für Designwerte erlaubt. HTTPS und HSTS sind Aufgaben der Hostingkonfiguration.

## Das Plugin schützt nicht alles

| Grenze | Bedeutung |
|---|---|
| Direkte Dateien und Uploads | Können vom Webserver ohne WordPress ausgeliefert werden |
| CDN und Servercache | Können WordPress und seine aktuellen Header umgehen |
| Login, Backend, Cron und AJAX | Behalten ihre eigenen WordPress-/Plugin-Schutzregeln |
| Authentifizierte REST-Aufrufe | Bleiben für Editor und Integrationen verfügbar |
| XML-RPC und weitere Serverdienste | Werden nicht als allgemeine Firewall abgeschirmt |
| Freigegebene Rechtsseiten | Verwenden das normale Theme mit eigenen Ressourcen |
| Kompromittierter Administrator oder GitHub-Release-Zugang | Liegt innerhalb wichtiger Vertrauensgrenzen; wird nicht durch Wartung geheilt |

IP-Ausnahmen sind keine starke Personenidentifikation. Proxy-Konfigurationen müssen zur tatsächlichen Infrastruktur passen. Regeln für Suchmaschinen sind Hinweise für kooperative Bots und kein Schutz vor Datensammlung.

## Paket- und Secret-Sicherheit

Laufzeitcode benötigt keine Secrets. GitHub-Prüfungen benötigen keinen Token auf der Website. Lokale Testkonfiguration und Zugangsdaten gehören in ignorierte Verzeichnisse, niemals ins Plugin-ZIP. Ein Secret-Scan muss auch die Git-Historie und veröffentlichte Assets prüfen; `.gitignore` allein genügt nicht.

Bei den GitHub-Updates besteht bislang keine unabhängige Paketsignaturprüfung. HTTPS, erwartete Release-Adressen und sichere Release-Rechte bilden die aktuelle Vertrauensbasis. Ergebnisse und Grenzen der konkreten Prüfung stehen im [Sicherheitsaudit](../SECURITY-AUDIT.md).
