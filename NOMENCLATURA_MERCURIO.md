# Definición de Modelos Mercurio (Configuración y Parámetros)

## Modelo Mercurio01 (Parámetros de Aplicación)

### Descripción

Almacena la configuración global para diferentes aplicaciones o módulos del sistema, como credenciales de correo y detalles de servidores FTP.

### Campos

| Campo      | Tipo   | Descripción                               |
| ---------- | ------ | ----------------------------------------- |
| codapl     | string | Código único de la aplicación (PK)        |
| email      | string | Dirección de correo electrónico asociada  |
| clave      | string | Contraseña para el correo electrónico     |
| path       | string | Ruta de archivos local para la aplicación |
| ftpserver  | string | Dirección del servidor FTP                |
| pathserver | string | Ruta en el servidor FTP                   |
| userserver | string | Usuario para la conexión FTP              |
| passserver | string | Contraseña para la conexión FTP           |

### Relaciones

No se observan relaciones directas con otros modelos.

### Vistas Relacionadas

No se han identificado vistas que utilicen este modelo directamente. Su uso parece estar limitado al backend para tareas de configuración.

---

## Modelo Mercurio02 (Datos de Cajas de Compensación)

### Descripción

Contiene la información de contacto y datos básicos de diferentes cajas de compensación familiar.

### Campos

| Campo   | Tipo   | Descripción                                  |
| ------- | ------ | -------------------------------------------- |
| codcaj  | string | Código único de la caja de compensación (PK) |
| nit     | string | NIT de la caja de compensación               |
| razsoc  | string | Razón social                                 |
| sigla   | string | Sigla o nombre corto                         |
| email   | string | Correo electrónico de contacto               |
| direccion | string | Dirección física                             |
| telefono| string | Teléfono de contacto                         |
| codciu  | string | Código de la ciudad                          |
| pagweb  | string | URL de la página web                         |
| pagfac  | string | URL de la página de Facebook                 |
| pagtwi  | string | URL de la página de Twitter                  |
| pagyou  | string | URL del canal de YouTube                     |

### Relaciones

- `codciu` podría tener una relación con un modelo de ciudades (ej. `Gener09`).

### Vistas Relacionadas

No se han identificado vistas que utilicen este modelo directamente. Es probable que se use para mostrar información de contacto en reportes o plantillas de correo.

---

## Modelo Mercurio03 (Firmas Autorizadas)

### Descripción

Gestiona la información de las personas autorizadas para firmar documentos o reportes, incluyendo el archivo de la firma digitalizada.

### Campos

| Campo   | Tipo   | Descripción                               |
| ------- | ------ | ----------------------------------------- |
| codfir  | string | Código único de la firma (PK)             |
| nombre  | string | Nombre completo de la persona que firma   |
| cargo   | string | Cargo de la persona                       |
| archivo | string | Nombre del archivo de imagen de la firma  |
| email   | string | Correo electrónico de la persona          |

### Relaciones

No se observan relaciones directas con otros modelos.

### Vistas Relacionadas

Utilizado en la generación de reportes y documentos que requieren una firma autorizada.

---

## Modelo Mercurio04 (Oficinas)

### Descripción

Define las oficinas o sucursales de la entidad, indicando si son principales y su estado.

### Campos

| Campo     | Tipo   | Descripción                         |
| --------- | ------ | ----------------------------------- |
| codofi    | string | Código único de la oficina (PK)     |
| detalle   | string | Nombre o descripción de la oficina  |
| principal | string | Indica si es la oficina principal (S/N) |
| estado    | string | Estado de la oficina (A/I)          |

### Relaciones

No se observan relaciones directas con otros modelos.

### Vistas Relacionadas

Puede ser utilizado en componentes de selección de sucursales o para mostrar información de contacto.

---

## Modelo Mercurio06 (Tipos de Empresa)

### Descripción

Catálogo para clasificar los tipos de empresa.

### Campos

| Campo   | Tipo   | Descripción                       |
| ------- | ------ | --------------------------------- |
| tipo    | string | Código del tipo de empresa (PK)   |
| detalle | string | Nombre del tipo de empresa        |

### Relaciones

No se observan relaciones directas con otros modelos, pero es probable que sea usado en `Mercurio30` (Empresas).

### Vistas Relacionadas

Utilizado en formularios de registro de empresas para seleccionar el tipo.

---

## Modelo Mercurio09 (Tipos de Procesos y Certificados)

### Descripción

Define los diferentes tipos de procesos o certificados que se pueden solicitar en el sistema, y los días de vigencia o plazo.

### Campos

| Campo  | Tipo   | Descripción                               |
| ------ | ------ | ----------------------------------------- |
| tipopc | string | Código del tipo de proceso/certificado (PK) |
| detalle| string | Nombre o descripción del proceso          |
| dias   | number | Días de vigencia o plazo para el proceso  |

### Relaciones

- Se relaciona con `Mercurio13` y `Mercurio14` para definir los documentos requeridos por cada tipo de proceso.

### Vistas Relacionadas

Utilizado en los módulos de solicitud de certificados y otros trámites para identificar el tipo de solicitud.

---

## Modelo Mercurio11 (Estados de Solicitud)

### Descripción

Catálogo de los posibles estados que puede tener una solicitud o trámite.

### Campos

| Campo   | Tipo   | Descripción                     |
| ------- | ------ | ------------------------------- |
| codest  | string | Código del estado (PK)          |
| detalle | string | Descripción del estado          |

### Relaciones

Este modelo es referenciado por múltiples tablas de `Mercurio` que gestionan solicitudes para registrar el estado de un trámite (ej. `Mercurio30`, `Mercurio31`, etc.).

### Vistas Relacionadas

Se utiliza en las vistas de consulta de solicitudes para mostrar el estado actual de un trámite.

---

## Modelo Mercurio12 (Catálogo de Documentos)

### Descripción

Maestro de todos los posibles documentos que se pueden solicitar o adjuntar en los diferentes trámites.

### Campos

| Campo  | Tipo   | Descripción                     |
| ------ | ------ | ------------------------------- |
| coddoc | number | Código del documento (PK)       |
| detalle| string | Nombre del documento            |

### Relaciones

- Se relaciona con `Mercurio13` y `Mercurio14` para especificar qué documentos son necesarios para cada tipo de proceso.

### Vistas Relacionadas

Utilizado en los formularios de solicitud para listar los documentos que el usuario debe adjuntar.

---

## Modelo Mercurio13 (Documentos por Tipo de Proceso - General)

### Descripción

Tabla de cruce que define qué documentos (`Mercurio12`) son requeridos para un tipo de proceso específico (`Mercurio09`). Esta tabla parece aplicar a personas naturales.

### Campos

| Campo         | Tipo   | Descripción                                    |
| ------------- | ------ | ---------------------------------------------- |
| id            | number | Identificador único (PK)                       |
| tipopc        | string | Código del tipo de proceso (FK `Mercurio09`)   |
| coddoc        | number | Código del documento (FK `Mercurio12`)         |
| obliga        | string | Indica si el documento es obligatorio (S/N)    |
| auto_generado | string | Indica si el documento es generado por el sistema |

### Relaciones

- `mercurio09()`: Pertenece a un `Mercurio09`.
- `mercurio12()`: Pertenece a un `Mercurio12`.

### Vistas Relacionadas

Su lógica se aplica en el backend para validar los documentos adjuntados en una solicitud.

---

## Modelo Mercurio14 (Documentos por Tipo de Proceso y Sociedad)

### Descripción

Define los documentos (`Mercurio12`) requeridos para un tipo de proceso (`Mercurio09`) según el tipo de sociedad del solicitante. Aplica a empresas.

### Campos

| Campo         | Tipo   | Descripción                                    |
| ------------- | ------ | ---------------------------------------------- |
| tipopc        | string | Código del tipo de proceso (PK, FK `Mercurio09`) |
| tipsoc        | string | Código del tipo de sociedad (PK, FK `Subsi54`) |
| coddoc        | number | Código del documento (PK, FK `Mercurio12`)     |
| obliga        | string | Indica si el documento es obligatorio (S/N)    |
| auto_generado | string | Indica si el documento es autogenerado         |
| nota          | string | Anotaciones o aclaraciones sobre el documento  |

### Relaciones

- `mercurio09()`: Pertenece a un `Mercurio09`.
- `mercurio12()`: Pertenece a un `Mercurio12`.
- `subsi54()`: Pertenece a un `Subsi54` (modelo no analizado, pero se infiere que es el catálogo de tipos de sociedad).

### Vistas Relacionadas

Se utiliza en el backend para determinar y validar los documentos requeridos en trámites de empresas.

---

## Modelo Mercurio18 (Catálogo General)

### Descripción

Un catálogo genérico para almacenar listas de valores simples con un código y una descripción.

### Campos

| Campo   | Tipo   | Descripción                     |
| ------- | ------ | ------------------------------- |
| id      | number | Identificador único (PK)        |
| codigo  | string | Código del ítem del catálogo    |
| detalle | string | Descripción o valor del ítem    |

### Relaciones

No se observan relaciones directas, pero puede ser utilizado por varios otros modelos para poblar listas desplegables.

### Vistas Relacionadas

Usado en diversos formularios como fuente de datos para componentes de selección.

---

## Modelo Mercurio45 (Certificados Laborales)

### Descripción

Registra la generación de certificados laborales para trabajadores.

### Campos

| Campo   | Tipo   | Descripción                               |
| ------- | ------ | ----------------------------------------- |
| id      | number | Identificador único (PK)                  |
| log     | number | Número de log                             |
| cedtra  | string | Cédula del trabajador                     |
| codben  | number | Código de beneficiario                     |
| nombre  | string | Nombre del beneficiario                   |
| fecha   | date   | Fecha de generación                       |
| codcer  | string | Código del certificado                    |
| nomcer  | string | Nombre del certificado                    |
| archivo | string | Archivo del certificado                   |
| usuario | number | Usuario que generó                        |
| estado  | string | Estado del certificado                    |
| motivo  | string | Motivo                                    |
| fecest  | date   | Fecha del estado                          |
| codest  | string | Código de estado (FK Mercurio11)          |
| tipo    | string | Tipo de usuario (FK Mercurio07)           |
| coddoc  | string | Código de documento (FK Mercurio07)       |
| documento| string | Número de documento (FK Mercurio07)       |
| fecsol  | date   | Fecha de solicitud                        |

### Relaciones

- `mercurio07()`: Usuario que generó el certificado.
- `mercurio11()`: Estado del certificado.

### Vistas Relacionadas

Utilizado en la generación y descarga de certificados laborales.

---

## Modelo Mercurio46 (Archivos del Sistema)

### Descripción

Almacena archivos generados por el sistema, como reportes o documentos oficiales.

### Campos

| Campo   | Tipo   | Descripción                               |
| ------- | ------ | ----------------------------------------- |
| id      | number | Identificador único (PK)                  |
| log     | number | Número de log                             |
| nit     | string | NIT relacionado                           |
| fecsis  | date   | Fecha del sistema                         |
| archivo | string | Nombre del archivo                        |

### Relaciones

No se observan relaciones directas.

### Vistas Relacionadas

Utilizado para almacenar archivos generados automáticamente.

---

## Modelo Mercurio47 (Estados de Solicitudes por Usuario)

### Descripción

Registra cambios de estado en solicitudes específicas de usuarios.

### Campos

| Campo     | Tipo   | Descripción                               |
| --------- | ------ | ----------------------------------------- |
| id        | number | Identificador único (PK)                  |
| documento | string | Número de documento del usuario           |
| tipo      | string | Tipo de usuario (FK Mercurio07)           |
| coddoc    | string | Código de documento (FK Mercurio07)       |
| estado    | string | Estado de la solicitud                    |
| fecest    | date   | Fecha del estado                          |
| codest    | string | Código de estado (FK Mercurio11)          |
| tipact    | string | Tipo de acción                            |
| usuario   | number | Usuario que realizó el cambio             |
| fecsol    | date   | Fecha de solicitud                        |
| fecapr    | date   | Fecha de aprobación                       |
| ruuid     | uuid   | UUID único de la solicitud                |

### Relaciones

- `mercurio07()`: Usuario de la solicitud.
- `mercurio11()`: Estado de la solicitud.

### Vistas Relacionadas

Utilizado en el seguimiento de estados de solicitudes individuales.

---

## Modelo Mercurio50 (Configuración de Aplicaciones)

### Descripción

Configuración específica para diferentes aplicaciones del sistema.

### Campos

| Campo   | Tipo   | Descripción                               |
| ------- | ------ | ----------------------------------------- |
| codapl  | string | Código de aplicación (PK)                 |
| webser  | string | Servicio web                              |
| path    | string | Ruta del archivo                          |
| urlonl  | string | URL online                                |
| puncom  | number | Punto de comunicación                     |

### Relaciones

No se observan relaciones directas.

### Vistas Relacionadas

Utilizado para configuración de aplicaciones específicas.

---

## Modelo Mercurio51 (Categorías del Sistema)

### Descripción

Catálogo de categorías utilizadas en el sistema.

### Campos

| Campo   | Tipo   | Descripción                               |
| ------- | ------ | ----------------------------------------- |
| codcat  | number | Código de categoría (PK)                  |
| detalle | string | Descripción de la categoría               |
| tipo    | string | Tipo de categoría                         |
| estado  | enum   | Estado (A/I - Activo/Inactivo)            |

### Relaciones

No se observan relaciones directas.

### Vistas Relacionadas

Utilizado en clasificaciones y categorizaciones del sistema.

---

## Modelo Mercurio52 (Menús del Sistema)

### Descripción

Define la estructura de menús del sistema de navegación.

### Campos

| Campo   | Tipo   | Descripción                               |
| ------- | ------ | ----------------------------------------- |
| codmen  | number | Código del menú (PK)                      |
| detalle | string | Descripción del menú                      |
| codare  | number | Código de área (FK Mercurio55)            |
| url     | string | URL del menú                              |
| tipo    | string | Tipo de menú                              |
| estado  | enum   | Estado (A/I - Activo/Inactivo)            |

### Relaciones

- `mercurio55()`: Área a la que pertenece el menú.

### Vistas Relacionadas

Utilizado en la construcción de la navegación del sistema.

---

## Modelo Mercurio53 (Promociones)

### Descripción

Gestiona las promociones y banners del sistema.

### Campos

| Campo   | Tipo   | Descripción                               |
| ------- | ------ | ----------------------------------------- |
| numero  | number | Número de promoción (PK)                  |
| archivo | string | Archivo de la promoción                   |
| orden   | number | Orden de presentación                     |
| url     | string | URL de destino                            |

### Relaciones

No se observan relaciones directas.

### Vistas Relacionadas

Utilizado para mostrar promociones en la interfaz.

---

## Modelo Mercurio54 (Tokens de Usuario)

### Descripción

Almacena tokens de autenticación para usuarios.

### Campos

| Campo     | Tipo   | Descripción                               |
| --------- | ------ | ----------------------------------------- |
| tipo      | string | Tipo de usuario (PK, FK Mercurio07)       |
| coddoc    | string | Código de documento (PK, FK Mercurio07)   |
| documento | string | Número de documento (PK, FK Mercurio07)   |
| token     | string | Token de autenticación                    |
| tokencel  | string | Token para celular                        |
| tiptra    | string | Tipo de transacción                       |
| codtra    | string | Código de transacción                     |
| doctra    | string | Documento de transacción                  |

### Relaciones

- `mercurio07()`: Usuario del token.

### Vistas Relacionadas

Utilizado en procesos de autenticación móvil.

---

## Modelo Mercurio55 (Áreas del Sistema)

### Descripción

Define las áreas funcionales del sistema.

### Campos

| Campo   | Tipo   | Descripción                               |
| ------- | ------ | ----------------------------------------- |
| codare  | number | Código de área (PK)                       |
| detalle | string | Descripción del área                      |
| codcat  | number | Código de categoría (FK Mercurio51)       |
| tipo    | string | Tipo de área                              |
| estado  | enum   | Estado (A/I - Activo/Inactivo)            |

### Relaciones

- `mercurio51()`: Categoría a la que pertenece el área.

### Vistas Relacionadas

Utilizado en la organización funcional del sistema.

---

## Modelo Mercurio56 (Información de Contacto)

### Descripción

Almacena información de contacto para diferentes entidades.

### Campos

| Campo   | Tipo   | Descripción                               |
| ------- | ------ | ----------------------------------------- |
| codinf  | string | Código de información (PK)                |
| archivo | string | Archivo adjunto                           |
| email   | string | Correo electrónico                        |
| telefono| string | Teléfono de contacto                      |
| nota    | string | Nota adicional                            |
| estado  | enum   | Estado (A/I - Activo/Inactivo)            |

### Relaciones

No se observan relaciones directas.

### Vistas Relacionadas

Utilizado en secciones de contacto e información.

---

## Modelo Mercurio57 (Promociones de Turismo)

### Descripción

Gestiona promociones específicas del sector turismo.

### Campos

| Campo   | Tipo   | Descripción                               |
| ------- | ------ | ----------------------------------------- |
| numtur  | number | Número de promoción turismo (PK)          |
| archivo | string | Archivo de la promoción                   |
| orden   | number | Orden de presentación                     |
| url     | string | URL de destino                            |
| estado  | string | Estado de la promoción                    |

### Relaciones

No se observan relaciones directas.

### Vistas Relacionadas

Utilizado para mostrar promociones de turismo.

---

## Modelo Mercurio58 (Archivos por Área)

### Descripción

Asocia archivos a diferentes áreas del sistema.

### Campos

| Campo   | Tipo   | Descripción                               |
| ------- | ------ | ----------------------------------------- |
| numero  | number | Número de archivo (PK)                    |
| archivo | string | Nombre del archivo                        |
| orden   | number | Orden de presentación                     |
| codare  | number | Código de área (FK Mercurio55)            |

### Relaciones

- `mercurio55()`: Área a la que pertenece el archivo.

### Vistas Relacionadas

Utilizado para organizar archivos por áreas.

---

## Modelo Mercurio59 (Servicios por Información)

### Descripción

Define servicios asociados a información de contacto.

### Campos

| Campo     | Tipo   | Descripción                               |
| --------- | ------ | ----------------------------------------- |
| codinf    | string | Código de información (PK, FK Mercurio56) |
| codser    | string | Código de servicio (PK)                   |
| numero    | number | Número de servicio (PK)                   |
| archivo   | string | Archivo adjunto                           |
| nota      | string | Nota del servicio                         |
| email     | string | Correo electrónico                        |
| precan    | enum   | Precancelable (S/N)                       |
| autser    | enum   | Auto servicio (S/N)                       |
| consumo   | enum   | Consumo                                   |
| estado    | enum   | Estado (A/I - Activo/Inactivo)            |
| fecini    | date   | Fecha de inicio                           |
| fecfin    | date   | Fecha de fin                              |

### Relaciones

- `mercurio56()`: Información de contacto relacionada.

### Vistas Relacionadas

Utilizado en la gestión de servicios.

---

## Modelo Mercurio60 (Transacciones de Puntos)

### Descripción

Registra transacciones de puntos en el sistema de fidelización.

### Campos

| Campo     | Tipo   | Descripción                               |
| --------- | ------ | ----------------------------------------- |
| id        | number | Identificador único (PK)                  |
| codinf    | string | Código de información                     |
| codser    | string | Código de servicio                        |
| numero    | number | Número de transacción                     |
| tipo      | string | Tipo de usuario                           |
| documento | string | Número de documento                       |
| coddoc    | string | Código de documento                       |
| codcat    | string | Código de categoría                       |
| valtot    | number | Valor total                               |
| fecsis    | date   | Fecha del sistema                         |
| hora      | string | Hora de la transacción                    |
| tipmov    | enum   | Tipo de movimiento                        |
| online    | number | Indicador online                          |
| consumo   | string | Consumo                                   |
| feccon    | date   | Fecha de consumo                          |
| punuti    | number | Puntos utilizados                         |
| puntos    | number | Puntos de la transacción                  |
| estado    | enum   | Estado (A/P - Activo/Pendiente)           |

### Relaciones

No se observan relaciones directas.

### Vistas Relacionadas

Utilizado en el sistema de puntos y fidelización.

---

## Modelo Mercurio61 (Detalle de Transacciones)

### Descripción

Detalle de productos o servicios en transacciones de puntos.

### Campos

| Campo     | Tipo   | Descripción                               |
| --------- | ------ | ----------------------------------------- |
| numero    | number | Número de transacción (PK, FK Mercurio60) |
| item      | number | Número de ítem (PK)                       |
| tipo      | string | Tipo de usuario                           |
| documento | string | Número de documento                       |
| cantidad  | number | Cantidad                                  |
| valor     | number | Valor                                     |

### Relaciones

- `mercurio60()`: Transacción padre.

### Vistas Relacionadas

Utilizado para detallar transacciones de puntos.

---

## Modelo Mercurio62 (Saldo de Puntos)

### Descripción

Mantiene el saldo de puntos de los usuarios.

### Campos

| Campo     | Tipo   | Descripción                               |
| --------- | ------ | ----------------------------------------- |
| tipo      | string | Tipo de usuario (PK)                      |
| documento | string | Número de documento (PK)                  |
| coddoc    | string | Código de documento (PK)                  |
| salgir    | number | Saldo de giro                             |
| salrec    | number | Saldo recibido                            |
| consumo   | number | Consumo                                   |
| puntos    | number | Puntos totales                            |
| punuti    | number | Puntos utilizados                         |

### Relaciones

No se observan relaciones directas.

### Vistas Relacionadas

Utilizado para consultar saldos de puntos.

---

## Modelo Mercurio63 (Movimientos de Puntos)

### Descripción

Registra movimientos de puntos (giros, transferencias, etc.).

### Campos

| Campo     | Tipo   | Descripción                               |
| --------- | ------ | ----------------------------------------- |
| numero    | number | Número de movimiento (PK)                 |
| tipo      | string | Tipo de usuario                           |
| documento | string | Número de documento                       |
| coddoc    | string | Código de documento                       |
| detalle   | string | Detalle del movimiento                    |
| tipmov    | enum   | Tipo de movimiento (S/R)                  |
| movimiento| number | Valor del movimiento                      |
| valor     | number | Valor monetario                           |
| hora      | string | Hora del movimiento                       |
| fecsis    | date   | Fecha del sistema                         |
| estado    | string | Estado del movimiento                     |

### Relaciones

No se observan relaciones directas.

### Vistas Relacionadas

Utilizado en reportes de movimientos de puntos.

---

## Modelo Mercurio64 (Transferencias de Puntos)

### Descripción

Registra transferencias de puntos entre usuarios.

### Campos

| Campo     | Tipo   | Descripción                               |
| --------- | ------ | ----------------------------------------- |
| numero    | number | Número de transferencia (PK)              |
| tipo      | string | Tipo de usuario                           |
| documento | string | Número de documento                       |
| coddoc    | string | Código de documento                       |
| tipmov    | enum   | Tipo de movimiento                        |
| pergir    | string | Período de giro                           |
| online    | number | Indicador online                          |
| transferencia| number | Valor de transferencia                 |
| valor     | number | Valor monetario                           |
| fecsis    | date   | Fecha del sistema                         |
| hora      | string | Hora de la transferencia                  |
| estado    | string | Estado de la transferencia                |

### Relaciones

No se observan relaciones directas.

### Vistas Relacionadas

Utilizado en transferencias de puntos.

---

## Modelo Mercurio65 (Sedes)

### Descripción

Gestiona las sedes físicas de la organización.

### Campos

| Campo     | Tipo   | Descripción                               |
| --------- | ------ | ----------------------------------------- |
| codsed    | number | Código de sede (PK)                       |
| nit       | string | NIT de la sede                            |
| razsoc    | string | Razón social                              |
| direccion | string | Dirección de la sede                      |
| email     | string | Correo electrónico                        |
| celular   | string | Celular de contacto                       |
| codcla    | number | Código de clasificación (FK Mercurio67)   |
| detalle   | string | Detalle de la sede                        |
| archivo   | string | Archivo adjunto                           |
| estado    | enum   | Estado (A/I - Activo/Inactivo)            |
| lat       | string | Latitud                                   |
| log       | string | Longitud                                  |

### Relaciones

- `mercurio67()`: Clasificación de la sede.

### Vistas Relacionadas

Utilizado en la gestión de sedes físicas.

---

## Modelo Mercurio66 (Reservas en Sedes)

### Descripción

Registra reservas de servicios en diferentes sedes.

### Campos

| Campo     | Tipo   | Descripción                               |
| --------- | ------ | ----------------------------------------- |
| numero    | number | Número de reserva (PK)                    |
| codsed    | number | Código de sede (FK Mercurio65)            |
| detalle   | string | Detalle de la reserva                     |
| valor     | number | Valor de la reserva                       |
| fecsis    | date   | Fecha del sistema                         |
| hora      | string | Hora de la reserva                        |
| estado    | enum   | Estado (C/P - Completado/Pendiente)       |
| fecest    | date   | Fecha del estado                          |
| tipo      | string | Tipo de usuario                           |
| documento | string | Número de documento                       |
| coddoc    | string | Código de documento                       |

### Relaciones

- `mercurio65()`: Sede donde se realiza la reserva.
- `mercurio62()`: Saldo de puntos del usuario.

### Vistas Relacionadas

Utilizado en el sistema de reservas.

---

## Modelo Mercurio67 (Clasificación de Sedes)

### Descripción

Catálogo de clasificaciones para sedes.

### Campos

| Campo   | Tipo   | Descripción                               |
| ------- | ------ | ----------------------------------------- |
| codcla  | number | Código de clasificación (PK)              |
| detalle | string | Descripción de la clasificación           |

### Relaciones

No se observan relaciones directas.

### Vistas Relacionadas

Utilizado para clasificar tipos de sedes.

---

## Modelo Mercurio68 (Transferencias de Beneficios)

### Descripción

Registra transferencias de beneficios entre usuarios.

### Campos

| Campo   | Tipo   | Descripción                               |
| ------- | ------ | ----------------------------------------- |
| numero  | number | Número de transferencia (PK)              |
| tipo    | string | Tipo de usuario origen                    |
| documento| string | Documento de origen                       |
| coddoc  | string | Código documento origen                   |
| tipben  | string | Tipo de beneficiario                      |
| docben  | string | Documento del beneficiario                |
| codben  | string | Código documento beneficiario              |
| valor   | number | Valor transferido                         |
| email   | string | Correo electrónico                        |
| fecsis  | date   | Fecha del sistema                         |
| hora    | string | Hora de la transferencia                  |

### Relaciones

No se observan relaciones directas.

### Vistas Relacionadas

Utilizado en transferencias de beneficios.

---

## Modelo Mercurio69 (Consumos de Puntos)

### Descripción

Registra consumos de puntos por servicios.

### Campos

| Campo   | Tipo   | Descripción                               |
| ------- | ------ | ----------------------------------------- |
| numero  | number | Número de consumo (PK)                    |
| tipo    | string | Tipo de usuario                           |
| documento| string | Número de documento                       |
| coddoc  | string | Código de documento                       |
| codser  | string | Código de servicio                        |
| puntos  | number | Puntos consumidos                         |
| fecsis  | date   | Fecha del sistema                         |
| hora    | string | Hora del consumo                          |

### Relaciones

No se observan relaciones directas.

### Vistas Relacionadas

Utilizado en el seguimiento de consumos de puntos.

---

## Modelo Mercurio70 (Calificaciones de Servicios)

### Descripción

Permite calificar servicios utilizados por los usuarios.

### Campos

| Campo     | Tipo   | Descripción                               |
| --------- | ------ | ----------------------------------------- |
| numero    | number | Número de calificación (PK)               |
| tipo      | string | Tipo de usuario                           |
| documento | string | Número de documento                       |
| coddoc    | string | Código de documento                       |
| codser    | string | Código de servicio                        |
| puntos    | number | Puntos asignados                          |
| calificacion| number| Calificación numérica                     |
| nota      | string | Nota de la calificación                   |
| fecsis    | date   | Fecha del sistema                         |
| hora      | string | Hora de la calificación                   |

### Relaciones

No se observan relaciones directas.

### Vistas Relacionadas

Utilizado en sistemas de calificación de servicios.

---

## Modelo Mercurio71 (Recuperación de Contraseña)

### Descripción

Gestiona el proceso de recuperación de contraseñas.

### Campos

| Campo     | Tipo   | Descripción                               |
| --------- | ------ | ----------------------------------------- |
| numero    | number | Número de recuperación (PK)               |
| tipo      | string | Tipo de usuario                           |
| documento | string | Número de documento                       |
| coddoc    | string | Código de documento                       |
| email     | string | Correo electrónico                        |
| estado    | enum   | Estado (P/A - Pendiente/Aprobado)         |
| fecsis    | date   | Fecha del sistema                         |
| hora      | string | Hora de la solicitud                      |
| fecest    | date   | Fecha del estado                          |
| codigo    | string | Código de verificación                    |

### Relaciones

No se observan relaciones directas.

### Vistas Relacionadas

Utilizado en el proceso de recuperación de contraseñas.

---

## Modelo Mercurio72 (Promociones de Turismo)

### Descripción

Gestiona promociones específicas del sector turismo.

### Campos

| Campo   | Tipo   | Descripción                               |
| ------- | ------ | ----------------------------------------- |
| numtur  | number | Número de promoción turismo (PK)          |
| archivo | string | Archivo de la promoción                   |
| orden   | number | Orden de presentación                     |
| url     | string | URL de destino                            |
| estado  | string | Estado de la promoción                    |

### Relaciones

No se observan relaciones directas.

### Vistas Relacionadas

Utilizado para mostrar promociones de turismo.

---

## Modelo Mercurio73 (Promociones de Educación)

### Descripción

Gestiona promociones específicas del sector educación.

### Campos

| Campo   | Tipo   | Descripción                               |
| ------- | ------ | ----------------------------------------- |
| numedu  | number | Número de promoción educación (PK)        |
| archivo | string | Archivo de la promoción                   |
| orden   | number | Orden de presentación                     |
| url     | string | URL de destino                            |
| estado  | string | Estado de la promoción                    |

### Relaciones

No se observan relaciones directas.

### Vistas Relacionadas

Utilizado para mostrar promociones de educación.

---

## Modelo Mercurio74 (Promociones de Recreación)

### Descripción

Gestiona promociones específicas del sector recreación.

### Campos

| Campo   | Tipo   | Descripción                               |
| ------- | ------ | ----------------------------------------- |
| numrec  | number | Número de promoción recreación (PK)       |
| archivo | string | Archivo de la promoción                   |
| orden   | number | Orden de presentación                     |
| url     | string | URL de destino                            |
| estado  | string | Estado de la promoción                    |

### Relaciones

No se observan relaciones directas.

### Vistas Relacionadas

Utilizado para mostrar promociones de recreación.

---

## Modelo Mercurio80 (Evaluaciones de Sedes)

### Descripción

Registra evaluaciones de sedes por profesores y colegios.

### Campos

| Campo     | Tipo   | Descripción                               |
| --------- | ------ | ----------------------------------------- |
| id        | number | Identificador único (PK)                  |
| profesor  | number | Código del profesor                       |
| colegio   | number | Código del colegio                        |
| modain    | number | Modalidad de ingreso                      |
| modser    | number | Modalidad de servicio                     |
| modjec    | number | Modalidad de jefe                        |
| fecha     | date   | Fecha de la evaluación                    |
| estado    | string | Estado de la evaluación                   |

### Relaciones

No se observan relaciones directas.

### Vistas Relacionadas

Utilizado en evaluaciones de sedes educativas.

---

## Modelo Mercurio82 (Proveedores)

### Descripción

Catálogo de proveedores del sistema.

### Campos

| Campo     | Tipo   | Descripción                               |
| --------- | ------ | ----------------------------------------- |
| id        | number | Identificador único (PK)                  |
| nombre    | string | Nombre del proveedor                      |
| direccion | string | Dirección del proveedor                   |
| telefono  | string | Teléfono de contacto                      |
| estado    | enum   | Estado (A/I - Activo/Inactivo)            |

### Relaciones

No se observan relaciones directas.

### Vistas Relacionadas

Utilizado en la gestión de proveedores.

---

## Modelo Mercurio83 (Beneficiarios Externos)

### Descripción

Registra beneficiarios externos con información detallada.

### Campos

| Campo     | Tipo   | Descripción                               |
| --------- | ------ | ----------------------------------------- |
| id        | number | Identificador único (PK)                  |
| tipben    | number | Tipo de beneficiario                      |
| tipideben | number | Tipo de identificación                    |
| numideben | string | Número de identificación                  |
| prinomben | string | Primer nombre                            |
| segnomben | string | Segundo nombre                            |
| priapeben | string | Primer apellido                           |
| segapeben | string | Segundo apellido                          |
| tipgenben | number | Tipo de género                            |
| fecnacben | date   | Fecha de nacimiento                       |
| codpaiben | number | Código de país                            |
| coddep_nac| string | Código departamento nacimiento            |
| ciunacben | string | Ciudad de nacimiento                      |
| fecafiben | date   | Fecha de afiliación                       |
| coddep_res| string | Código departamento residencia            |
| ciuresben | string | Ciudad de residencia                      |
| codareresben| number| Código área residencia                    |
| direccionben| string| Dirección de residencia                   |
| codgru    | number | Código de grupo                           |
| codpob    | number | Código de población                       |
| facvul    | number | Factor de vulnerabilidad                  |
| tipjor    | number | Tipo de jornada                           |
| fecina    | date   | Fecha de ingreso                          |
| motivo    | string | Motivo                                    |
| codres    | number | Código de responsable                     |
| codpue    | number | Código de pueblo                          |
| responsable| number| Responsable                               |
| nivedu    | number | Nivel educativo                           |
| codgra    | number | Código de grado                           |

### Relaciones

Referencia a múltiples catálogos externos (xml4b_*).

### Vistas Relacionadas

Utilizado en programas de beneficiarios externos.

---

## Modelo Mercurio85 (Acudientes de Beneficiarios)

### Descripción

Registra información de acudientes de beneficiarios.

### Campos

| Campo     | Tipo   | Descripción                               |
| --------- | ------ | ----------------------------------------- |
| id        | number | Identificador único (PK, FK Mercurio83)   |
| tipideacu | number | Tipo de identificación acudiente          |
| numideacu | string | Número identificación acudiente           |
| prinomacu | string | Primer nombre acudiente                   |
| segnomacu | string | Segundo nombre acudiente                  |
| priapeacu | string | Primer apellido acudiente                 |
| segapeacu | string | Segundo apellido acudiente                |
| telacu    | string | Teléfono acudiente                        |

### Relaciones

- `mercurio83()`: Beneficiario al que pertenece.

### Vistas Relacionadas

Utilizado en la gestión de acudientes de beneficiarios.

---

## Modelo Formularios Dinámicos

### Descripción

Gestiona formularios dinámicos que pueden ser configurados para diferentes módulos del sistema, permitiendo crear interfaces de usuario flexibles sin modificar código.

### Campos

| Campo         | Tipo   | Descripción                               |
| ------------- | ------ | ----------------------------------------- |
| id            | number | Identificador único (PK)                  |
| name          | string | Nombre único del formulario               |
| title         | string | Título del formulario                     |
| description   | text   | Descripción del formulario                |
| module        | string | Módulo al que pertenece                   |
| endpoint      | string | Endpoint para envío de datos              |
| method        | string | Método HTTP (default: POST)               |
| is_active     | boolean| Estado del formulario (default: true)     |
| layout_config | json   | Configuración de layout                   |
| permissions   | json   | Permisos del formulario                   |
| created_at    | timestamp| Fecha de creación                        |
| updated_at    | timestamp| Fecha de actualización                   |

### Relaciones

No se observan relaciones directas con otros modelos Mercurio.

### Vistas Relacionadas

Utilizado en la construcción de formularios dinámicos en la interfaz de usuario.

---

## Modelo Componentes Dinámicos

### Descripción

Define componentes individuales que pueden ser utilizados en formularios dinámicos, como inputs, selects, textareas, etc.

### Campos

| Campo         | Tipo   | Descripción                               |
| ------------- | ------ | ----------------------------------------- |
| id            | number | Identificador único (PK)                  |
| name          | string | Nombre único del componente               |
| type          | enum   | Tipo de componente (input/select/textarea/dialog/date/number) |
| label         | string | Etiqueta del componente                   |
| placeholder   | string | Texto placeholder                         |
| form_type     | string | Tipo de formulario (default: input)       |
| group_id      | number | ID del grupo (default: 1)                 |
| order         | number | Orden de presentación (default: 1)        |
| default_value | text   | Valor por defecto                         |
| is_disabled   | boolean| Si está deshabilitado (default: false)    |
| is_readonly   | boolean| Si es solo lectura (default: false)       |
| data_source   | json   | Fuente de datos para selects             |
| css_classes   | string | Clases CSS adicionales                    |
| help_text     | text   | Texto de ayuda                            |
| target        | number | Target del componente (default: -1)       |
| event_config  | json   | Configuración de eventos                  |
| search_type   | string | Tipo de búsqueda                          |
| date_max      | date   | Fecha máxima para date inputs             |
| number_min    | decimal| Valor mínimo para number inputs           |
| number_max    | decimal| Valor máximo para number inputs           |
| number_step   | decimal| Paso para number inputs (default: 1)      |
| created_at    | timestamp| Fecha de creación                        |
| updated_at    | timestamp| Fecha de actualización                   |

### Relaciones

- `componentes_validaciones()`: Validaciones asociadas al componente.

### Vistas Relacionadas

Utilizado para renderizar componentes individuales en formularios dinámicos.

---

## Modelo Componentes Validaciones

### Descripción

Define las reglas de validación para componentes dinámicos, incluyendo patrones, longitudes, rangos y mensajes de error personalizados.

### Campos

| Campo         | Tipo   | Descripción                               |
| ------------- | ------ | ----------------------------------------- |
| id            | number | Identificador único (PK)                  |
| componente_id | number | ID del componente (FK componentes_dinamicos) |
| pattern       | string | Patrón de validación regex                |
| default_value | text   | Valor por defecto                         |
| max_length    | number | Longitud máxima                           |
| min_length    | number | Longitud mínima                           |
| numeric_range | string | Rango numérico                            |
| field_size    | number | Tamaño del campo (default: 42)            |
| detail_info   | text   | Información detallada                     |
| is_required   | boolean| Si es requerido (default: false)          |
| custom_rules  | json   | Reglas personalizadas                     |
| error_messages| json   | Mensajes de error personalizados          |
| created_at    | timestamp| Fecha de creación                        |
| updated_at    | timestamp| Fecha de actualización                   |

### Relaciones

- `componentes_dinamicos()`: Componente al que pertenece la validación.

### Vistas Relacionadas

Utilizado para validar datos en formularios dinámicos del lado cliente y servidor.
