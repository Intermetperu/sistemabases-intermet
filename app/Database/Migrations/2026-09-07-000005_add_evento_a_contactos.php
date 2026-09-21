<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEventoAContactos extends Migration
{
    public function up()
    {
        $this->forge->addColumn('contactos', [
            'evento_id' => [
                'type'       => 'INT',
                'null'       => true,
                'after'      => 'unidad_negocio',
            ],
            'importacion_id' => [
                'type'       => 'INT',
                'null'       => true,
                'after'      => 'evento_id',
            ],
        ]);

        $this->forge->addKey('evento_id');
        $this->forge->addKey('importacion_id');
        $this->forge->processIndexes('contactos');

        $this->db->query(
            'ALTER TABLE contactos
             ADD CONSTRAINT fk_contactos_evento
             FOREIGN KEY (evento_id) REFERENCES eventos(id) ON DELETE SET NULL'
        );

        $this->db->query(
            'ALTER TABLE contactos
             ADD CONSTRAINT fk_contactos_importacion
             FOREIGN KEY (importacion_id) REFERENCES importaciones(id) ON DELETE SET NULL'
        );
    }

    public function down()
    {
        $this->db->query('ALTER TABLE contactos DROP FOREIGN KEY fk_contactos_evento');
        $this->db->query('ALTER TABLE contactos DROP FOREIGN KEY fk_contactos_importacion');
        $this->forge->dropColumn('contactos', ['evento_id', 'importacion_id']);
    }
}
