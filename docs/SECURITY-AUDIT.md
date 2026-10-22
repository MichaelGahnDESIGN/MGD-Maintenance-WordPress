# Sicherheitsprüfung · MG Maintenance Studio

**Prüfdatum:** 22. Oktober 2026  
**Laufzeitstand:** 0.3.1, Commit `3ec6dcd0d5ee25c267e9d55e146158b492cbe2f4`  
**Prüfart:** Quellcode-Review, Secret-Scans, Abhängigkeitsabfrage und lokale WordPress-Tests.

## Ergebnis und Aussagekraft

Im geprüften Quellstand, der vollständigen vorhandenen Git-Historie und dem von GitHub heruntergeladenen Release-ZIP hat Gitleaks 8.30.1 **keine Secrets erkannt**. GitHub meldete zum Prüfzeitpunkt ebenfalls keine Secret-Scanning-Alerts. Die geprüften Negativfälle für Berechtigungen, Vorschau, Eingaben und Updateangebote wurden abgewiesen wie vorgesehen.

Das bedeutet nicht, dass jedes denkbare Secret-Format erkannt wird oder das Plugin frei von allen Sicherheitslücken ist. Die Prüfung umfasst dieses Repository und dieses Release, nicht andere Plugins, Kundenserver oder private lokale Arbeitsverzeichnisse. Ein unabhängiger Penetrationstest und eine Abnahme der jeweiligen produktiven Infrastruktur stehen weiterhin aus.

## Geprüfte Flächen

| Fläche | Verfahren | Ergebnis |
|---|---|---|
| Vollständige vorhandene Git-Historie | `gitleaks git . --log-opts=--all --redact`; fünf vorhandene Commits zum Ausgangsstand | Keine erkannten Secrets |
| Versionierter Quellstand | Separater Export der 60 versionierten Ausgangsdateien, `gitleaks dir --redact`; 78 Dateien einschließlich neuer Dokumentation vor Veröffentlichung erneut geprüft | Keine erkannten Secrets im Ausgangsstand und in der ergänzten Dokumentation |
| Tatsächliches Release-Asset 0.3.1 | Von GitHub heruntergeladen, ZIP-CRC/Pfade geprüft, 38 Dateien entpackt und gescannt | Keine erkannten Secrets |
| GitHub Secret Scanning | Authentifizierte API-Abfrage; nur Alert-Status ausgewertet | Keine Alerts; Secret Scanning und Push Protection aktiv |
| Entwicklungsabhängigkeiten | `composer audit --locked --no-interaction` | Keine bekannten Sicherheitsmeldungen zum Prüfzeitpunkt |
| Plugin-Laufzeit | Manuelle Prüfung der Handler, Validierung, Ausgabe, Vorschau, IP-Auswertung und Updatequelle | Keine konkrete ausnutzbare Schwachstelle in den geprüften Pfaden nachgewiesen; Grenzen unten |
| Lokale WordPress-Integration | 30 vorhandene Integrationsprüfungen | Bestanden |
| Browserabläufe, HTTP-Header, Nonce und Einmal-Token | 19 vorhandene Browserprüfungen | Bestanden |
| Zusätzliche Sicherheitsfälle | 11 vorhandene echte HTTP-/Browserfälle | Bestanden |
| Updateverhalten | 18 vorhandene Prüfungen mit kontrollierten HTTP-Testantworten | Bestanden |

SHA-256 des tatsächlich geprüften veröffentlichten ZIPs:

```text
63e9701b72a461ae1d8e3453fb05cd0a5a2dc1f24527bcdda282ce423b811907
```

Die aktuelle Wiederholungsprüfung lief in einer neuen lokalen WordPress-**6.9**-Installation mit PHP **8.1.34**, MariaDB in flüchtigem Speicher und Portfreigabe ausschließlich an `127.0.0.1`. Sie verwendet synthetische, zufällig erzeugte Testkonten. Die älteren Tests anderer WordPress-Versionen stehen getrennt im [historischen Testbericht](../mg-maintenance-studio/docs/TESTBERICHT.md); sie wurden hier nicht als neue Ergebnisse ausgegeben.

## Manuell geprüfte Schutzmaßnahmen

### Berechtigungen und fremdausgelöste Schreibaktionen

Admin-Schreibhandler prüfen `manage_options`, POST und eine aktionsbezogene Nonce. Änderungen der Updatequelle benötigen zusätzlich `update_plugins`. Veröffentlichung und Wartungsschalter verlangen ihre jeweilige Bestätigung. Tests prüfen insbesondere unberechtigte Abonnenten, fehlende Bestätigungen und veraltete Entwurfsrevisionen.

Die Vorschau prüft Administratorrechte, Nonce und den zusätzlichen Token aus dem POST-Body. Es gibt keinen anonymen Vorschau-Handler. Die reine Token-Ausstellung erzeugt noch keine öffentliche Freigabe.

### Vorschau und Sitzung

Der Einmal-Token besitzt 256 Bit Zufall, wird als SHA-256-Hash gespeichert und mit Ablaufzeit und Sitzungs-Hash verglichen. Bedingtes Löschen macht seine erfolgreiche Verwendung einmalig. Ablauf und Fremdzugriff wurden erneut getestet. Live-Nachrichten prüfen Ursprung und Elternfenster; Texte werden nicht als HTML eingesetzt.

### Eingaben, HTML und Medien

Farben, Zahlen, Auswahlwerte, Rollen, URLs und Netze werden serverseitig validiert. Zu breite Netze, JavaScript-Links und CSS-Injektionen werden in den Tests zurückgewiesen. Rechtstext-HTML ist auf Grundformatierung und Links beschränkt. Die Ausgabe nutzt passende WordPress-Escapingfunktionen. Medien benötigen einen erlaubten MIME-Typ und identischen Ursprung.

### IP- und Proxy-Vertrauen

Ohne bewusst konfigurierte Proxyadresse zählt die direkte Gegenstelle. `X-Forwarded-For` wird nur bei einer vertrauenswürdigen Gegenstelle ausgewertet. Tests decken gefälschte Header, erlaubte Ketten, Abbruch am ersten fremden Teilnehmer sowie echten HTTP-Zugriff mit und ohne Freigabe ab.

### Updatequelle und Paketverteilung

Nur passende öffentliche Release-Metadaten aus Michaels GitHub-Konto werden akzeptiert. Versionssyntax, Tag, Assetname und Downloadadresse müssen zusammenpassen. Fremde Hosts, zusätzliche Parameter, Vorabversionen und Entwürfe werden zurückgewiesen. Ein API-Fehler liefert kein neues Paketangebot. Änderungen der Quelle sind gesondert berechtigt und per Nonce geschützt.

Das Plugin verwendet keinen GitHub-Token. Der normale WordPress-Upgrader übernimmt den eigentlichen HTTPS-Download. Eine eigene kryptografische Paketprüfung findet derzeit nicht statt; siehe die folgende Risikobewertung.

## Verbleibende Risiken und betriebliche Grenzen

| Punkt | Bewertung und Umgang |
|---|---|
| Keine unabhängige Paketsignatur bzw. eigene Hashprüfung bei Installation | Bekannte Lieferketten-Vertrauensgrenze. Ein kompromittiertes Release-Konto könnte ein Paket austauschen. Release-Rechte begrenzen, Mehrfaktor-Anmeldung verwenden; eine künftige Signaturprüfung gesondert entwerfen. Ein veröffentlichter Hash allein ist keine unabhängige Signatur. |
| Direkte Dateien, XML-RPC, Caches und externe Serverdienste | Außerhalb einer vollständigen WordPress-Ausgabesperre. Bei vertraulichen Inhalten Hosting-/Serverzugriffsschutz einsetzen und Gastansicht prüfen. |
| Anonyme REST-Webhooks | Können bei aktivierter Wartung 503 erhalten. Integrationen vor Kundenbetrieb abnehmen. |
| Proxy-Konfiguration | Falsche Vertrauensadressen können Freigaben verfälschen. Nur belegte einzelne Proxyadressen eintragen und reale Headerkette testen. |
| Gleichzeitig speichernde Administratoren | Revisionsvergleich schützt vor veralteten Tabs, ist aber kein atomarer Datenbank-Lock. Parallele Redaktion abstimmen. |
| Nicht eingelöste Vorschau-Metadaten | Abgelaufen wirkungslos, aber keine zeitgesteuerte Löschung. Werden ersetzt oder über Bereinigung/Deaktivierung/Deinstallation entfernt. |
| Multisite, Kunden-Themes, CDN und Security-Plugins | Nicht in dieser Wiederholungsprüfung als reale Zielumgebungen getestet. |
| Scannerabdeckung | Musterbasierte Scans können unbekannte, verschlüsselte oder ungewöhnlich formatierte Geheimnisse übersehen. Keine pauschale Garantie „secret-frei“. |

## Dauerhafte Ergänzungen

- README und Handbuch erklären den veröffentlichten Stand, Datenflüsse und Sicherheitsgrenzen.
- `SECURITY.md` beschreibt vertrauliche Meldungen. GitHubs private Schwachstellenmeldung wurde aktiviert.
- CI prüft künftig Git-Historie und Quellstand mit Gitleaks 8.30.1. Die Installation des Werkzeugs verwendet einen festgehaltenen SHA-256-Wert des offiziellen Release-Assets.
- Dokumentationslinks werden automatisch geprüft. Das Wiki bleibt als versionierter Quelltext im Repository erhalten.

Keine produktive Website, kein Kundenkonto und keine produktive Datenbank wurden verändert. Der Plugin-Laufzeitcode wurde bei dieser Dokumentations- und Prüfrunde nicht verändert. Das bestehende Release-ZIP wird nicht nachträglich überschrieben.

## Nachvollziehen

Lokale Integrations- und Sicherheitstests: [tests/README.md](../tests/README.md). Secret-Berichte nur redigiert erzeugen, außerhalb öffentlicher Dateien ablegen und bei Treffern zunächst bewerten. Echte Secrets nie als Beleg in einen öffentlichen Bericht kopieren.

Referenzen: [WordPress Nonces und Berechtigungen](https://developer.wordpress.org/apis/security/nonces/), [WordPress: häufige Sicherheitsprobleme bei Plugins](https://developer.wordpress.org/plugins/wordpress-org/common-issues/), [Gitleaks 8.30.1](https://github.com/gitleaks/gitleaks/releases/tag/v8.30.1).

### Testanordnung

Die Sicherheits-Suite setzt ihren Testzustand am Ende zurück. Ein anschließend ohne erneuten Aufbau gestarteter Browserlauf erhielt deshalb zunächst HTTP 200 statt des erwarteten Wartungsstatus 503. Nach erneutem Aufbau der dokumentierten Integrations-Fixture bestanden alle 19 Browserfälle. Dafür wurde keine Plugin-Logik geändert.
