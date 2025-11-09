# Definición de Modelos

## Modelo Mercurio30 (Formulario Afiliación Empleador)

### Descripción

Estructura de datos para crear/editar un empleador. Campos extraídos de la vista `mercurio/empresa/tmp/tmp_create`.

### Campos

| Campo        | Tipo    | Descripción                                               |
| ------------ | ------- | --------------------------------------------------------- |
| id           | number  | Identificador interno (oculto)                            |
| tipdoc       | text    | Tipo documento empresa (oculto, usado en proceso)         |
| calemp       | text    | Calificación empleador, valor por defecto `E`             |
| tipper       | select  | Tipo persona                                              |
| nit          | number  | NIT o documento empresa                                   |
| razsoc       | text    | Razón social                                              |
| coddoc       | select  | Tipo documento empresa                                    |
| digver       | number  | Dígito de verificación                                    |
| sigla        | text    | Sigla                                                     |
| matmer       | text    | Matrícula mercantil                                       |
| dirpri       | address | Dirección comercial                                       |
| tipsoc       | select  | Tipo sociedad                                             |
| tipemp       | select  | Tipo empresa                                              |
| codzon       | select  | Ciudad/Zona donde laboran trabajadores                    |
| codact       | select  | Código CIUU-DIAN (Actividad económica)                    |
| fecini       | date    | Fecha inicio actividades                                  |
| tottra       | number  | Total trabajadores                                        |
| valnom       | number  | Valor nómina                                              |
| codcaj       | select  | Caja anterior de afiliación                               |
| ciupri       | select  | Ciudad comercial                                          |
| telpri       | number  | Teléfono comercial con indicativo                         |
| celpri       | number  | Celular comercial                                         |
| emailpri     | email   | Email comercial                                           |
| codciu       | select  | Ciudad notificación                                       |
| direccion    | address | Dirección notificación                                    |
| telefono     | number  | Teléfono notificación                                     |
| email        | email   | Email notificación                                        |
| fax          | text    | Fax notificación                                          |
| celular      | number  | Celular notificación                                      |
| autoriza     | select  | Autoriza tratamiento de datos                             |
| fecsol       | date    | Fecha de solicitud (oculto/proceso)                       |
| log          | number  | Correlativo/auditoría                                     |
| estado       | text    | Estado del registro (T/D/A/X/P)                           |
| codest       | text    | Código de estado                                          |
| motivo       | text    | Motivo del estado                                         |
| fecest       | date    | Fecha del estado                                          |
| usuario      | number  | Usuario que realiza la acción                             |
| tipo         | text    | Tipo identificación (llave compuesta relación mercurio07) |
| documento    | text    | Documento (llave compuesta relación mercurio07)           |
| cedrep       | text    | Documento representante legal                             |
| repleg       | text    | Nombre completo representante legal                       |
| priaperepleg | text    | Primer apellido representante legal                       |
| segaperepleg | text    | Segundo apellido representante legal                      |
| prinomrepleg | text    | Primer nombre representante legal                         |
| segnomrepleg | text    | Segundo nombre representante legal                        |
| priape       | text    | Primer apellido (cuando empleador es persona natural)     |
| segape       | text    | Segundo apellido (persona natural)                        |
| prinom       | text    | Primer nombre (persona natural)                           |
| segnom       | text    | Segundo nombre (persona natural)                          |
| fecapr       | date    | Fecha de aprobación resolución                            |
| sat_fecapr   | text    | Fecha de aprobación SAT                                   |
| sat_cedrep   | text    | Documento representante (SAT)                             |
| sat_numtra   | text    | Número de transacción SAT                                 |
| ruuid        | uuid    | Identificador único de solicitud                          |

### Relaciones

- Formularios/Componentes select (`component_*`) definen catálogos externos. No se infiere relación directa en el modelo sin revisar origen de datos.

### Vistas Relacionadas

- mercurio/empresa/index.blade.php
- mercurio/empresa/tmp/tmp_create.blade.php

## Modelo Mercurio31 (Formulario Afiliación Trabajador)

### Descripción

Estructura de datos para crear/editar un trabajador asociado a una empresa. Campos extraídos de `mercurio/trabajador/tmp/tmp_create`.

### Campos

| Campo               | Tipo    | Descripción                               |
| ------------------- | ------- | ----------------------------------------- |
| id                  | number  | Identificador interno (oculto)            |
| profesion           | text    | Profesión (oculto, por defecto "Ninguna") |
| fax                 | text    | Fax (oculto)                              |
| nit                 | number  | NIT empleador                             |
| razsoc              | text    | Razón social empleador                    |
| codsuc              | select  | Sucursal empresa                          |
| tipdoc              | select  | Tipo documento trabajador                 |
| cedtra              | number  | Número de identificación                  |
| priape              | text    | Primer apellido                           |
| segape              | text    | Segundo apellido                          |
| prinom              | text    | Primer nombre                             |
| segnom              | text    | Segundo nombre                            |
| codzon              | select  | Zona trabajo                              |
| dirlab              | address | Dirección de trabajo                      |
| ruralt              | select  | Labor rural                               |
| fecing              | date    | Fecha ingreso                             |
| fecnac              | date    | Fecha nacimiento                          |
| ciunac              | select  | Ciudad nacimiento                         |
| sexo                | select  | Sexo                                      |
| orisex              | select  | Orientación sexual                        |
| facvul              | select  | Factor vulnerabilidad                     |
| estciv              | select  | Estado civil                              |
| tipafi              | select  | Tipo afiliado                             |
| cargo               | select  | Cargo                                     |
| salario             | text    | Salario                                   |
| tipsal              | select  | Tipo salario                              |
| horas               | text    | Horas mensuales                           |
| tipcon              | select  | Tipo contrato                             |
| tipjor              | select  | Tipo jornada                              |
| comision            | select  | Recibe comisión                           |
| labora_otra_empresa | select  | Labora en otra empresa                    |
| otra_empresa        | text    | Empresa donde también labora              |
| telefono            | number  | Teléfono                                  |
| celular             | number  | Celular                                   |
| codciu              | select  | Ciudad residencia                         |
| direccion           | address | Dirección residencia                      |
| barrio              | text    | Barrio                                    |
| email               | email   | Email                                     |
| rural               | select  | Residencia rural                          |
| vivienda            | select  | Vivienda                                  |
| autoriza            | select  | Autoriza tratamiento de datos             |

### Relaciones

- Los `component_*` corresponden a catálogos (documentos, ciudades, zona, etc.). No se documenta relación sin revisar modelos fuente.

### Vistas Relacionadas

- mercurio/trabajador/index.blade.php
- mercurio/trabajador/tmp/tmp_create.blade.php

## Modelo Mercurio34 (Formulario Registro Beneficiario)

### Descripción

Estructura de datos para crear/editar beneficiarios asociados a un trabajador o empleador. Campos extraídos de `mercurio/beneficiario/tmp/tmp_create`.

### Campos

| Campo           | Tipo   | Descripción                                     |
| --------------- | ------ | ----------------------------------------------- |
| id              | number | Identificador interno (oculto)                  |
| profesion       | text   | Profesión (oculto, por defecto "Ninguna")       |
| fax             | text   | Fax (oculto)                                    |
| nit             | number | NIT empresa/empleador                           |
| parent          | select | Parentesco con trabajador                       |
| cedtra          | number | Identificación del trabajador                   |
| cedcon          | number | Identificación acudiente (madre/padre)          |
| convive         | select | Con quién convive                               |
| cedacu          | number | Identificación acudiente (solo lectura en UI)   |
| huerfano        | select | ¿Es huérfano?                                   |
| tiphij          | select | Tipo hijo                                       |
| peretn          | select | Pertenencia étnica                              |
| resguardo_id    | select | Resguardo indígena                              |
| pub_indigena_id | select | Pueblo indígena                                 |
| tipdoc          | select | Tipo documento beneficiario                     |
| numdoc          | number | Número identificación                           |
| priape          | text   | Primer apellido                                 |
| segape          | text   | Segundo apellido                                |
| prinom          | text   | Primer nombre                                   |
| segnom          | text   | Segundo nombre                                  |
| fecnac          | date   | Fecha nacimiento                                |
| ciunac          | select | Ciudad nacimiento                               |
| sexo            | select | Sexo                                            |
| estciv          | select | Estado civil                                    |
| telefono        | number | Teléfono                                        |
| celular         | number | Celular                                         |
| email           | text   | Email                                           |
| codciu          | select | Ciudad residencia                               |
| direccion       | text   | Dirección residencia                            |
| tippag          | select | Tipo medio pago subsidio                        |
| numcue          | number | Número de cuenta/Daviplata                      |
| tipcue          | select | Tipo de cuenta                                  |
| codban          | select | Banco                                           |
| biodesco        | select | ¿Desconoce ubicación del padre/madre biológico? |
| biocedu         | number | Cédula padre/madre biológico                    |
| biotipdoc       | select | Tipo documento padre/madre biológico            |
| bioprinom       | text   | Primer nombre padre/madre biológico             |
| biosegnom       | text   | Segundo nombre padre/madre biológico            |
| biopriape       | text   | Primer apellido padre/madre biológico           |
| biosegape       | text   | Segundo apellido padre/madre biológico          |
| bioemail        | text   | Email padre/madre biológico                     |
| biophone        | number | Teléfono padre/madre biológico                  |
| biocodciu       | select | Ciudad residencia padre/madre biológico         |
| biodire         | text   | Dirección residencia padre/madre biológico      |
| biourbana       | select | Residencia zona urbana (padre/madre biológico)  |

### Relaciones

- Catálogos en `component_*` (parentescos, documentos, ciudades, bancos, etc.). No se infiere relación directa sin revisar modelos.

### Vistas Relacionadas

- mercurio/beneficiario/index.blade.php
- mercurio/beneficiario/tmp/tmp_create.blade.php

## Modelo Mercurio32 (Formulario Registro Cónyuge)

### Descripción

Estructura de datos para crear/editar información del cónyuge o compañer@ permanente. Campos extraídos de `mercurio/conyuge/tmp/tmp_create`.

### Campos

| Campo           | Tipo    | Descripción                             |
| --------------- | ------- | --------------------------------------- |
| id              | number  | Identificador interno (oculto)          |
| profesion       | text    | Profesión (oculto)                      |
| fax             | text    | Fax (oculto)                            |
| cedtra          | number  | Identificación del trabajador           |
| nit             | number  | NIT (cuando aplica en flujo de empresa) |
| comper          | select  | ¿Compañer@ permanente?                  |
| tiecon          | number  | Tiempo convivencia (años)               |
| ciures          | select  | Ciudad residencia                       |
| codzon          | select  | Zona                                    |
| tipviv          | select  | Vivienda                                |
| direccion       | address | Dirección residencia                    |
| nivedu          | select  | Nivel educación                         |
| captra          | select  | Capacidad de trabajo                    |
| tipdis          | select  | Tipo discapacidad                       |
| codocu          | select  | Ocupación                               |
| empresalab      | text    | Empresa donde labora                    |
| fecing          | date    | Fecha inicio laboral                    |
| salario         | number  | Ingresos mensuales                      |
| tippag          | select  | Tipo pago subsidio                      |
| numcue          | number  | Número de cuenta                        |
| codban          | select  | Banco                                   |
| autoriza        | select  | Autoriza tratamiento de datos           |
| tipdoc          | select  | Tipo documento cónyuge                  |
| cedcon          | number  | Número identificación                   |
| priape          | text    | Primer apellido                         |
| segape          | text    | Segundo apellido                        |
| prinom          | text    | Primer nombre                           |
| segnom          | text    | Segundo nombre                          |
| fecnac          | date    | Fecha nacimiento                        |
| ciunac          | select  | Ciudad nacimiento                       |
| sexo            | select  | Sexo                                    |
| estciv          | select  | Estado civil                            |
| telefono        | number  | Teléfono                                |
| celular         | number  | Celular                                 |
| email           | text    | Email                                   |
| peretn          | select  | Pertenencia étnica                      |
| resguardo_id    | select  | Resguardo indígena                      |
| pub_indigena_id | select  | Pueblo indígena                         |

### Relaciones

- Catálogos `component_*` (documentos, ciudades, ocupación, bancos, etc.). No se documenta relación sin revisar modelos fuente.

### Vistas Relacionadas

- mercurio/conyuge/index.blade.php
- mercurio/conyuge/tmp/tmp_create.blade.php

## Modelo Mercurio36 - Afiliación Facultativo

### Descripción

Estructura de datos para registrar afiliación de trabajador facultativo. Campos extraídos de `mercurio/facultativo/tmp/tmp_create`.

### Campos

| Campo           | Tipo    | Descripción                                 |
| --------------- | ------- | ------------------------------------------- |
| id              | number  | Identificador interno (oculto)              |
| calemp          | text    | Calificación afiliado, por defecto `F`      |
| coddocrepleg    | text    | Tipo documento representante legal (oculto) |
| fecsol          | date    | Fecha solicitud (oculto en UI)              |
| coddoc          | select  | Tipo documento                              |
| cedtra          | number  | Identificación                              |
| priape          | text    | Primer apellido                             |
| segape          | text    | Segundo apellido                            |
| prinom          | text    | Primer nombre                               |
| segnom          | text    | Segundo nombre                              |
| codact          | select  | CIUU-DIAN Actividad económica               |
| fecini          | date    | Fecha inicio                                |
| codcaj          | select  | Caja a la que estuvo afiliado antes         |
| fecnac          | date    | Fecha nacimiento                            |
| ciunac          | select  | Ciudad nacimiento                           |
| facvul          | select  | Factor vulnerabilidad                       |
| sexo            | select  | Sexo                                        |
| orisex          | select  | Orientación sexual                          |
| estciv          | select  | Estado civil                                |
| cabhog          | select  | Cabeza de hogar                             |
| codciu          | select  | Ciudad residencia                           |
| direccion       | address | Dirección de residencia                     |
| dirlab          | address | Dirección de trabajo                        |
| salario         | number  | Salario                                     |
| tipsal          | select  | Tipo salario                                |
| captra          | select  | Capacidad de trabajo                        |
| tipdis          | select  | Tipo discapacidad                           |
| nivedu          | select  | Nivel educación                             |
| vivienda        | select  | Vivienda                                    |
| tipafi          | select  | Tipo afiliado                               |
| peretn          | select  | Pertenencia étnica                          |
| resguardo_id    | select  | Resguardo indígena                          |
| pub_indigena_id | select  | Pueblo indígena                             |
| cargo           | select  | Cargo                                       |
| tippag          | select  | Tipo pago subsidio                          |
| numcue          | number  | Número de cuenta                            |
| tipcue          | select  | Tipo de cuenta                              |
| codban          | select  | Banco                                       |
| email           | email   | Email notificación                          |
| telefono        | number  | Teléfono notificación                       |
| celular         | number  | Celular notificación                        |
| codzon          | select  | Lugar donde labora (zona)                   |
| ruralt          | select  | Labor rural                                 |
| rural           | select  | Residencia rural                            |
| autoriza        | select  | Autoriza tratamiento de datos               |

Notas: Algunos campos se gestionan mediante componentes `component_*` y corresponden a catálogos (documentos, ciudades, bancos, etc.).

### Relaciones

- Catálogos `component_*` (documentos, ciudades, actividad económica, bancos, etc.). Requiere revisar modelos fuente para mapear relaciones Eloquent.

### Vistas Relacionadas

- mercurio/facultativo/index.blade.php
- mercurio/facultativo/tmp/tmp_create.blade.php

## Modelo Mercurio38 - Afiliación Pensionado

### Descripción

Estructura de datos para registrar afiliación de trabajador pensionado. Campos extraídos de `mercurio/pensionado/tmp/tmp_create`.

### Campos

| Campo           | Tipo    | Descripción                                 |
| --------------- | ------- | ------------------------------------------- |
| id              | number  | Identificador interno (oculto)              |
| calemp          | text    | Calificación afiliado, por defecto `P`      |
| coddocrepleg    | text    | Tipo documento representante legal (oculto) |
| fecsol          | date    | Fecha solicitud (oculto en UI)              |
| tipdoc          | select  | Tipo documento                              |
| cedtra          | number  | Identificación                              |
| priape          | text    | Primer apellido                             |
| segape          | text    | Segundo apellido                            |
| prinom          | text    | Primer nombre                               |
| segnom          | text    | Segundo nombre                              |
| codact          | select  | CIUU-DIAN Actividad económica               |
| fecini          | date    | Fecha inicio                                |
| codcaj          | select  | Caja a la que estuvo afiliado antes         |
| fecnac          | date    | Fecha nacimiento                            |
| ciunac          | select  | Ciudad nacimiento                           |
| facvul          | select  | Factor vulnerabilidad                       |
| sexo            | select  | Sexo                                        |
| orisex          | select  | Orientación sexual                          |
| estciv          | select  | Estado civil                                |
| cabhog          | select  | Cabeza de hogar                             |
| codciu          | select  | Ciudad residencia                           |
| direccion       | address | Dirección de residencia                     |
| dirlab          | address | Dirección de trabajo                        |
| salario         | number  | Salario                                     |
| tipsal          | select  | Tipo salario                                |
| captra          | select  | Capacidad de trabajo                        |
| tipdis          | select  | Tipo discapacidad                           |
| nivedu          | select  | Nivel educación                             |
| vivienda        | select  | Vivienda                                    |
| tipafi          | select  | Tipo afiliado                               |
| peretn          | select  | Pertenencia étnica                          |
| resguardo_id    | select  | Resguardo indígena                          |
| pub_indigena_id | select  | Pueblo indígena                             |
| cargo           | select  | Cargo                                       |
| tippag          | select  | Tipo pago subsidio                          |
| numcue          | number  | Número de cuenta                            |
| tipcue          | select  | Tipo de cuenta                              |
| codban          | select  | Banco                                       |
| email           | email   | Email notificación                          |
| telefono        | number  | Teléfono notificación                       |
| celular         | number  | Celular notificación                        |
| codzon          | select  | Lugar donde labora (zona)                   |
| ruralt          | select  | Labor rural                                 |
| rural           | select  | Residencia rural                            |
| autoriza        | select  | Autoriza tratamiento de datos               |

Notas:

- El modelo Kumbia `mercurio38.php` expone propiedades adicionales (p. ej. `nit`, `fecing`, `repleg`, `tipsoc`, `tipemp`) que pueden utilizarse en otros flujos; no todas aparecen en esta UI.

### Relaciones

- Catálogos `component_*` (documentos, ciudades, actividad económica, bancos, etc.). Requiere revisar modelos/migraciones para mapear relaciones Eloquent.

### Vistas Relacionadas

- mercurio/pensionado/index.blade.php
- mercurio/pensionado/tmp/tmp_create.blade.php

## Modelo Mercurio41 - Afiliación Independiente

### Descripción

Estructura de datos para registrar afiliación de trabajador independiente. Campos extraídos de `mercurio/independiente/tmp/tmp_create`.

### Campos

| Campo           | Tipo    | Descripción                                 |
| --------------- | ------- | ------------------------------------------- |
| id              | number  | Identificador interno (oculto)              |
| calemp          | text    | Calificación afiliado, por defecto `I`      |
| coddocrepleg    | text    | Tipo documento representante legal (oculto) |
| fecsol          | date    | Fecha solicitud (oculto en UI)              |
| coddoc          | select  | Tipo documento                              |
| cedtra          | number  | Identificación                              |
| priape          | text    | Primer apellido                             |
| segape          | text    | Segundo apellido                            |
| prinom          | text    | Primer nombre                               |
| segnom          | text    | Segundo nombre                              |
| codact          | select  | CIUU-DIAN Actividad económica               |
| fecini          | date    | Fecha inicio                                |
| codcaj          | select  | Caja a la que estuvo afiliado antes         |
| fecnac          | date    | Fecha nacimiento                            |
| ciunac          | select  | Ciudad nacimiento                           |
| facvul          | select  | Factor vulnerabilidad                       |
| sexo            | select  | Sexo                                        |
| orisex          | select  | Orientación sexual                          |
| estciv          | select  | Estado civil                                |
| cabhog          | select  | Cabeza de hogar                             |
| codciu          | select  | Ciudad residencia                           |
| direccion       | address | Dirección de residencia                     |
| dirlab          | address | Dirección de trabajo                        |
| salario         | number  | Salario                                     |
| tipsal          | select  | Tipo salario                                |
| captra          | select  | Capacidad de trabajo                        |
| tipdis          | select  | Tipo discapacidad                           |
| nivedu          | select  | Nivel educación                             |
| vivienda        | select  | Vivienda                                    |
| tipafi          | select  | Tipo afiliado                               |
| peretn          | select  | Pertenencia étnica                          |
| resguardo_id    | select  | Resguardo indígena                          |
| pub_indigena_id | select  | Pueblo indígena                             |
| cargo           | select  | Cargo                                       |
| tippag          | select  | Tipo pago subsidio                          |
| numcue          | number  | Número de cuenta                            |
| tipcue          | select  | Tipo de cuenta                              |
| codban          | select  | Banco                                       |
| email           | email   | Email notificación                          |
| telefono        | number  | Teléfono notificación                       |
| celular         | number  | Celular notificación                        |
| codzon          | select  | Lugar donde labora (zona)                   |
| ruralt          | select  | Labor rural                                 |
| rural           | select  | Residencia rural                            |
| autoriza        | select  | Autoriza tratamiento de datos               |

### Relaciones

- Catálogos `component_*` (documentos, ciudades, actividad económica, bancos, etc.). Requiere revisar modelos/migraciones para mapear relaciones Eloquent.

### Vistas Relacionadas

- mercurio/independiente/index.blade.php
- mercurio/independiente/tmp/tmp_create.blade.php

## Modelo Mercurio39 - Comunitaria

### Descripción

Estructura de datos para capturar información de la modalidad comunitaria. Campos extraídos del formulario en `mercurio/comunitaria/index.blade.php` (modal de captura).

### Campos

| Campo     | Tipo    | Descripción                        |
| --------- | ------- | ---------------------------------- |
| id        | number  | Identificador interno (oculto)     |
| calemp    | text    | Calificación (oculto en UI)        |
| codact    | text    | Actividad económica (oculto en UI) |
| tipdoc    | select  | Tipo documento                     |
| cedtra    | number  | Identificación                     |
| priape    | text    | Primer apellido                    |
| segape    | text    | Segundo apellido                   |
| prinom    | text    | Primer nombre                      |
| segnom    | text    | Segundo nombre                     |
| fecnac    | date    | Fecha nacimiento                   |
| ciunac    | select  | Ciudad nacimiento                  |
| sexo      | select  | Sexo                               |
| estciv    | select  | Estado civil                       |
| cabhog    | select  | Cabeza hogar                       |
| codciu    | select  | Ciudad residencia                  |
| codzon    | select  | Zona                               |
| direccion | address | Dirección                          |
| barrio    | text    | Barrio                             |
| telefono  | number  | Teléfono                           |
| celular   | number  | Celular                            |
| fax       | text    | Fax                                |
| email     | text    | Email                              |
| fecing    | date    | Fecha ingreso                      |
| salario   | number  | Salario                            |
| captra    | select  | Capacidad de trabajo               |
| tipdis    | select  | Tipo discapacidad                  |
| nivedu    | select  | Nivel educación                    |
| rural     | select  | Residencia rural                   |
| vivienda  | select  | Vivienda                           |
| tipafi    | select  | Tipo afiliado                      |
| autoriza  | select  | Autoriza tratamiento de datos      |

### Relaciones

- Catálogos `component_*` (documentos, ciudades, zona, etc.). Requiere revisar modelos/migraciones para mapear relaciones Eloquent.

### Vistas Relacionadas

- mercurio/comunitaria/index.blade.php

## Modelo Mercurio40 - Servicio Doméstico

### Descripción

Estructura de datos para registrar afiliación en servicio doméstico. Campos extraídos de `mercurio/domestico/tmp/tmp_create`.

### Campos

| Campo           | Tipo   | Descripción                                 |
| --------------- | ------ | ------------------------------------------- |
| id              | number | Identificador interno (oculto)              |
| calemp          | text   | Calificación afiliado (oculto en UI)        |
| coddocrepleg    | text   | Tipo documento representante legal (oculto) |
| coddoc          | select | Tipo documento                              |
| cedtra          | number | Identificación                              |
| priape          | text   | Primer apellido                             |
| segape          | text   | Segundo apellido                            |
| prinom          | text   | Primer nombre                               |
| segnom          | text   | Segundo nombre                              |
| codact          | select | CIUU-DIAN Actividad económica               |
| fecini          | date   | Fecha inicio                                |
| codcaj          | select | Caja a la que estuvo afiliado antes         |
| fecnac          | date   | Fecha nacimiento                            |
| ciunac          | select | Ciudad nacimiento                           |
| facvul          | select | Factor vulnerabilidad                       |
| sexo            | select | Sexo                                        |
| orisex          | select | Orientación sexual                          |
| estciv          | select | Estado civil                                |
| cabhog          | select | Cabeza de hogar                             |
| codciu          | select | Ciudad residencia                           |
| direccion       | text   | Dirección de residencia                     |
| dirlab          | text   | Dirección de trabajo                        |
| salario         | number | Salario                                     |
| tipsal          | select | Tipo salario                                |
| captra          | select | Capacidad de trabajo                        |
| tipdis          | select | Tipo discapacidad                           |
| nivedu          | select | Nivel educación                             |
| vivienda        | select | Vivienda                                    |
| tipafi          | select | Tipo afiliado                               |
| peretn          | select | Pertenencia étnica                          |
| resguardo_id    | select | Resguardo indígena                          |
| pub_indigena_id | select | Pueblo indígena                             |
| cargo           | select | Cargo                                       |
| tippag          | select | Tipo pago subsidio                          |
| numcue          | number | Número de cuenta                            |
| tipcue          | select | Tipo de cuenta                              |
| codban          | select | Banco                                       |
| email           | text   | Email notificación                          |
| telefono        | number | Teléfono notificación                       |
| celular         | number | Celular notificación                        |
| codzon          | select | Lugar donde labora (zona)                   |
| ruralt          | select | Labor rural                                 |
| rural           | select | Residencia rural                            |
| autoriza        | select | Autoriza tratamiento de datos               |

### Relaciones

- Catálogos `component_*` (documentos, ciudades, actividad económica, bancos, etc.). Requiere revisar modelos/migraciones para mapear relaciones Eloquent.

### Vistas Relacionadas

- mercurio/domestico/tmp/tmp_create.blade.php

## Modelo Mercurio45 - Certificados

### Descripción

Gestión de presentación de certificados por beneficiario. Los campos visibles en UI están en `mercurio/certificados/index.blade.php`; los metadatos se toman del modelo Kumbia `mercurio45.php`.

### Campos

| Campo     | Tipo   | Descripción                                |
| --------- | ------ | ------------------------------------------ |
| id        | number | Identificador interno (oculto)             |
| cedtra    | text   | Identificación del trabajador titular      |
| codben    | number | Código beneficiario                        |
| nombre    | text   | Nombre beneficiario                        |
| codcer    | select | Certificado a presentar (por beneficiario) |
| nomcer    | text   | Nombre del certificado                     |
| archivo   | file   | Archivo adjunto del certificado            |
| fecha     | date   | Fecha de solicitud/cargue                  |
| estado    | text   | Estado de la solicitud (T/D/A/X/P)         |
| motivo    | text   | Motivo del estado (opcional)               |
| fecest    | date   | Fecha del estado                           |
| codest    | text   | Código del estado                          |
| usuario   | number | Usuario que realiza la acción              |
| tipo      | text   | Tipo (uso interno)                         |
| coddoc    | text   | Tipo documento (cuando aplique)            |
| documento | text   | Documento (cuando aplique)                 |

### Relaciones

- `codben` referencia a Beneficiario; `codcer` referencia catálogo de certificados. Requiere revisar migraciones/modelos Laravel para mapear Eloquent.

### Vistas Relacionadas

- mercurio/certificados/index.blade.php
