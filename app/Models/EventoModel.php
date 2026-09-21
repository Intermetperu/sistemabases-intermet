<?php

namespace App\Models;

use CodeIgniter\Model;

class EventoModel extends Model
{
    protected $table          = 'eventos';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $useTimestamps  = true;
    protected $dateFormat     = 'datetime';
    protected $createdField   = 'created_at';
    protected $updatedField   = 'updated_at';

    protected $allowedFields = ['nombre', 'descripcion', 'fecha_evento', 'lugar'];

    protected $validationRules = [
        'nombre' => 'required|min_length[2]|max_length[200]',
    ];

    protected $validationMessages = [
        'nombre' => [
            'required'   => 'El nombre del evento es obligatorio.',
            'min_length' => 'El nombre del evento es muy corto.',
        ],
    ];

    /**
     * Lista todos los eventos junto con la cantidad de contactos que tiene
     * cada uno. Útil para el selector de eventos y para el listado de
     * administración de eventos.
     */
    public function listarConConteo(): array
    {
        return $this->select('eventos.*, COUNT(DISTINCT contacto_importaciones.contacto_id) AS total_contactos')
            ->join('contacto_importaciones', 'contacto_importaciones.evento_id = eventos.id', 'left')
            ->groupBy('eventos.id')
            ->orderBy('eventos.fecha_evento', 'DESC')
            ->orderBy('eventos.id', 'DESC')
            ->findAll();
    }

    /**
     * Busca un evento por nombre (sin distinguir mayúsculas/espacios extra).
     * Si no existe, lo crea. Se usa al subir un CSV indicando el nombre de
     * un evento nuevo en vez de seleccionar uno existente.
     */
    public function buscarOcrear(string $nombre, ?string $descripcion = null, ?string $fecha = null, ?string $lugar = null): array
    {
        $nombre = trim($nombre);

        $existente = $this->where('nombre', $nombre)->first();
        if ($existente) {
            return $existente;
        }

        $id = $this->insert([
            'nombre'       => $nombre,
            'descripcion'  => $descripcion !== '' ? $descripcion : null,
            'fecha_evento' => $fecha !== '' ? $fecha : null,
            'lugar'        => $lugar !== '' ? $lugar : null,
        ], true);

        return $this->find($id);
    }
}
