#!/usr/bin/env python3
import re
import sys
from pathlib import Path

if len(sys.argv) != 2:
    print(f"Usage: {sys.argv[0]} <page>")
    print(f"Example: {sys.argv[0]} stats")
    sys.exit(1)

page = sys.argv[1]
html_dir = Path(page) / "html"
csv_file = Path(f"gentime-{page}.csv")

if not html_dir.exists():
    print(f"Error: {html_dir} does not exist")
    sys.exit(1)

csv_lines = ["date,seconds"]

for html_file in sorted(html_dir.glob("*.html")):
    content = html_file.read_text()
    date = html_file.stem

    match = re.search(r'Page générée en ([\d.]+) secondes', content)
    if match:
        seconds = float(match.group(1))
        csv_lines.append(f"{date},{seconds}")
    else:
        print(f"Warning: No generation time found in {html_file.name}")

csv_file.write_text("\n".join(csv_lines) + "\n")
print(f"Wrote {len(csv_lines) - 1} rows to {csv_file}")
