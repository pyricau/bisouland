#!/usr/bin/env python3
import re
import os
from pathlib import Path

html_dir = Path("top/html")
csv_dir = Path("top/csv")

for html_file in html_dir.glob("*.html"):
    content = html_file.read_text()

    # Find all table rows with data (skip header row)
    rows = re.findall(r'<tr>\s*<td>(\d+)</td>\s*<td>.*?</td>\s*<td>([^<]+)</td>\s*<td>([^<]+)</td>', content, re.DOTALL)

    csv_lines = ["position,name,score,love_points"]
    for position, name, score in rows:
        # Remove spaces from score and convert to int
        score_int = int(score.replace(" ", "").strip())
        csv_lines.append(f"{position},{name.strip()},{score_int},")

    csv_file = csv_dir / f"{html_file.stem}.csv"
    csv_file.write_text("\n".join(csv_lines) + "\n")
    print(f"Converted {html_file.name} -> {csv_file.name}")
