# Zugriffe, Rollen und IP-Freigaben

[Zur Übersicht](Home.md) · [Sicherheitsgrenzen](Sicherheit.md)

## Wer sieht die normale Website?

Bei ausgeschaltetem Wartungsmodus verändert das Plugin die normale Frontend-Ausgabe nicht. Bei eingeschalteter Wartung entscheidet der **veröffentlichte** Einstellungsstand:

1. Angemeldete Konten mit `manage_options` verwenden die separate Administrator-Ausnahme.
2. Andere angemeldete Konten können vollständig, nach ausgewählten Rollen oder gar nicht freigegeben werden.
3. Eine aktivierte passende IP-Freigabe kann unabhängig davon Zugriff erlauben.

Die Administratorregel hat innerhalb der Benutzerprüfung Vorrang. Eine passende IP-Freigabe kann trotzdem greifen. Der dezente Wartungshinweis erscheint nur bei angemeldeten, freigegebenen Personen und ist nicht anklickbar.

## Einzeladressen und Netze

Pro Zeile eine IPv4-/IPv6-Adresse oder ein CIDR-Netz eintragen. CIDR bezeichnet einen zusammenhängenden Adressbereich. Zulässig sind IPv4-Netze ab `/24` und IPv6-Netze ab `/64`, jeweils einschließlich engerer Bereiche. Maximal 100 Einträge werden akzeptiert.

Ein Netz kann viele Geräte und Personen umfassen. Wähle möglichst einzelne, tatsächlich benötigte Adressen. Die folgenden Beispiele sind reservierte Dokumentationsadressen und keine reale Freigabe:

```text
192.0.2.17
198.51.100.0/24
2001:db8::17
```

Entwurf speichern und anschließend ausdrücklich veröffentlichen. Danach aus einem erlaubten und einem nicht erlaubten Netz testen. Ein VPN-Wechsel oder eine neue Anschluss-IP kann die Wirkung ändern. Automatisch auslaufende Freigaben gibt es in 0.3.1 nicht.

## Reverse Proxy und CDN

Ohne konfigurierte Proxys zählt `REMOTE_ADDR`, also die direkte Gegenstelle der Anfrage. Ein vom Besucher frei gesetzter `X-Forwarded-For`-Header wird dann ignoriert.

Nur wenn diese Gegenstelle ausdrücklich als vertrauenswürdiger Proxy eingetragen ist, wird die Headerkette von rechts nach links ausgewertet. Die Auswertung endet beim ersten nicht vertrauten Teilnehmer. Proxy-Einträge müssen einzelne Adressen sein. Der Proxy selbst muss die Header korrekt bereinigen oder ergänzen.

Falsche Proxy-Einträge können eine Freigabe unzuverlässig machen. Hosting- oder CDN-Adressen nicht raten und nicht pauschal große Netze übernehmen. Bei unklarer Infrastruktur besser mit angemeldeten Konten arbeiten.

## Weitere WordPress-Zugänge

Login, Backend, Cron und AJAX bleiben für den normalen WordPress-Betrieb erreichbar und benötigen weiterhin ihre eigenen Berechtigungen. Authentifizierte REST-Aufrufe bleiben verfügbar. Anonyme REST-Aufrufe werden während Wartung mit 503 gesperrt, sofern keine IP-Ausnahme greift. Das kann auch externe Webhooks treffen.

Diese Ausnahmen sind kein allgemeiner Zugriff auf unveröffentlichte Seiten. Direkte Upload-Dateien, XML-RPC und Cache-Antworten liegen außerhalb der vollständigen Kontrolle dieses Plugins.
