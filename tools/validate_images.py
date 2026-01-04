#!/usr/bin/env python3
"""
validate_images.py

Checks that files listed in IMAGE_ASSETS.md exist and (optionally) reports image dimensions.
Usage:
  python tools/validate_images.py [--check-dimensions]

Requires: pillow (optional, only for dimension checks)
"""
import os
import re
import sys
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
MANIFEST = ROOT / 'IMAGE_ASSETS.md'

if not MANIFEST.exists():
    print('ERROR: IMAGE_ASSETS.md not found at', MANIFEST)
    sys.exit(2)

with MANIFEST.open('r', encoding='utf-8') as f:
    content = f.read()

# Find paths like assets/img/...
paths = re.findall(r"(assets/img/[A-Za-z0-9_\-\/\.]+)", content)
paths = list(dict.fromkeys(paths))  # unique

print(f'Found {len(paths)} image paths in IMAGE_ASSETS.md')

missing = []
present = []
for p in paths:
    full = ROOT / p
    if full.exists():
        present.append(full)
    else:
        missing.append(full)

if missing:
    print('\nMissing files:')
    for m in missing:
        print(' -', m)
else:
    print('\nAll files exist.')

check_dims = '--check-dimensions' in sys.argv
if check_dims:
    try:
        from PIL import Image
    except Exception:
        print('\nPillow not installed. Install via: pip install pillow')
        sys.exit(1)
    print('\nImage dimensions:')
    for p in present:
        try:
            with Image.open(p) as img:
                print(f' - {p.relative_to(ROOT)}: {img.width}x{img.height} ({img.format})')
        except Exception as e:
            print(f' - {p.relative_to(ROOT)}: ERROR reading ({e})')

if missing:
    sys.exit(1)
else:
    sys.exit(0)
