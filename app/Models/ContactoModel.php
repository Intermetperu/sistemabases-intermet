<?php

namespace App\Models;

use CodeIgniter\Model;

class ContactoModel extends Model
{
    protected $table            = 'contactos';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = true;
    protected $dateFormat       = 'datetime';
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $allowedFields = [
        'nombre', 'nombre_2', 'nombre_3', 'nombres_completos',
        'apellido', 'apellido_2', 'apellidos_completos', 'nombres_y_apellidos_completos',
        'tipo_documento_identidad', 'nro_documento', 'celular', 'celular_2', 'telefono',
        'empresa', 'sector', 'direccion_empresa', 'web_empresa', 'ruc_empresa', 'cargo',
        'linkedin', 'correo_electronico', 'correo_electronico_2', 'correo_corporativo',
        'correo_corporativo_2', 'status_correo_electronico', 'status_correo_corporativo',
        'profesion', 'observacion', 'pais', 'nota_origen', 'unidad_negocio',
        'estado_envio_correo', 'correo_enviado_en',
        'evento_id', 'importacion_id',
    ];

    public static function columnMapping(): array
    {
        return [
            'Nombre'                          => 'nombre',
            'Nombre 2'                        => 'nombre_2',
            'Nombre 3'                        => 'nombre_3',
            'Nombres Completos'               => 'nombres_completos',
            'Apellido'                        => 'apellido',
            'Apellido 2'                      => 'apellido_2',
            'Apellidos Completos'             => 'apellidos_completos',
            'Nombres y Apellidos Completos'   => 'nombres_y_apellidos_completos',
            'Tipo de Documento de Identidad'  => 'tipo_documento_identidad',
            'Nro de Documento'                => 'nro_documento',
            'Celular'                         => 'celular',
            'Celular 2'                       => 'celular_2',
            'Teléfono'                        => 'telefono',
            'Empresa'                         => 'empresa',
            'Sector'                          => 'sector',
            'Dirección Empresa'               => 'direccion_empresa',
            'Web Empresa'                     => 'web_empresa',
            'Ruc Empresa'                     => 'ruc_empresa',
            'Cargo'                           => 'cargo',
            'Linkedin'                        => 'linkedin',
            'Correo Electrónico'              => 'correo_electronico',
            'Correo Electrónico 2'            => 'correo_electronico_2',
            'Correo corporativo'              => 'correo_corporativo',
            'Correo corporativo 2'            => 'correo_corporativo_2',
            'Status_correo_electronico'       => 'status_correo_electronico',
            'Status_correo_corporativo'       => 'status_correo_corporativo',
            'Profesión'                       => 'profesion',
            'Observación'                     => 'observacion',
            'País'                            => 'pais',
            'Nota de origen'                  => 'nota_origen',
            'Unidad de Negocio'               => 'unidad_negocio',
        ];
    }

    public static function searchableFields(): array
    {
        return [
            'nombres_y_apellidos_completos', 'empresa', 'cargo', 'correo_electronico',
            'nota_origen', 'status_correo_corporativo', 'linkedin', 'pais',
        ];
    }

    /**
     * Mapa dbColumn => Etiqueta legible. Útil para el selector de columnas
     * y para el constructor de reglas de segmentación en el frontend.
     */
    public static function allColumns(): array
    {
        $columnas = array_flip(self::columnMapping());
        // Columna calculada (join con eventos), no proviene del CSV pero sí es
        // útil para mostrarla/filtrarla en el explorador y en los resultados.
        $columnas['evento_nombre'] = 'Evento';
        return $columnas;
    }

    /**
     * Aplica filtros dinámicos (texto libre, país, empresa y/o reglas de
     * segmentación) sobre el builder actual del modelo. Pensado para
     * encadenarse antes de countAllResults()/findAll().
     *
     * Formato de $filtros:
     * [
     *   'term'      => string,
     *   'pais'      => string,
     *   'empresa'   => string,
     *   'evento_id' => int,
     *   'reglas'    => [ ['campo' => 'empresa', 'operador' => 'contiene', 'valor' => 'acme'], ... ]
     * ]
     */
    public function aplicarFiltros(array $filtros): self
    {
        $term     = trim((string) ($filtros['term'] ?? ''));
        $pais     = trim((string) ($filtros['pais'] ?? ''));
        $empresa  = trim((string) ($filtros['empresa'] ?? ''));
        $eventoId = (int) ($filtros['evento_id'] ?? 0);
        $reglas   = $filtros['reglas'] ?? [];

        if ($term !== '') {
            $this->groupStart();
            foreach (self::searchableFields() as $i => $field) {
                $i === 0 ? $this->like($field, $term) : $this->orLike($field, $term);
            }
            $this->groupEnd();
        }

        if ($pais !== '') {
            $this->where('pais', $pais);
        }

        if ($empresa !== '') {
            $this->like('empresa', $empresa);
        }

        if ($eventoId > 0) {
            $this->whereIn('contactos.id', function ($builder) use ($eventoId) {
                return $builder->select('contacto_id')->from('contacto_importaciones')->where('evento_id', $eventoId);
            });
        }

        if (is_array($reglas)) {
            foreach ($reglas as $regla) {
                $campo    = (string) ($regla['campo'] ?? '');
                $operador = (string) ($regla['operador'] ?? 'contiene');
                $valor    = trim((string) ($regla['valor'] ?? ''));

                if (!in_array($campo, $this->allowedFields, true)) {
                    continue;
                }

                // "evento_id" e "importacion_id" en contactos solo guardan el
                // ÚLTIMO evento/carga que tocó a ese contacto (se sobrescriben
                // en cada nueva importación). Para que "igual" filtre por el
                // historial real (todos los eventos/cargas que alguna vez
                // tocaron al contacto) se consulta la bitácora completa
                // contacto_importaciones en vez de la columna denormalizada.
                if ($operador === 'igual' && in_array($campo, ['evento_id', 'importacion_id'], true) && $valor !== '') {
                    $columnaPivot = $campo;
                    $this->whereIn('contactos.id', function ($builder) use ($columnaPivot, $valor) {
                        return $builder->select('contacto_id')->from('contacto_importaciones')->where($columnaPivot, $valor);
                    });
                    continue;
                }

                // Se prefija con "contactos." porque algunos listados hacen JOIN
                // con la tabla eventos (que también tiene una columna "nombre"),
                // y sin el prefijo el motor de base de datos la vería ambigua.
                $campo = 'contactos.' . $campo;

                switch ($operador) {
                    case 'contiene':
                        if ($valor !== '') {
                            $this->like($campo, $valor);
                        }
                        break;
                    case 'no_contiene':
                        if ($valor !== '') {
                            $this->notLike($campo, $valor);
                        }
                        break;
                    case 'igual':
                        $this->where($campo, $valor);
                        break;
                    case 'diferente':
                        $this->where($campo . ' !=', $valor);
                        break;
                    case 'empieza':
                        if ($valor !== '') {
                            $this->like($campo, $valor, 'after');
                        }
                        break;
                    case 'termina':
                        if ($valor !== '') {
                            $this->like($campo, $valor, 'before');
                        }
                        break;
                    case 'vacio':
                        $this->groupStart()
                            ->where($campo . ' IS NULL')
                            ->orWhere($campo, '')
                            ->groupEnd();
                        break;
                    case 'no_vacio':
                        $this->where($campo . ' IS NOT NULL')
                            ->where($campo . ' !=', '');
                        break;
                }
            }
        }

        return $this;
    }

    /**
     * Paginación real en base de datos (no limitada a 500 filas como buscar()).
     */
    public function paginar(int $page, int $perPage, array $filtros = []): array
    {
        $page    = max(1, $page);
        $perPage = max(1, min(500, $perPage));

        $this->select('contactos.*, eventos.nombre AS evento_nombre')
            ->join('eventos', 'eventos.id = contactos.evento_id', 'left');
        $this->aplicarFiltros($filtros);
        $total = $this->countAllResults(false);

        $offset = ($page - 1) * $perPage;
        $data = $this->orderBy('contactos.id', 'DESC')->findAll($perPage, $offset);

        return [
            'data'       => $data,
            'total'      => $total,
            'page'       => $page,
            'perPage'    => $perPage,
            'totalPages' => $total > 0 ? (int) ceil($total / $perPage) : 0,
        ];
    }

    /**
     * Cuenta cuántos contactos coinciden con un set de filtros, sin traer datos.
     */
    public function contarConFiltros(array $filtros): int
    {
        $this->aplicarFiltros($filtros);
        return $this->countAllResults();
    }

    /**
     * Búsqueda de texto libre, combinable con filtros de país, empresa y evento.
     */
    public function buscar(string $term, string $pais = '', string $empresa = '', int $eventoId = 0)
    {
        $builder = $this->select(
            'contactos.id, contactos.nombres_y_apellidos_completos, contactos.empresa, contactos.cargo,
             contactos.correo_corporativo, contactos.correo_electronico, contactos.celular, contactos.telefono,
             contactos.nota_origen, contactos.status_correo_corporativo, contactos.linkedin, contactos.pais,
             contactos.estado_envio_correo, contactos.evento_id, eventos.nombre AS evento_nombre'
        )->join('eventos', 'eventos.id = contactos.evento_id', 'left');

        if ($term !== '') {
            $builder->groupStart();
            foreach (self::searchableFields() as $i => $field) {
                if ($i === 0) {
                    $builder->like($field, $term);
                } else {
                    $builder->orLike($field, $term);
                }
            }
            $builder->groupEnd();
        }

        if ($pais !== '') {
            $builder->where('pais', $pais);
        }

        if ($empresa !== '') {
            $builder->like('empresa', $empresa);
        }

        if ($eventoId > 0) {
            $builder->whereIn('contactos.id', function ($sub) use ($eventoId) {
                return $sub->select('contacto_id')->from('contacto_importaciones')->where('evento_id', $eventoId);
            });
        }

        return $builder->orderBy('contactos.id', 'DESC')->findAll(500);
    }

    /**
     * Lista de países distintos registrados (para el filtro rápido).
     */
    public function paisesDistintos(): array
    {
        $rows = $this->select('pais')
            ->where('pais IS NOT NULL')
            ->where('pais !=', '')
            ->groupBy('pais')
            ->orderBy('pais', 'ASC')
            ->findAll(300);

        return array_column($rows, 'pais');
    }

    /**
     * Empresas que contienen el texto buscado (para el filtro rápido tipo autocompletar).
     */
    public function empresasComoOpciones(string $q): array
    {
        if (mb_strlen($q) < 2) {
            return [];
        }

        $rows = $this->select('empresa')
            ->where('empresa IS NOT NULL')
            ->where('empresa !=', '')
            ->like('empresa', $q)
            ->groupBy('empresa')
            ->orderBy('empresa', 'ASC')
            ->findAll(20);

        return array_column($rows, 'empresa');
    }
}