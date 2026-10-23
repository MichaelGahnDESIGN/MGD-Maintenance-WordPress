# Installation und Bedienung

## Installation zuerst in einer Testumgebung

Den Ordner `mg-maintenance-studio` nach `wp-content/plugins/` einer eigens angelegten Testinstallation kopieren oder ein ZIP dieses Ordners installieren. Danach Plugin aktivieren. Die erste Aktivierung legt Standardwerte an und lässt den Wartungsmodus **ausgeschaltet**. Einstellungen finden sich unter **Maintenance Studio**. Erforderlich ist die WordPress-Berechtigung `manage_options`.

Die Plugin-Aktivierung ist etwas anderes als die Aktivierung des Wartungsmodus. Eine spätere Plugin-Reaktivierung verwendet den bewusst gespeicherten Wartungsstatus; vor Reaktivierung deshalb den letzten Status kennen.

## Ersteinrichtung

Marke, Titel, Beschreibung und Hinweise im Entwurf bearbeiten. Eigene Rechtstexte eingeben; es werden keine Rechtstexte erfunden. Der Unterstützerhinweis lautet „supported by Michael Gahn DESIGN“. Er ist sichtbar, kann verlinkt und bei Bedarf ausgeschaltet werden. Leeres Linkziel ergibt einfachen Text.

Entwurf speichern und die geschützte Vorschau öffnen. Sie benötigt Administrator-Anmeldung, eine WordPress-Nonce und einen einmaligen Token, der innerhalb von zwei Minuten verwendet werden muss. Der Token wird per POST übertragen, niemals als URL-Parameter. Pro Konto ist eine Freigabe gleichzeitig gültig; ein neu geladener Editor ersetzt die vorherige. Nach dem Öffnen bleibt die bereits geladene Vorschau im Browser sichtbar. „Vorschau erlauben“ ausschalten und den Entwurf speichern verhindert neue Vorschauen, schließt aber keine bereits geladene Browseransicht.

Text, Farben, Abstände, Ausrichtung, Schriftwahl und Container lassen sich nach Öffnen unmittelbar im Vorschaufenster prüfen. Ein neu gewähltes Logo oder Hintergrundbild erscheint ebenfalls direkt. Kontakt-, Countdown- und Rechtstextänderungen benötigen Speichern und erneutes Öffnen der Vorschau. Eine ungespeicherte Vorschau veröffentlicht nichts.

Erst **Einstellungen veröffentlichen** übernimmt den Stand, und nur bei angekreuzter Bestätigung. Ein aktiver Wartungsmodus zeigt diese Veröffentlichung sofort. **Wartungsmodus aktivieren/deaktivieren** in der Übersicht ist eine separate bestätigte Aktion. Countdown und Datum ändern den Modus niemals selbständig.

## Design und Vorlagen

Elegant Gold verwendet einen warmweißen Grund mit gedämpftem Gold, Ruhiges Atelier helle Naturtöne, Minimalistisch Hell eine klare weiße Oberfläche. Die Vorlagen beginnen linksbündig ohne Schatten oder große Karten. Presets ändern nur Designwerte im Formular, nicht Texte oder Rechte. Die Rückgängig-Funktion stellt vorherige Formularwerte wieder her; gespeicherte Änderungen werden zusätzlich als vorheriger Entwurf aufbewahrt. Vor einem umfangreichen Umbau den aktuellen Entwurf speichern.

Mediathek-Bilder müssen vom gleichen Ursprung wie die Website kommen. Erlaubt sind JPEG, PNG, WebP, GIF und AVIF. SVG-Uploads werden bewusst nicht zusätzlich freigeschaltet. Logos benötigen einen sinnvollen Alternativtext, sofern sie Informationen vermitteln. Die Schriftoption akzeptiert vorhandene lokale WOFF2-Anhänge, deren Dateityp WordPress bereits zulässt. Dieses Plugin erweitert keine Upload-Berechtigungen und enthält selbst keine fremden Schriften. Die Verantwortung für Bild- und Schriftlizenzen liegt beim Betreiber.

Der Editor prüft einige Text-Farbpaare auf Kontrast und warnt unter 4,5:1. Das ersetzt keine vollständige Barrierefreiheitsprüfung. Eigene Bilder, Schriftgrößen, Links und Inhalte separat prüfen. Animation ist standardmäßig aus und beachtet reduzierte Bewegung im Betriebssystem.

## Rechtstexte und Kontakt

Bis zu fünf eigenständige Rechtstextblöcke mit Titel, sicheren HTML-Grundelementen, Aktivstatus, Sortierung, Footer- und Modaloption. Links ohne JavaScript führen zu den im Dokument vorhandenen Textabschnitten. Dialoge unterstützen Escape, Schließen-Button, Fokusbindung und Fokusrückgabe. Das Schließen per Hintergrundklick ist abschaltbar.

Optional eine veröffentlichte WordPress-Seite per ID verknüpfen. Diese Seite wird ausdrücklich vom Wartungsmodus ausgenommen und verwendet ihr normales Theme. Dessen externe Ressourcen und Datenschutzverhalten liegen außerhalb dieses Plugins. Private und passwortgeschützte Seiten können nicht als solche Ausnahme gespeichert werden.

Kontakt ist ausschließlich Anzeige/Link (E-Mail, Telefon, Webadresse). Newsletter- oder Rückrufhinweise sind Text, kein Anmeldeformular. Es werden weder Anfragen versandt noch Kontaktdaten von Besuchern gespeichert.

## IPs und Benutzer

Einzelne IP-Adressen oder CIDR-Netze pro Zeile. IPv4-Netze müssen /24 oder enger sein, IPv6-Netze /64 oder enger. Auch erlaubte Netze können viele Benutzer umfassen: nur die tatsächlich benötigten eintragen. Es gibt keine temporären Freigaben und kein personenbezogenes Änderungsprotokoll in Version 0.3.1.

Ohne Proxy-Eintrag zählt nur REMOTE_ADDR. Nur einzelne, bewusst bestätigte Proxy-IP-Adressen dürfen X-Forwarded-For vertrauenswürdig machen. Die Kette wird von rechts nach links bis zum ersten nicht vertrauten Teilnehmer ausgewertet. Der vorgelagerte Proxy muss den Header sicher bereinigen/ergänzen. IP-Freigaben ersetzen niemals eine Anmeldung.

Administrator-Ausnahme wird separat geprüft und hat Vorrang vor der allgemeinen Benutzerregel. Danach gelten „alle eingeloggten Benutzer“, ausgewählte Rollen oder keine Freigabe. Eine zusätzliche IP-Freigabe kann auch für ansonsten blockierte eingeloggte Benutzer greifen. Der Badge erscheint nur bei angemeldeten freigegebenen Benutzern; er ist nicht anklickbar und fängt keine Eingaben ab.
