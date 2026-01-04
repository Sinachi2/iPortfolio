#!/usr/bin/env python3
"""
set_ga.py

Replace GA placeholder G-XXXXXXXXXX in index.html with a provided Measurement ID.
Usage:
  python tools/set_ga.py G-ABCDEFGHIJ
This script creates a backup of index.html as index.html.bak
"""
import sys
from pathlib import Path

if len(sys.argv) != 2:
    print('Usage: python tools/set_ga.py G-XXXXXXXXXX')
    sys.exit(2)

ga = sys.argv[1].strip()
if not ga.startswith('G-'):
    print('Measurement ID should start with G-')
    sys.exit(2)

ROOT = Path(__file__).resolve().parents[1]
INDEX = ROOT / 'index.html'
if not INDEX.exists():
    print('index.html not found at', INDEX)
    sys.exit(2)

bak = INDEX.with_suffix('.html.bak')
INDEX.replace(bak) if INDEX.exists() else None

text = bak.read_text(encoding='utf-8')
new_text = text.replace('G-XXXXXXXXXX', ga)
INDEX.write_text(new_text, encoding='utf-8')
print('Updated index.html with', ga)
print('Backup created at', bak)
