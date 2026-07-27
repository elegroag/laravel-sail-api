<?php

namespace App\Support;

use App\Models\Gener18;
use Carbon\Carbon;
use DateTimeInterface;

class AuditoriaSolicitudFieldsBuilder
{
    /** @var array<string, string>|null */
    private ?array $tiposDocumentoCache = null;

    /**
     * @return array<string, mixed>
     */
    public function build(string $tipopc, object $solicitud): array
    {
        return match ($tipopc) {
            '1' => $this->buildTrabajador($solicitud),
            '2' => $this->buildEmpresa($solicitud),
            '3' => $this->buildConyuge($solicitud),
            '4' => $this->buildBeneficiario($solicitud),
            '5', '6', '14' => $this->buildActualizacion($solicitud),
            '7' => $this->buildRetiro($solicitud),
            '8' => $this->buildCertificado($solicitud),
            '9', '10', '11', '12', '13' => $this->buildAfiliadoPersona($solicitud),
            default => [],
        };
    }

    /**
     * @return array<string, mixed>
     */
    private function buildTrabajador(object $m): array
    {
        $fields = [];
        $this->add($fields, 'NIT', $this->get($m, 'getNit'));
        $this->add($fields, 'Razón social', $this->get($m, 'getRazsoc'));
        $this->addContactoYUbicacion($fields, $m);
        $this->addPersonaAfiliado($fields, $m);
        $this->addLaboral($fields, $m);
        $this->addPago($fields, $m);
        $this->addLookup($fields, 'Tipo solicitante', solicitud_tipo_actualizacion_array(), $this->get($m, 'getTipo'));
        $this->addLookup($fields, 'Tipo documento solicitante', $this->catalogoTiposDocumento(), $this->get($m, 'getCoddoc'));

        return $fields;
    }

    /**
     * @return array<string, mixed>
     */
    private function buildEmpresa(object $m): array
    {
        $fields = [];
        $this->add($fields, 'NIT', $this->get($m, 'getNit'));
        $this->addLookup($fields, 'Tipo documento', $this->catalogoTiposDocumento(), $this->get($m, 'getTipdoc'));
        $this->add($fields, 'Razón social', $this->get($m, 'getRazsoc'));
        $this->add($fields, 'Sigla', $this->get($m, 'getSigla'));
        $this->add($fields, 'Dígito verificador', $this->get($m, 'getDigver'));
        $this->addLookup($fields, 'Calidad empresa', calemp_array(), $this->get($m, 'getCalemp'));
        $this->addLookup($fields, 'Tipo empresa', get_array_tipos(), $this->get($m, 'getTipemp'));
        $this->add($fields, 'Cédula representante', $this->get($m, 'getCedrep'));
        $this->add($fields, 'Representante legal', $this->get($m, 'getRepleg'));
        $this->addLookup($fields, 'Tipo persona representante', tipper_array(), $this->get($m, 'getTipper'));
        $this->addLookup($fields, 'Tipo documento representante', coddoc_repleg_array(), $this->get($m, 'getCoddocrepleg'));
        $this->addContactoYUbicacion($fields, $m);
        $this->add($fields, 'Dirección principal', $this->get($m, 'getDirpri'));
        $this->add($fields, 'Ciudad principal', $this->get($m, 'getCiupri'));
        $this->add($fields, 'Teléfono principal', $this->get($m, 'getTelpri'));
        $this->add($fields, 'Celular principal', $this->get($m, 'getCelpri'));
        $this->add($fields, 'Email principal', $this->get($m, 'getEmailpri'));
        $this->add($fields, 'Actividad económica', $this->get($m, 'getCodact'));
        $this->addDate($fields, 'Fecha inicio', $this->get($m, 'getFecini'));
        $this->add($fields, 'Total trabajadores', $this->get($m, 'getTottra'));
        $this->add($fields, 'Valor nómina', $this->get($m, 'getValnom'));
        $this->add($fields, 'Tipo sociedad', $this->get($m, 'getTipsoc'));
        $this->add($fields, 'Matrícula mercantil', $this->get($m, 'getMatmer'));

        return $fields;
    }

    /**
     * @return array<string, mixed>
     */
    private function buildConyuge(object $m): array
    {
        $fields = [];
        $this->add($fields, 'Cédula trabajador', $this->get($m, 'getCedtra'));
        $this->add($fields, 'Cédula cónyuge', $this->get($m, 'getCedcon'));
        $this->addLookup($fields, 'Tipo documento', $this->catalogoTiposDocumento(), $this->get($m, 'getTipdoc'));
        $this->addNombres($fields, $m);
        $this->addDate($fields, 'Fecha nacimiento', $this->get($m, 'getFecnac'));
        $this->add($fields, 'Ciudad nacimiento', $this->get($m, 'getCiunac'));
        $this->addLookup($fields, 'Sexo', sexos_array(), $this->get($m, 'getSexo'));
        $this->addLookup($fields, 'Estado civil', estados_civiles_array(), $this->get($m, 'getEstciv'));
        $this->addLookup($fields, 'Compañía permanente', convive_array(), $this->get($m, 'getComper'));
        $this->add($fields, 'Ciudad residencia', $this->get($m, 'getCiures'));
        $this->add($fields, 'Zona', $this->get($m, 'getCodzon'));
        $this->addLookup($fields, 'Tipo vivienda', vivienda_array(), $this->get($m, 'getTipviv'));
        $this->add($fields, 'Dirección', $this->get($m, 'getDireccion'));
        $this->add($fields, 'Barrio', $this->get($m, 'getBarrio'));
        $this->add($fields, 'Teléfono', $this->get($m, 'getTelefono'));
        $this->add($fields, 'Celular', $this->get($m, 'getCelular'));
        $this->add($fields, 'Email', $this->get($m, 'getEmail'));
        $this->addLookup($fields, 'Nivel educativo', nivel_educativo_array(), $this->get($m, 'getNivedu'));
        $this->addDate($fields, 'Fecha ingreso', $this->get($m, 'getFecing'));
        $this->add($fields, 'Ocupación', $this->get($m, 'getCodocu'));
        $this->add($fields, 'Salario', $this->get($m, 'getSalario'));
        $this->addLookup($fields, 'Capacidad trabajar', captra_array(), $this->get($m, 'getCaptra'));
        $this->add($fields, 'Tiempo convivencia', $this->get($m, 'getTiecon'));
        $this->addLookup($fields, 'Tipo salario', tipsal_array(), $this->get($m, 'getTipsal'));
        $this->addLookup($fields, 'Tipo pago', tipo_pago_array(), $this->get($m, 'getTippag'));
        $this->add($fields, 'Número cuenta', $this->get($m, 'getNumcue'));
        $this->add($fields, 'Empresa labora', $this->get($m, 'getEmpresalab'));
        $this->addLookup($fields, 'Tipo solicitante', solicitud_tipo_actualizacion_array(), $this->get($m, 'getTipo'));

        return $fields;
    }

    /**
     * @return array<string, mixed>
     */
    private function buildBeneficiario(object $m): array
    {
        $fields = [];
        $this->add($fields, 'NIT', $this->get($m, 'getNit'));
        $this->add($fields, 'Cédula trabajador', $this->get($m, 'getCedtra'));
        $this->add($fields, 'Cédula cónyuge', $this->get($m, 'getCedcon'));
        $this->add($fields, 'Número documento', $this->get($m, 'getNumdoc'));
        $this->addLookup($fields, 'Tipo documento', $this->catalogoTiposDocumento(), $this->get($m, 'getTipdoc'));
        $this->addNombres($fields, $m);
        $this->addDate($fields, 'Fecha nacimiento', $this->get($m, 'getFecnac'));
        $this->add($fields, 'Ciudad nacimiento', $this->get($m, 'getCiunac'));
        $this->addLookup($fields, 'Sexo', sexos_array(), $this->get($m, 'getSexo'));
        $this->addLookup($fields, 'Parentesco', parentesco_array(), $this->get($m, 'getParent'));
        $this->addLookup($fields, 'Huérfano', huerfano_array(), $this->get($m, 'getHuerfano'));
        $this->addLookup($fields, 'Tipo hijo', tipo_hijo_array(), $this->get($m, 'getTiphij'));
        $this->addLookup($fields, 'Nivel educativo', nivel_educativo_array(), $this->get($m, 'getNivedu'));
        $this->addLookup($fields, 'Capacidad trabajar', captra_array(), $this->get($m, 'getCaptra'));
        $this->addLookup($fields, 'Tipo discapacidad', tipo_discapacidad_array(), $this->get($m, 'getTipdis'));
        $this->addLookup($fields, 'Calendario', calendario_array(), $this->get($m, 'getCalendario'));
        $this->add($fields, 'Código beneficiario', $this->get($m, 'getCodben'));
        $this->add($fields, 'Cédula acudiente', $this->get($m, 'getCedacu'));
        $this->addLookup($fields, 'Tipo solicitante', solicitud_tipo_actualizacion_array(), $this->get($m, 'getTipo'));

        return $fields;
    }

    /**
     * @return array<string, mixed>
     */
    private function buildActualizacion(object $m): array
    {
        $fields = [];
        $this->add($fields, 'Documento', $this->get($m, 'getDocumento'));
        $this->addLookup($fields, 'Tipo documento', $this->catalogoTiposDocumento(), $this->get($m, 'getCoddoc'));
        $this->addLookup($fields, 'Tipo solicitante', solicitud_tipo_actualizacion_array(), $this->get($m, 'getTipo'));

        if (method_exists($m, 'getTipActInArray')) {
            $this->add($fields, 'Tipo actualización', $m->getTipActInArray());
        } else {
            $this->addLookup($fields, 'Tipo actualización', solicitud_tipo_actualizacion_array(), $this->get($m, 'getTipact'));
        }

        return $fields;
    }

    /**
     * @return array<string, mixed>
     */
    private function buildRetiro(object $m): array
    {
        $fields = [];
        $this->add($fields, 'NIT', $this->get($m, 'getNit'));
        $this->add($fields, 'Cédula trabajador', $this->get($m, 'getCedtra'));
        $this->add($fields, 'Nombre trabajador', $this->get($m, 'getNomtra'));
        $this->addDate($fields, 'Fecha retiro', $this->get($m, 'getFecret'));
        $this->add($fields, 'Motivo', $this->get($m, 'getMotivo'));
        $this->add($fields, 'Motivo rechazo', $this->get($m, 'getMotrec'));
        $this->addLookup($fields, 'Tipo solicitante', solicitud_tipo_actualizacion_array(), $this->get($m, 'getTipo'));

        return $fields;
    }

    /**
     * @return array<string, mixed>
     */
    private function buildCertificado(object $m): array
    {
        $fields = [];
        $this->add($fields, 'Cédula trabajador', $this->get($m, 'getCedtra'));
        $this->add($fields, 'Código beneficiario', $this->get($m, 'getCodben'));
        $this->add($fields, 'Nombre', $this->get($m, 'getNombre'));
        $this->add($fields, 'Certificado', $this->get($m, 'getNomcer'));
        $this->addDate($fields, 'Fecha certificado', $this->get($m, 'getFecha'));
        $this->addLookup($fields, 'Tipo solicitante', solicitud_tipo_actualizacion_array(), $this->get($m, 'getTipo'));

        return $fields;
    }

    /**
     * @return array<string, mixed>
     */
    private function buildAfiliadoPersona(object $m): array
    {
        $fields = [];
        $this->addLookup($fields, 'Tipo documento', $this->catalogoTiposDocumento(), $this->get($m, 'getTipdoc') ?? $this->get($m, 'getCoddoc'));
        $this->addNombres($fields, $m);
        $this->addPersonaAfiliado($fields, $m);
        $this->addContactoYUbicacion($fields, $m);
        $this->addLaboral($fields, $m);
        $this->addPago($fields, $m);
        $this->addLookup($fields, 'Tipo solicitante', solicitud_tipo_actualizacion_array(), $this->get($m, 'getTipo'));

        return $fields;
    }

    /**
     * @param  array<string, mixed>  $fields
     */
    private function addPersonaAfiliado(array &$fields, object $m): void
    {
        $this->addDate($fields, 'Fecha nacimiento', $this->get($m, 'getFecnac'));
        $this->add($fields, 'Ciudad nacimiento', $this->get($m, 'getCiunac'));
        $this->addLookup($fields, 'Sexo', sexos_array(), $this->get($m, 'getSexo'));
        $this->addLookup($fields, 'Orientación sexual', orientacion_sexual_array(), $this->get($m, 'getOrisex'));
        $this->addLookup($fields, 'Estado civil', estados_civiles_array(), $this->get($m, 'getEstciv'));
        $this->addLookup($fields, 'Cabeza de hogar', condicionSN(), $this->get($m, 'getCabhog'));
        $this->addLookup($fields, 'Factor vulnerabilidad', vulnerabilidades_array(), $this->get($m, 'getFacvul'));
        $this->addLookup($fields, 'Pertenencia étnica', pertenencia_etnica_array(), $this->get($m, 'getPeretn'));
        $this->addLookup($fields, 'Autoriza tratamiento datos', condicionSN(), $this->get($m, 'getAutoriza'));
    }

    /**
     * @param  array<string, mixed>  $fields
     */
    private function addContactoYUbicacion(array &$fields, object $m): void
    {
        $this->add($fields, 'Ciudad', $this->get($m, 'getCodciu') ?? $this->get($m, 'getCiures'));
        $this->add($fields, 'Zona', $this->get($m, 'getCodzon'));
        $this->add($fields, 'Dirección', $this->get($m, 'getDireccion'));
        $this->add($fields, 'Barrio', $this->get($m, 'getBarrio'));
        $this->add($fields, 'Teléfono', $this->get($m, 'getTelefono'));
        $this->add($fields, 'Celular', $this->get($m, 'getCelular'));
        $this->add($fields, 'Email', $this->get($m, 'getEmail'));
    }

    /**
     * @param  array<string, mixed>  $fields
     */
    private function addLaboral(array &$fields, object $m): void
    {
        $this->addDate($fields, 'Fecha ingreso', $this->get($m, 'getFecing'));
        $this->add($fields, 'Salario', $this->get($m, 'getSalario'));
        $this->addLookup($fields, 'Capacidad trabajar', captra_array(), $this->get($m, 'getCaptra'));
        $this->addLookup($fields, 'Tipo discapacidad', tipo_discapacidad_array(), $this->get($m, 'getTipdis'));
        $this->addLookup($fields, 'Nivel educativo', nivel_educativo_array(), $this->get($m, 'getNivedu'));
        $this->addLookup($fields, 'Residencia rural', condicionSN(), $this->get($m, 'getRural'));
        $this->add($fields, 'Horas', $this->get($m, 'getHoras'));
        $this->addLookup($fields, 'Tipo contrato', tipo_contrato(), $this->get($m, 'getTipcon'));
        $this->addLookup($fields, 'Sindicalizado', condicionSN(), $this->get($m, 'getTrasin'));
        $this->addLookup($fields, 'Tipo vivienda', vivienda_array(), $this->get($m, 'getVivienda') ?? $this->get($m, 'getTipviv'));
        $this->add($fields, 'Tipo afiliado', $this->get($m, 'getTipafi'));
        $this->add($fields, 'Cargo', $this->get($m, 'getCargo'));
        $this->add($fields, 'Dirección laboral', $this->get($m, 'getDirlab'));
        $this->addLookup($fields, 'Labor rural', condicionSN(), $this->get($m, 'getRuralt'));
        $this->add($fields, 'Comisión', $this->get($m, 'getComision'));
        $this->addLookup($fields, 'Tipo jornada', tipo_jornada_array(), $this->get($m, 'getTipjor'));
        $this->add($fields, 'Código sucursal', $this->get($m, 'getCodsuc'));
    }

    /**
     * @param  array<string, mixed>  $fields
     */
    private function addPago(array &$fields, object $m): void
    {
        $this->addLookup($fields, 'Tipo salario', tipsal_array(), $this->get($m, 'getTipsal'));
        $this->addLookup($fields, 'Tipo pago', tipo_pago_array(), $this->get($m, 'getTippag'));
        $this->add($fields, 'Número cuenta', $this->get($m, 'getNumcue'));
        $this->add($fields, 'Código banco', $this->get($m, 'getCodban'));
        $this->addLookup($fields, 'Tipo cuenta', tipo_cuenta_array(), $this->get($m, 'getTipcue'));
    }

    /**
     * @param  array<string, mixed>  $fields
     */
    private function addNombres(array &$fields, object $m): void
    {
        $this->add($fields, 'Primer apellido', $this->get($m, 'getPriape'));
        $this->add($fields, 'Segundo apellido', $this->get($m, 'getSegape'));
        $this->add($fields, 'Primer nombre', $this->get($m, 'getPrinom'));
        $this->add($fields, 'Segundo nombre', $this->get($m, 'getSegnom'));
    }

    /**
     * @param  array<string, mixed>  $fields
     */
    private function add(array &$fields, string $label, mixed $value): void
    {
        if ($value === null || $value === '') {
            return;
        }

        $fields[$label] = $value;
    }

    /**
     * @param  array<string, mixed>  $fields
     */
    private function addDate(array &$fields, string $label, mixed $value): void
    {
        $formatted = $this->formatFecha($value);
        $this->add($fields, $label, $formatted);
    }

    /**
     * @param  array<string, mixed>  $fields
     */
    private function addLookup(array &$fields, string $label, array $catalogo, mixed $value): void
    {
        $this->add($fields, $label, $this->lookup($catalogo, $value));
    }

    private function get(object $m, string $getter): mixed
    {
        if (method_exists($m, $getter)) {
            try {
                return $m->{$getter}();
            } catch (\Throwable) {
                // Continúa con acceso por atributo (snapshots de auditoría).
            }
        }

        // Snapshots auditoria_* no tienen getters: getNit -> nit, getRazsoc -> razsoc.
        if (str_starts_with($getter, 'get') && strlen($getter) > 3) {
            $field = lcfirst(substr($getter, 3));

            return $m->{$field} ?? null;
        }

        return null;
    }

    private function lookup(array $catalogo, mixed $valor): mixed
    {
        if ($valor === null || $valor === '') {
            return null;
        }

        $clave = (string) $valor;

        if (array_key_exists($clave, $catalogo)) {
            return $catalogo[$clave];
        }

        if (is_numeric($clave) && array_key_exists((int) $clave, $catalogo)) {
            return $catalogo[(int) $clave];
        }

        return $valor;
    }

    /**
     * @return array<string, string>
     */
    private function catalogoTiposDocumento(): array
    {
        if ($this->tiposDocumentoCache === null) {
            $this->tiposDocumentoCache = Gener18::query()
                ->pluck('detdoc', 'coddoc')
                ->all();
        }

        return $this->tiposDocumentoCache;
    }

    private function formatFecha(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($value instanceof DateTimeInterface) {
            return Carbon::instance($value)->format('Y-m-d');
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }
}
