#!/usr/bin/env python3
"""Verifica links relativos em arquivos HTML e Markdown do repositório.

Uso:
    python3 scripts/verificar_links.py [--verbose]

Ignora links externos (http/https/mailto), âncoras e diretórios de build
(.git, vendor, node_modules, reports, coverage-detail).
"""
from __future__ import annotations

import argparse
import re
import sys
from pathlib import Path
from urllib.parse import unquote, urlparse

ROOT = Path(__file__).resolve().parent.parent
SKIP_DIRS = {".git", "vendor", "node_modules", "reports", "coverage-detail", "__pycache__"}
HTML_REF = re.compile(r"""(?:href|src)\s*=\s*["']([^"']+)["']""", re.IGNORECASE)
MD_LINK = re.compile(r"\[[^\]]*\]\(([^)\s]+)\)")


def iter_files() -> list[Path]:
    files = []
    for path in ROOT.rglob("*"):
        if not path.is_file():
            continue
        if any(part in SKIP_DIRS for part in path.relative_to(ROOT).parts):
            continue
        if path.suffix.lower() in {".html", ".md"}:
            files.append(path)
    return files


def extract_refs(path: Path) -> list[str]:
    text = path.read_text(encoding="utf-8", errors="ignore")
    pattern = HTML_REF if path.suffix.lower() == ".html" else MD_LINK
    return [m.group(1) for m in pattern.finditer(text)]


def is_local(ref: str) -> bool:
    if not ref or ref.startswith(("#", "mailto:", "tel:", "javascript:", "data:")):
        return False
    return urlparse(ref).scheme == ""


def check() -> tuple[int, list[str]]:
    problems: list[str] = []
    total = 0
    for file in iter_files():
        for ref in extract_refs(file):
            if not is_local(ref):
                continue
            target_ref = ref.split("#", 1)[0].split("?", 1)[0]
            if not target_ref:
                continue
            target = (file.parent / unquote(target_ref)).resolve()
            total += 1
            if not target.exists():
                problems.append(f"{file.relative_to(ROOT)} -> {ref}")
    print(f"Arquivos verificados: {len(iter_files())} · links locais: {total}")
    return total, problems


def main() -> int:
    parser = argparse.ArgumentParser(description="Verifica links relativos do repositório.")
    parser.add_argument("--verbose", action="store_true", help="lista todos os links verificados")
    parser.parse_args()

    _, problems = check()
    if problems:
        print(f"\n❌ {len(problems)} link(s) quebrado(s):")
        for item in problems:
            print(f"  - {item}")
        return 1

    print("✅ Nenhum link relativo quebrado.")
    return 0


if __name__ == "__main__":
    sys.exit(main())
