#!/usr/bin/env python3
import re
from pathlib import Path

html_dir = Path("stats/html")
csv_file = Path("stats/stats.csv")

# Mapping from French text to CSV column names
metric_patterns = [
    (r"Nombre total de points d'amours disponibles dans le jeu\s*:\s*(.+)", "total_points"),
    (r"Nombre de points d'amours moyen par personne\s*:\s*(.+)", "avg_points"),
    (r"Nombre de membres connectés dans les dernières 5 minutes\s*:\s*(.+)", "connected_5min"),
    (r"Nombre de membres connectés dans les dernières 60 minutes\s*:\s*(.+)", "connected_60min"),
    (r"Nombre de membres connectés dans les dernières 12 heures\s*:\s*(.+)", "connected_12h"),
    (r"Nombre de membres connectés dans les dernières 24 heures\s*:\s*(.+)", "connected_24h"),
    (r"Nombre de membres connectés dans les dernières 48 heures\s*:\s*(.+)", "connected_48h"),
    (r"Nombre de membres connectés dans les derniers 7 jours\s*:\s*(.+)", "connected_7d"),
    (r"Nombre de membres connectés dans les derniers 30 jours\s*:\s*(.+)", "connected_30d"),
    (r"Nombre de membres connectés depuis un an\s*:\s*(.+)", "connected_1y"),
]

columns = ["date"] + [name for _, name in metric_patterns]
csv_lines = [",".join(columns)]

for html_file in sorted(html_dir.glob("*.html")):
    content = html_file.read_text()
    date = html_file.stem

    # Extract the #corps div content
    corps_match = re.search(r'<div id="corps">(.*?)</div>', content, re.DOTALL)
    if not corps_match:
        print(f"Warning: No #corps div found in {html_file.name}")
        continue

    corps_content = corps_match.group(1)

    # Extract metrics
    metrics = {"date": date}
    for pattern, col_name in metric_patterns:
        match = re.search(pattern, corps_content)
        if match:
            # Remove spaces (thousand separators) and convert to int
            value = match.group(1).replace(" ", "").strip()
            # Remove any trailing <br> or HTML tags
            value = re.sub(r'<.*', '', value)
            metrics[col_name] = int(value)

    values = [str(metrics.get(col, "")) for col in columns]
    csv_lines.append(",".join(values))

csv_file.write_text("\n".join(csv_lines) + "\n")
print(f"Wrote {len(csv_lines) - 1} rows to {csv_file}")
