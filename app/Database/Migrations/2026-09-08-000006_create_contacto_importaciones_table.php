<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateContactoImportacionesTable extends Migration
{
    /**
     * Bitácora histórica: cada fila dice "este contacto fue tocado
     * (insertado o actualizado) por esta importación, para este evento".
     *
     * A diferencia de contactos.evento_id / contactos.importacion_id (que
     * solo guardan el ÚLTIMO evento/carga y se sobrescriben en cada nueva
     * importación), esta tabla nunca se sobrescribe: por eso permite:
     *   - Ver exactamente qué contactos trajo una carga puntual del historial,
     *     aunque ese contacto haya sido tocado después por otra carga.
     *   - Buscar "todos los contactos que alguna vez asistieron al evento X",
     *     aunque su último evento registrado sea otro.
     */
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'auto_increment' => true],

            'contacto_id'    => ['type' => 'INT', 'null' => false],
            'importacion_id' => ['type' => 'INT', 'null' => false],
            'evento_id'      => ['type' => 'INT', 'null' => true],

            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('contacto_id');
        $this->forge->addKey('importacion_id');
        $this->forge->addKey('evento_id');
        $this->forge->createTable('contacto_importaciones');

        $this->db->query(
            'ALTER TABLE contacto_importaciones
             ADD CONSTRAINT fk_ci_contacto
             FOREIGN KEY (contacto_id) REFERENCES contactos(id) ON DELETE CASCADE'
        );
        $this->db->query(
            'ALTER TABLE contacto_importaciones
             ADD CONSTRAINT fk_ci_importacion
             FOREIGN KEY (importacion_id) REFERENCES importaciones(id) ON DELETE CASCADE'
        );
        $this->db->query(
            'ALTER TABLE contacto_importaciones
             ADD CONSTRAINT fk_ci_evento
             FOREIGN KEY (evento_id) REFERENCES eventos(id) ON DELETE SET NULL'
        );
    }

    public function down()
    {
        $this->db->query('ALTER TABLE contacto_importaciones DROP FOREIGN KEY fk_ci_contacto');
        $this->db->query('ALTER TABLE contacto_importaciones DROP FOREIGN KEY fk_ci_importacion');
        $this->db->query('ALTER TABLE contacto_importaciones DROP FOREIGN KEY fk_ci_evento');
        $this->forge->dropTable('contacto_importaciones');
    }
}
