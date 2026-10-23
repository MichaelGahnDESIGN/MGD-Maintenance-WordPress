# Geprüfte statische Einzel-Ausnahmen

Die nachfolgende Liste zeigt alle PHPCS-Einzel-Ausnahmen im tatsächlich gebauten Paket, einschließlich schon zuvor vorhandener Stellen. Kein phpcs:disable und keine globale Abschaltung von Plugin-Check-Prüfgruppen. Die konkreten Gründe stehen direkt am betroffenen Zugriff. Die Code-Ausnahmen müssen bei künftigen Änderungen erneut geprüft werden; sie sind keine pauschale Freigabe ähnlicher neuer Zugriffe.

- `includes/class-admin.php:46` – WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Ausschließlich fest benannte lokale Preset-Dateien.
- `includes/class-admin.php:55` – WordPress.Security.NonceVerification.Missing -- Private Weiterleitung nach Nonce-geschütztem Handler; feste erlaubte Rückkehrseiten.
- `includes/class-admin.php:77` – WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Settings prüft jedes Feld; Fehler verhindern den gesamten Schreibvorgang.
- `includes/class-admin.php:168` – WordPress.Security.NonceVerification.Recommended -- Nur lesende Auswahl eines registrierten Einstellungsbereichs.
- `includes/class-admin.php:189` – WordPress.Security.EscapeOutput.OutputNotEscaped -- Attribute werden oben einzeln mit esc_attr aufgebaut.
- `includes/class-admin.php:192` – WordPress.Security.EscapeOutput.OutputNotEscaped -- Attribute werden oben einzeln mit esc_attr aufgebaut.
- `includes/class-admin.php:198` – WordPress.Security.EscapeOutput.OutputNotEscaped -- Attribute werden oben einzeln mit esc_attr aufgebaut.
- `includes/class-admin.php:208` – WordPress.Security.EscapeOutput.OutputNotEscaped -- Attribute werden oben einzeln mit esc_attr aufgebaut.
- `includes/class-plugin.php:44` – WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound -- Etablierte Konstante für Cache-Plugins; Umbenennen würde Wartungsseiten im Seiten-Cache zulassen.
- `templates/admin.php:11` – WordPress.Security.NonceVerification.Recommended -- Feste Meldungsauswahl ohne Zustandsänderung.
- `templates/dashboard.php:9` – WordPress.Security.NonceVerification.Recommended -- Nur feste Erfolgsmeldung, keine Statusänderung.
- `templates/error-pages-admin.php:10` – WordPress.Security.NonceVerification.Recommended -- Nur feste Rückmeldung eines geschützten Speichervorgangs.
