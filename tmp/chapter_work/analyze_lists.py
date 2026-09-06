"""Inspect figure/table captions and locate them in a rendered PDF."""

from __future__ import annotations

import re
import sys
import zipfile
from pathlib import Path

from lxml import etree
from pypdf import PdfReader


W_NS = "http://schemas.openxmlformats.org/wordprocessingml/2006/main"
NS = {"w": W_NS}


def normalize(text: str) -> str:
    """Normalize whitespace and punctuation for reliable text matching."""
    return re.sub(r"\s+", " ", text.replace("\u00a0", " ")).strip().casefold()


def paragraph_text(paragraph: etree._Element) -> str:
    """Return the visible text contained in one Word paragraph."""
    return "".join(paragraph.xpath(".//w:t/text()", namespaces=NS)).strip()


def paragraph_style(paragraph: etree._Element) -> str:
    """Return the paragraph style identifier when present."""
    values = paragraph.xpath("./w:pPr/w:pStyle/@w:val", namespaces=NS)
    return values[0] if values else ""


def main() -> None:
    docx_path = Path(sys.argv[1])
    pdf_path = Path(sys.argv[2])

    with zipfile.ZipFile(docx_path) as archive:
        root = etree.fromstring(archive.read("word/document.xml"))

    paragraphs = root.xpath(".//w:p", namespaces=NS)
    chapter_started = False
    captions: list[tuple[int, str, str, str]] = []

    print("CAPTION-LIKE PARAGRAPHS")
    for index, paragraph in enumerate(paragraphs):
        text = paragraph_text(paragraph)
        style = paragraph_style(paragraph)
        if text.upper() == "CHAPTER ONE":
            chapter_started = True
        if not chapter_started:
            continue
        if not re.match(r"^(?:Appendix\s+)?(?:Figure|Table)\s+", text, flags=re.I):
            continue
        location = "TXBX" if paragraph.xpath("ancestor::w:txbxContent", namespaces=NS) else "BODY"
        captions.append((index, location, style, text))
        print(f"{index:04d} | {location:4s} | {style:18s} | {text}")

    reader = PdfReader(pdf_path)
    page_texts = [normalize(page.extract_text() or "") for page in reader.pages]

    print("\nCAPTION PAGE LOCATIONS")
    for _, location, style, text in captions:
        target = normalize(text)
        matches = [page_no for page_no, page_text in enumerate(page_texts, start=1) if target in page_text]
        if not matches:
            # Fall back to the caption number and a distinctive title suffix.
            parts = target.split(" ", 2)
            short_target = " ".join(parts[:2]) if len(parts) >= 2 else target
            matches = [page_no for page_no, page_text in enumerate(page_texts, start=1) if short_target in page_text]
        print(f"{text} | physical_pages={matches} | {location}/{style}")

    print("\nLIST PAGES")
    for page_no, page_text in enumerate(page_texts, start=1):
        if "list of figures" in page_text or "list of tables" in page_text:
            print(f"physical_page={page_no}")
            print((reader.pages[page_no - 1].extract_text() or "").strip())


if __name__ == "__main__":
    main()
