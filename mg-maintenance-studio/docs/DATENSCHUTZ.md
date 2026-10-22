# Datenschutz und Aufbewahrung

MG Maintenance Studio verwendet keine Telemetrie, kein Tracking, keine externen Fonts, Icon-Dienste, Bibliotheks-CDNs oder Bot-Datenbanken. Das Plugin versendet keine E-Mails und betreibt kein Formular. Es setzt keine eigenen Besucher-Cookies. Die WordPress-Anmeldung verwendet weiterhin die üblichen WordPress-Cookies.

## Lokal gespeicherte Daten

In der WordPress-Datenbank bleiben vom Betreiber eingegebene Designwerte, Inhalte, Kontaktangaben, Rechtstexte, Rollen, IP-Freigaben und vertrauenswürdige Proxy-Adressen erhalten. IP-Adressen können personenbezogene Daten sein; nur erforderliche Freigaben eintragen und regelmäßig prüfen. Mediathek-Dateien bleiben normale WordPress-Anhänge; das Plugin speichert lediglich deren IDs.

Es gibt höchstens einen vorherigen Entwurf und veröffentlichten Stand. Alte Inhalte und IP-Freigaben können daher noch in diesen Ständen vorhanden sein, nachdem sie im aktuellen Stand entfernt wurden. Die Aktion „Alte Stände und Vorschau-Freigaben löschen“ entfernt diese Kopien. Backups der Website haben unabhängige Aufbewahrungsfristen.

Vorschau-Freigaben enthalten nur Token-Hash, Ablaufzeit und Hash der Sitzung in Benutzermetadaten. Ein erfolgreicher Aufruf löscht den Datensatz. Nicht verwendete abgelaufene Datensätze gewähren keinen Zugriff mehr; sie werden beim nächsten Erzeugen überschrieben und durch Bereinigung, Vorschau-Abschaltung, Deaktivierung oder Deinstallation gelöscht. Es gibt keine automatische Cron-Bereinigung.

## Flüchtige Verarbeitung

Die Besucher-IP wird im Arbeitsspeicher auf Freigaben geprüft, aber vom Plugin weder gespeichert noch geloggt. Die erkannte IP der aktuellen Admin-Anfrage erscheint nur im Backend. Webserver, Hoster, Sicherheitsplugins oder CDN können unabhängig davon IPs und Anfragen protokollieren. Diese Infrastruktur liegt außerhalb des Plugins und muss separat geprüft werden.

Vorschau-Tokens werden nicht in URLs oder Plugin-Logs geschrieben. POST-Body-Aufzeichnung durch vorgeschaltete Debugging- oder Sicherheitssoftware sollte deaktiviert sein. Es werden keine personenbezogenen Erstellerprotokolle angelegt.

## Löschen

Einträge aus Entwurf entfernen, speichern und ausdrücklich veröffentlichen; danach alte Stände löschen. Für vollständige Entfernung die Löschoption im Entwurf setzen und speichern, Plugin deaktivieren und über WordPress deinstallieren. Ohne gesetzte Löschoption bleiben Einstellungen für spätere Wiederverwendung erhalten. Vorschau-Metadaten werden bei Deinstallation immer gelöscht. Mediathek-Anhänge werden nie automatisch gelöscht.

Diese technische Beschreibung ist keine Rechtsberatung und kein fertiger Datenschutzerklärungstext für die Website. Der Betreiber muss eigene Kontaktdaten, Rechtsgrundlagen, Dienste und Aufbewahrungsfristen prüfen.

## Optionale GitHub-Updates ab 0.3.0

Nur nach Einrichtung und Einschalten kontaktiert der Server `api.github.com`.
GitHub erhält technisch die öffentliche Server-IP, die abgefragte Repositoryadresse
und die Pluginversion im User-Agent. Keine Seiteninhalte, Besucher-IP-Adressen,
Nutzerkonten oder Zugangsdaten werden übertragen. Paketdownloads verwenden GitHub
und dessen Asset-Infrastruktur. Es kann eine Verarbeitung außerhalb der EU stattfinden.
Die Wartungsseite selbst lädt weiterhin ausschließlich lokale Ressourcen.

`mgms_updates` speichert Repository und Zustimmung. `mgms_release` speichert nur
Version, Paketadresse, Prüfzeit und eine allgemeine Ergebnismeldung; zwölf Stunden
bei Erfolg, fünfzehn Minuten bei Fehlern. Der Cache wird bei Änderung oder Deinstallation
entfernt. Die Updateeinstellung folgt bei Deinstallation der vorhandenen Löschoption.
