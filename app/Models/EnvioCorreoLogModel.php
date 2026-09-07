<?php

namespace App\Models;

use CodeIgniter\Model;

class EnvioCorreoLogModel extends Model
{
    protected $table          = 'envios_correo_log';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $useTimestamps  = false; // created_at se maneja a mano para la ventana de 24h
    protected $allowedFields  = ['contacto_id', 'correo_destino', 'asunto', 'estado', 'error_detalle', 'created_at'];

    /**
     * Cuántos correos se enviaron exitosamente en las últimas 24 horas (ventana móvil).
     */
    public function contarEnviadosUltimas24h(): int
    {
        return $this->where('estado', 'enviado')
            ->where('created_at >=', date('Y-m-d H:i:s', time() - 86400))
            ->countAllResults();
    }

    /**
     * Fecha/hora en que se liberará el próximo cupo (24h después del envío más antiguo
     * dentro de la ventana actual). Null si no hay envíos en las últimas 24h.
     */
    public function proximaLiberacion(): ?string
    {
        $row = $this->select('created_at')
            ->where('estado', 'enviado')
            ->where('created_at >=', date('Y-m-d H:i:s', time() - 86400))
            ->orderBy('created_at', 'ASC')
            ->first();

        if (!$row) {
            return null;
        }

        return date('Y-m-d H:i:s', strtotime($row['created_at']) + 86400);
    }
}
