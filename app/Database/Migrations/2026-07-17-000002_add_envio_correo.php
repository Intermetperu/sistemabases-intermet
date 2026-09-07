<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEnvioCorreo extends Migration
{
    public function up()
    {
        $this->forge->addColumn('contactos', [
            'estado_envio_correo' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true, 'after' => 'unidad_negocio'],
            'correo_enviado_en'   => ['type' => 'DATETIME', 'null' => true, 'after' => 'estado_envio_correo'],
        ]);

        $this->forge->addField([
            'id'             => ['type' => 'INT', 'auto_increment' => true],
            'contacto_id'    => ['type' => 'INT', 'null' => false],
            'correo_destino' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'asunto'         => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'estado'         => ['type' => 'VARCHAR', 'constraint' => 20], // enviado | error
            'error_detalle'  => ['type' => 'TEXT', 'null' => true],
            'created_at'     => ['type' => 'DATETIME'],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('contacto_id');
        $this->forge->addKey('created_at'); // clave para la ventana de 24 horas
        $this->forge->addKey('estado');
        $this->forge->createTable('envios_correo_log');
    }

    public function down()
    {
        $this->forge->dropTable('envios_correo_log');
        $this->forge->dropColumn('contactos', ['estado_envio_correo', 'correo_enviado_en']);
    }
}
