#!/usr/bin/env python3
import re
from pathlib import Path
from html import unescape

html_dir = Path("articles/html")
md_dir = Path("articles/md")
md_dir.mkdir(exist_ok=True)

def html_to_md(html):
    """Convert HTML content to markdown."""
    text = html

    # Convert links to markdown
    text = re.sub(r'<a[^>]*href="([^"]*)"[^>]*>([^<]*)</a>', r'[\2](\1)', text)

    # Remove wayback machine URL prefixes
    text = re.sub(r'https://web\.archive\.org/web/\d+/', '', text)

    # Convert <br> to newlines
    text = re.sub(r'<br\s*/?>', '\n', text)

    # Remove smiley images (or convert to alt text)
    text = re.sub(r'<img[^>]*alt="([^"]*)"[^>]*>', r'\1', text)

    # Remove span tags but keep content
    text = re.sub(r'</?span[^>]*>', '', text)

    # Remove any remaining HTML tags
    text = re.sub(r'<[^>]+>', '', text)

    # Unescape HTML entities
    text = unescape(text)

    # Clean up multiple newlines
    text = re.sub(r'\n{3,}', '\n\n', text)

    return text.strip()

# Collect all unique articles (keyed by date)
articles = {}

for html_file in sorted(html_dir.glob("*.html")):
    content = html_file.read_text()

    # Find all news divs
    news_blocks = re.findall(r'<div class="news">(.*?)</div>', content, re.DOTALL)

    for block in news_blocks:
        # Extract title
        title_match = re.search(r'<h3>\s*(.*?)\s*</h3>', block, re.DOTALL)
        title = title_match.group(1).strip() if title_match else "Sans titre"

        # Extract date
        date_match = re.search(r'<em>\s*le (\d{2})/(\d{2})/(\d{4}) à (\d{2})h(\d{2})', block)
        if not date_match:
            continue

        day, month, year = date_match.group(1), date_match.group(2), date_match.group(3)
        hour, minute = date_match.group(4), date_match.group(5)
        article_key = f"{year}-{month}-{day}-{hour}{minute}"

        # Skip if we already have this article
        if article_key in articles:
            continue

        # Extract modified date if present
        modified_match = re.search(r'<em>modifi(?:é|ée) le (\d{2}/\d{2}/\d{4}) à (\d{2}h\d{2})', block)
        modified_info = ""
        if modified_match:
            modified_info = f" (modifié le {modified_match.group(1)} à {modified_match.group(2)})"

        # Extract content
        content_match = re.search(r'<p>\s*(.*?)\s*</p>', block, re.DOTALL)
        article_content = html_to_md(content_match.group(1)) if content_match else ""

        articles[article_key] = {
            "title": title,
            "date": f"{day}/{month}/{year}",
            "time": f"{hour}h{minute}",
            "modified": modified_info,
            "content": article_content,
        }

# Write each article to its own file
for key in sorted(articles.keys()):
    article = articles[key]
    md_lines = [
        f"# {article['title']}",
        f"*le {article['date']} à {article['time']}{article['modified']}*",
        "",
        article['content'],
        "",
    ]
    md_file = md_dir / f"{key}.md"
    md_file.write_text("\n".join(md_lines))

print(f"Extracted {len(articles)} unique articles to {md_dir}/")
