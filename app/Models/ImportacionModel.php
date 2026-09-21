<?php

namespace App\Models;

use CodeIgniter\Model;

class ImportacionModel extends Model
{
    protected $table          = 'importaciones';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $useTimestamps  = true;
    protected $dateFormat     = 'datetime';
    protected $createdField   = 'created_at';
    protected $updatedField   = '';

    protected $allowedFields = [
        'evento_id', 'nombre_archivo', 'total_filas', 'insertados',
        'actualizados', 'usuario_id', 'usuario_nombre',
    ];

    /**
     * Historial paginado de cargas de CSV, con el nombre del evento
     * asociado (si tiene). Permite filtrar por evento.
     */
    public function paginar(int $page, int $perPage, int $eventoId = 0): array
    {
        $page    = max(1, $page);
        $perPage = max(1, min(200, $perPage));

        $builder = $this->select('importaciones.*, eventos.nombre AS evento_nombre')
            ->join('eventos', 'eventos.id = importaciones.evento_id', 'left');

        if ($eventoId > 0) {
            $builder->where('importaciones.evento_id', $eventoId);
        }

        $total  = $builder->countAllResults(false);
        $offset = ($page - 1) * $perPage;

        $data = $builder->orderBy('importaciones.id', 'DESC')->findAll($perPage, $offset);

        return [
            'data'       => $data,
            'total'      => $total,
            'page'       => $page,
            'perPage'    => $perPage,
            'totalPages' => $total > 0 ? (int) ceil($total / $perPage) : 0,
        ];
    }
}
