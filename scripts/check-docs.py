"""Lokale Markdown-Links und Bilder prüfen, ohne externe Seiten aufzurufen."""
from pathlib import Path
import re
from urllib.parse import unquote, urlsplit

root = Path(__file__).resolve().parents[1]
files = [root / 'README.md', root / 'SECURITY.md', *sorted((root / 'docs').rglob('*.md'))]
errors = []
links = 0
for page in files:
    content = re.sub(r'```.*?```', '', page.read_text(encoding='utf-8'), flags=re.S)
    for match in re.finditer(r'\[[^\]]*\]\(([^)]+)\)', content):
        address = match.group(1).strip('<>')
        parts = urlsplit(address)
        if parts.scheme or parts.netloc or not parts.path:
            continue
        target = (page.parent / unquote(parts.path)).resolve()
        links += 1
        if not target.is_relative_to(root) or not target.exists():
            errors.append(f'{page.relative_to(root)}: {address}')
if errors:
    raise SystemExit('\n'.join(errors))
print(f'{len(files)} Markdown-Dateien, {links} lokale Links und Bildverweise gültig.')
