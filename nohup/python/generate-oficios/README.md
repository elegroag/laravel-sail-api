# Generate Oficios PDF

Comando para generar PDFs a partir de HTML

```sh
sudo apt install build-essential libcairo2 libpango-1.0-0 libpangocairo-1.0-0 libgdk-pixbuf2.0-0 libffi-dev shared-mime-info

curl -LsSf https://astral.sh/uv/install.sh | sh
uv venv
source .venv/bin/activate
python main.py
```

## Uso de UV environment manager

```sh
uv venv
uv pip install weasyprint python-dotenv
uv run python main.py
```

```toml
[project]
name = "html-to-pdf-converter"
version = "0.1.0"
requires-python = ">=3.8"
dependencies = [
    "weasyprint",
    "python-dotenv",
]
```

## Ejemplo de estructura de proyecto

tu_proyecto/
├── .env
├── pyproject.toml (opcional, pero recomendado)
├── main.py
├── templates/
│ └── solicitud.html
└── output/ (se creará al ejecutar)

```sh
uv venv
uv pip install weasyprint python-dotenv
uv run python main.py
```

### Con valores por defecto (.env)

```sh
uv run python main.py
```

### Con rutas personalizadas

```sh
uv run python main.py ./data/form.html ./exports/form_2025.pdf
```
