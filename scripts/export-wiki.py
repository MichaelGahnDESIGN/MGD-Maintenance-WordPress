"""Handbuch für ein GitHub-Wiki exportieren; Quelldateien bleiben unverändert."""
from pathlib import Path
import argparse
import re
from urllib.parse import quote, urlsplit

parser = argparse.ArgumentParser(description=__doc__)
parser.add_argument('destination', type=Path, help='Lokaler Ausgabeordner oder vorhandener Wiki-Checkout')
args = parser.parse_args()
root = Path(__file__).resolve().parents[1]
source = root / 'docs/wiki'
destination = args.destination.resolve()
if destination == source or destination == root:
    parser.error('Ein separates Zielverzeichnis verwenden.')
destination.mkdir(parents=True, exist_ok=True)

for page in sorted(source.glob('*.md')):
    def rewrite(match):
        label, address = match.groups()
        parts = urlsplit(address)
        if parts.scheme or parts.netloc or not parts.path:
            return match.group(0)
        target = (page.parent / parts.path).resolve()
        if not target.is_relative_to(root) or not target.is_file():
            raise ValueError(f'Ungültiger Dokumentationslink in {page.name}')
        if target.parent == source and target.suffix == '.md':
            url = quote(target.stem)
        else:
            kind = 'raw' if target.suffix == '.png' else 'blob'
            url = f'https://github.com/MichaelGahnDESIGN/MGD-Maintenance-WordPress/{kind}/main/' + quote(target.relative_to(root).as_posix())
        if parts.fragment:
            url += '#' + parts.fragment
        return f'[{label}]({url})'
    text = re.sub(r'\[([^\]]*)\]\(([^)]+)\)', rewrite, page.read_text(encoding='utf-8'))
    (destination / page.name).write_text(text, encoding='utf-8')
print(f'{len(list(source.glob("*.md")))} Wiki-Dateien exportiert.')
