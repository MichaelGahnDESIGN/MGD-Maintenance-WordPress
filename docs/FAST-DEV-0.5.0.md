# Fast-Dev-Playbook 0.5.0

## Ziel

Der Designbereich zeigt Medien als Bildvorschau statt als Attachment-ID. Die Wartungsseite unterstützt Text und Logo, nur Text oder nur Logo. Der Wartungsstatus ist als eindeutiger Schalter im Kopf jedes Editors erreichbar. Vier Vorlagen bleiben erkennbar unterschiedliche Layouts.

## Lieferbare Schritte

1. Menü auf die letzte WordPress-Position setzen und den Statusschalter mit Nonce sowie `manage_options` absichern. Rückfall: Schalter wieder ausschalten.
2. Branding-Modus und bildbasierte Medienauswahl in Entwurf, Veröffentlichung und Vorschau ergänzen. Rückfall: den vorherigen veröffentlichten Stand wiederherstellen.
3. Die Designs mit eigenständigen Strukturmerkmalen und einer klaren Auswahloberfläche sichtbar unterscheiden. Rückfall: die vorherige Vorlage auswählen und veröffentlichen.
4. Den Verlassen-Hinweis nur bei tatsächlichen, noch nicht abgesendeten Editoränderungen auslösen. Rückfall: Seite neu laden; der gespeicherte Entwurf bleibt unverändert.

## Prüfbelege

- PHP-Syntax, Schema-/Preset-Test, JavaScript-Syntax und Paketprüfung.
- Lokale WordPress-Prüfung für Speicherung, Medienausgabe und Statuswechsel, sofern die isolierte Testinstallation verfügbar ist.
- Sichtprüfung der breiten und schmalen Adminansicht einschließlich Fokus, Bildvorschau und Statusschalter.

## Veröffentlichungsweg

Kein Live-Deployment. Nach bestandenen lokalen Prüfungen: Commit, GitHub-Workflow, Release-ZIP `mg-maintenance-studio-0.5.0.zip`. Der vorherige Release-Tag bleibt als Rückfall bestehen.
