# Ressourcen-Updater: sichere Ausbaustufe

Dies ist ein Vorschlag, kein installierter Hintergrunddienst. Ziel: bekannte Schwachstellen und auslaufende Unterstützung erkennen, sichere Änderungen vorbereiten und Michael nur bei Entscheidungen oder gescheiterten Prüfungen einbeziehen.

## Zwei getrennte Ebenen

1. **Projektabhängigkeiten:** Composer-, npm-/Node-, Container- und GitHub-Actions-Abhängigkeiten aus vorhandenen Manifesten und Lockdateien erfassen. GitHub Dependabot/Alerts oder vorhandene Renovate-Installation nutzen, statt einen eigenen Paketmanager zu schreiben. Security-PRs pro zusammengehöriger Änderung mit nachvollziehbaren Tests. Bot-Zugriff mit minimalen Rechten; keine automatische Veröffentlichung allein durch einen grünen Dependency-Job.
2. **Laufzeiten und Server:** PHP-/Node-Laufzeit, Betriebssystem, Provider und Container getrennt inventarisieren. Aus `package.json` lässt sich die produktive Node-Version nicht sicher ableiten. Ein hostendes WordPress-Plugin darf weder PHP noch das Betriebssystem selbst austauschen. Provider-Backports prüfen, bevor eine Versionsnummer pauschal als ungepatcht gilt.

## Ablauf

Das Inventar enthält Projekt, verantwortliche Umgebung, Ist-Version, erlaubte Zielversion, Bezugsquelle, Supportende, Testweg und Rückfallweg; keine Passwörter, Tokens oder Kundendaten. Offizielle Advisories und Supportlisten regelmäßig abrufen. Fehlende Informationen als unbekannt behandeln. Ein grüner Bericht bedeutet nur, dass die überprüften Quellen gerade keine bekannten Treffer liefern.

Vorbereitung in isoliertem Branch: Paket aus vertrauenswürdiger Quelle, Lockdatei und Integrität prüfen, nötige Migrationen erkennen, Ziel-Laufzeit in einer wegwerfbaren Testumgebung aufbauen. Installationsskripte von Abhängigkeiten sind ausführbarer Fremdcode: keine produktiven Secrets oder privilegierten Runner hineinreichen. PRs aus fremden Forks nie mit schreibenden Tokens über `pull_request_target` ausführen.

Für freigegebene, überschaubare Patch-Updates kann eine projektbezogene Automerge-Regel möglich sein. Sie braucht verbindliche Prüfungen, eine begrenzte Allowlist und eine getrennte Releaseentscheidung. Majorwechsel, Datenmigrationen, Authentifizierungs-/Zahlungsänderungen, EOL-Wechsel und produktive Server-Neustarts bleiben eigene Änderungen mit Sicherung und geprüftem Rückfallweg. Nach fehlgeschlagenem Test nicht mit `--force`, ignorierten Advisories oder deaktivierten Prüfungen weiterarbeiten.

Eine geplante Automation sollte bei unverändertem Zustand still bleiben. Nur relevante neue Schwachstellen, fehlgeschlagene Prüfungen oder eine nötige Freigabe melden. Ohne eingerichteten Zeitplan erfolgt keine dauerhafte Überwachung.

## Offizielle Grundlagen

- [GitHub: Dependabot Security Updates](https://docs.github.com/en/code-security/how-tos/secure-your-supply-chain/secure-your-dependencies/configure-security-updates)
- [PHP: Unterstützte Versionen](https://www.php.net/supported-versions.php)
- [Node.js: Release- und LTS-Status](https://nodejs.org/en/about/previous-releases)

Die aktuelle Lage jeweils neu prüfen. Eine bestimmte heute sichere Versionsnummer nicht dauerhaft im Skill festschreiben.
