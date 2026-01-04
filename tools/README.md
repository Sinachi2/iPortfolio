Tools README

validate_images.py - checks files listed in IMAGE_ASSETS.md exist and optionally prints image dimensions.

Requirements:
- Python 3.8+
- (Optional) Pillow for dimensions: `pip install -r tools/requirements.txt`

Usage:
- Check existence only:

```bash
python tools/validate_images.py
```

- Check existence + dimensions:

```bash
pip install -r tools/requirements.txt
python tools/validate_images.py --check-dimensions
```

set_ga.py - safely replace GA placeholder in `index.html`:

```bash
python tools/set_ga.py G-XXXXXXXXXX
```

Notes:
- `tools/set_ga.py` will back up `index.html` to `index.html.bak` before replacing.
- `forms/contact.php` reads env vars; set them on your hosting panel or via `.htaccess`/server config.
