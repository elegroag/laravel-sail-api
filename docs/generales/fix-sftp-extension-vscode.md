# Fix: `TypeError: isDate is not a function` en extensión SFTP de VSCode

**Fecha:** 2026-08-24
**Extensión afectada:** `satiromarra.code-sftp` v1.18.1
**Dependencia afectada:** `ssh2` v1.13.0 (incluida en `node_modules` de la extensión)
**VSCode probado:** 1.134.0 (Node.js 18+)

---

## 1. Síntoma

Al intentar abrir o sincronizar cualquier archivo desde el host remoto aparece en el log de la extensión SFTP:

```
[error] TypeError: isDate is not a function
    at attrsToBytes (.../ssh2/lib/protocol/SFTP.js:2492:45)
    at SFTP.open (.../ssh2/lib/protocol/SFTP.js:331:15)
    ...
when remote ➞ local <archivo>
```

Adicionalmente se registran, antes del error, mensajes repetidos del tipo:

```
[info] Section for '172.168.0.15' not found with Host, try Match
```

---

## 2. Causa raíz

`ssh2@1.13.0` realiza al inicio de `lib/protocol/SFTP.js`:

```js
const { inherits, isDate } = require('util');
```

y luego invoca `isDate(...)` en varios lugares, por ejemplo:

```js
// SFTP.js:2492
if ((typeof attrs.atime === 'number' || isDate(attrs.atime))
    && (typeof attrs.mtime === 'number' || isDate(attrs.mtime))) {
```

El problema: **`util.isDate()` se eliminó de Node.js en la versión 10.0.0** (estaba deprecada desde 0.11.3). En Node 18+ (el que usa VSCode 1.134.0) `require('util').isDate` devuelve `undefined` y cualquier llamada posterior lanza `TypeError: isDate is not a function`.

El error se gatilló al:

1. Intentar un *transfer* `remote ➞ local` (descargar el archivo desde el servidor).
2. La librería ssh2 lee los atributos del archivo remoto (`attrsToBytes`).
3. Esos atributos incluyen `atime` y `mtime`, y al verificar su tipo, `isDate` falla.

---

## 3. Solución aplicada (polyfill)

Editar el archivo empaquetado dentro de la extensión:

**Ruta:**
```
~/.vscode/extensions/satiromarra.code-sftp-1.18.1/node_modules/ssh2/lib/protocol/SFTP.js
```

**Cambio:** reemplazar la línea 10

```diff
- const { inherits, isDate } = require('util');
+ const { inherits } = require('util');
+ // Polyfill: util.isDate() se eliminó de Node.js en v10.0.0.
+ // La extensión code-sftp 1.18.1 incluye ssh2@1.13.0, que aún la requiere.
+ const isDate = (v) => v instanceof Date;
```

`v instanceof Date` es el chequeo canónico en Node.js moderno (no depende de APIs removidas) y se comporta idénticamente a la antigua `util.isDate` para los usos que ssh2 le da.

### Verificación

```bash
node --check ~/.vscode/extensions/satiromarra.code-sftp-1.18.1/node_modules/ssh2/lib/protocol/SFTP.js
# → SYNTAX OK
```

Prueba funcional del polyfill:

```bash
node -e "const isDate = (v) => v instanceof Date; \
         console.log(isDate(new Date()));   // true \
         console.log(isDate(123));         // false"
```

### Activar el fix

Recargar VSCode para que tome efecto:

```
Ctrl + Shift + P  →  "Developer: Reload Window"
```

---

## 4. Warnings relacionados: "Section for host not found"

Estos mensajes **no son fatales**; ssh2 los emite al parsear `~/.ssh/config`. Indican que el host definido por IP en el workspace (`api.code-workspace`) **no tiene una entrada `Host ...`** en el config.

Solución: agregar en `~/.ssh/config`:

```sshconfig
Host 172.168.0.15
    HostName 172.168.0.15
    User root
    Port 22
    HostKeyAlgorithms +ssh-rsa
    KexAlgorithms +diffie-hellman-group1-sha1
    Ciphers +aes128-cbc,3des-cbc
    PubkeyAcceptedAlgorithms +ssh-rsa
```

Las directivas `+ssh-rsa`, `+diffie-hellman-group1-sha1`, etc., son necesarias porque algunos servidores legacy (como los de este entorno) todavía negocian algoritmos antiguos.

---

## 5. Trabajo relacionado: `api.code-workspace` vacío

El archivo `api.code-workspace` en el proyecto había quedado vacío (0 bytes). La extensión SFTP usa ese archivo como configuración de perfiles remotos y, al estar vacío, cualquier intento de *transfer* sobre él vuelve a caer en `attrsToBytes`.

Recomendaciones:

- Mantener una copia de respaldo del archivo de workspace con, al menos, un perfil SFTP válido.
- Verificar su contenido periódicamente; la extensión no lo regenera automáticamente.

Estructura mínima válida:

```json
{
  "folders": [
    { "path": "." }
  ],
  "settings": {
    "uploadOnSave": false,
    "ignore": [
      ".git", "vendor", "node_modules", ".env", "*.log"
    ]
  }
}
```

---

## 6. Durabilidad y alternativas

El polyfill vive **dentro de `node_modules` de la extensión**. Se borrará si:

- VSCode reinstala o actualiza la extensión.
- Se ejecuta `Extensions: Reinstall Extension`.

Para algo más robusto:

| Opción | Pros | Contras |
|--------|------|---------|
| **Actualizar la extensión** `satiromarra.code-sftp` | Solución mantenida por el autor | No hay garantía de que el changelog haya migrado a `ssh2` ≥ 1.14 |
| **Cambiar a `liximomo.sftp`** | Mantiene `ssh2` actualizado, configuración muy similar | Re-mapeo de perfiles |
| **Cambiar a `vscode-sftp`** | Activa, buen mantenimiento | UI y opciones diferentes |
| **Mantener el polyfill vía script** | Repetible tras actualizaciones | Requiere ejecutarlo cada vez que se reinstale |

### Script reproducible (opcional)

Para reaplicar el parche automáticamente tras cualquier actualización:

```bash
#!/usr/bin/env bash
set -euo pipefail

EXT_DIR="$HOME/.vscode/extensions/satiromarra.code-sftp-1.18.1"
TARGET="$EXT_DIR/node_modules/ssh2/lib/protocol/SFTP.js"

if [[ ! -f "$TARGET" ]]; then
  echo "No se encontró $TARGET" >&2
  exit 1
fi

if grep -q "Polyfill: util.isDate" "$TARGET"; then
  echo "Polyfill ya aplicado, nada que hacer."
  exit 0
fi

sed -i "s|const { inherits, isDate } = require('util');|const { inherits } = require('util');\n// Polyfill: util.isDate() se eliminó de Node.js en v10.0.0.\nconst isDate = (v) => v instanceof Date;|" "$TARGET"

echo "Polyfill aplicado en $TARGET"
```

Guardar como `scripts/fix-sftp-extension.sh` y ejecutar tras cada actualización de la extensión.

---

## 7. Verificación end-to-end

Después de aplicar el fix:

1. Recargar VSCode.
2. Abrir un archivo del workspace que exista en el host remoto.
3. Verificar que la transferencia `remote ➞ local` se completa sin `TypeError`.
4. Confirmar en el log que no aparecen nuevas líneas `Section for ... not found` (si agregaste la entrada SSH).

Si todo sale bien, los logs deberían mostrar únicamente líneas `[info]` normales de transferencia y no más `[error]`.

---

## 8. Referencias

- Node.js — [`util.isDate()` removal](https://nodejs.org/api/deprecations.html#DEP0009)
- `ssh2` releases: https://github.com/mscdex/ssh2/releases (≥ 1.14 elimina la dependencia de `util.isDate`)
- Extensión: https://marketplace.visualstudio.com/items?itemName=satiromarra.code-sftp