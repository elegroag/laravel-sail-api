#!/usr/bin/env python3
"""
Convierte HTML a PDF usando WeasyPrint.
Soporta argumentos CLI o valores por defecto desde .env.
Incluye manejo detallado de errores.

Uso:
    uv run python main.py
    uv run python main.py entrada.html salida.pdf
"""

import os
import sys
import base64
import json
from pathlib import Path
from dotenv import load_dotenv
from weasyprint import HTML
from jinja2 import Template


def emit_json(success, message, **data):
    payload = {"success": bool(success), "message": str(message)}
    if data:
        payload.update(data)
    try:
        print(json.dumps(payload, ensure_ascii=False))
    except Exception:
        # Fallback mínimo si algo no serializa
        print(json.dumps({"success": False, "message": "Error al serializar respuesta"}))

def main():
    try:
        # Cargar variables de entorno (solo para valores por defecto)
        env_path = Path(__file__).with_name('.env')
        load_dotenv(env_path)
        template_path = os.getenv("TEMPLATE_PATH")
        output_path = os.getenv("OUTPUT_PATH")

        # Determinar rutas: CLI tiene prioridad
        if len(sys.argv) == 4:
            # Si existen rutas base en .env, combinar; de lo contrario usar los argumentos tal cual
            if template_path:
                template_path = os.path.join(template_path, sys.argv[1])
            else:
                template_path = sys.argv[1]

            if output_path:
                output_path = os.path.join(output_path, sys.argv[2])
            else:
                output_path = sys.argv[2]
            b64_str = sys.argv[3]
            try:
                json_bytes = base64.b64decode(b64_str, validate=True)
                json_str = json_bytes.decode('utf-8')
                context = json.loads(json_str)
                if not isinstance(context, dict):
                    raise ValueError("El JSON decodificado debe ser un objeto (dict).")
            except (base64.binascii.Error, UnicodeDecodeError, json.JSONDecodeError) as e:
                raise ValueError(f"Error al decodificar o parsear el JSON en base64: {e}")


            if not template_path or not output_path:
                emit_json(False, "Faltan rutas: TEMPLATE_PATH u OUTPUT_PATH no definidas y no se suministraron argumentos válidos")
                sys.exit(1)
        else:
            emit_json(False, "Uso inválido", hint="python main.py [input.html] [output.pdf] [context_base64]")
            sys.exit(1)

        # Validar que el archivo de entrada exista
        if not os.path.isfile(template_path):
            raise FileNotFoundError(f"Archivo de plantilla no encontrado: {template_path}")

        # Asegurar que el directorio de salida exista
        output_dir = Path(output_path).parent
        try:
            output_dir.mkdir(parents=True, exist_ok=True)
        except OSError as e:
            raise OSError(f"No se pudo crear el directorio de salida '{output_dir}': {e}")

        # Generar PDF
        try:
            # Renderizar template
            with open(template_path, 'r', encoding='utf-8') as f:
                template_content = f.read()

            rendered_html = Template(template_content).render(**context)

            # Asegurar directorio de salida
            Path(output_path).parent.mkdir(parents=True, exist_ok=True)

            # Generar PDF con base_url para imágenes
            base_url = Path(template_path).parent
            HTML(string=rendered_html, base_url=str(base_url)).write_pdf(output_path)
        except Exception as e:
            raise RuntimeError(f"Error al renderizar el PDF desde '{template_path}': {e}")

        emit_json(True, "PDF generado exitosamente", output=str(output_path))

    except FileNotFoundError as e:
        emit_json(False, f"Archivo no encontrado: {e}")
        sys.exit(1)
    except OSError as e:
        emit_json(False, f"Error de sistema (permisos, disco, ruta inválida): {e}")
        sys.exit(1)
    except RuntimeError as e:
        emit_json(False, f"Error de renderizado: {e}")
        sys.exit(1)
    except Exception as e:
        emit_json(False, f"Error inesperado: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main()

""""
Pruebas de uso:
    # Usando valores por defecto en .env
    uv run python main.py

    # Especificando rutas y contexto JSON en base64

    echo -n '{"razon":"empresa", "direccion":"C 12 CALLE 12", "barrio":"CENTRO", "ciudad":"MEDELLIN", "coddoc":"1", "tipper":"N", "tipemp":"P"}' | base64

    uv run python /var/www/html/nohup/python/generate-oficios/main.py \
        empresa.html \
        empresa.pdf \
        eyJyYXpvbiI6ImVtcHJlc2EiLCAiZGlyZWNjaW9uIjoiQyAxMiBDQUxMRSAxMiIsICJiYXJyaW8iOiJDRU5UUk8iLCAiY2l1ZGFkIjoiTUVERUxMSU4iLCAiY29kZG9jIjoiMSIsICJ0aXBwZXIiOiJOIiwgInRpcGVtcCI6IlAifQ==
"""
