#!/usr/bin/env python3
import csv
import sys
import os

def ascii_chart(filename, title, width=60, height=15, percentile=90, stabilized=None):
    with open(filename) as f:
        reader = csv.DictReader(f)
        data = [(row['date'], float(row['seconds'])) for row in reader]

    dates, values = zip(*data)
    values = [v * 1000 for v in values]  # convert to ms
    sorted_vals = sorted(values)
    cap = sorted_vals[int(len(sorted_vals) * percentile / 100)]
    max_val, min_val = cap, min(values)
    clipped = sum(1 for v in values if v > cap)

    print(f"\n{'=' * width}\n{title}\n{'=' * width}")
    print(f"Cap (p{percentile}): {max_val:.1f}ms | Min: {min_val:.1f}ms | Points: {len(values)}")
    if stabilized:
        print(f"Stabilized at: {stabilized}")
    if clipped:
        print(f"Clipped: {clipped} outliers above cap (max was {max(values):.1f}ms)")
    print(f"Range: {dates[0]} to {dates[-1]}\n")

    for row in range(height, 0, -1):
        threshold = min_val + (max_val - min_val) * row / height
        line = "".join("▲" if v > cap else "█" if v >= threshold else " " for v in values)
        label = f"{threshold:5.0f}ms" if row % 3 == 0 else "       "
        print(f"{label} |{line}|")

    print(f"        +{'-' * len(values)}+")
    print(f"         {dates[0][:4]}{' ' * (len(values) - 8)}{dates[-1][:4]}")

if __name__ == "__main__":
    script_dir = os.path.dirname(os.path.abspath(__file__))
    ascii_chart(os.path.join(script_dir, "gentime-articles.csv"), "ARTICLES PAGE GENERATION TIME", stabilized="~8ms")
    ascii_chart(os.path.join(script_dir, "gentime-stats.csv"), "STATS PAGE GENERATION TIME", percentile=75, stabilized="~40ms")
