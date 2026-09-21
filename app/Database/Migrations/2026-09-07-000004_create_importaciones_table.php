<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateImportacionesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'auto_increment' => true],

            'evento_id'      => ['type' => 'INT', 'null' => true],
            'nombre_archivo' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'total_filas'    => ['type' => 'INT', 'null' => false, 'default' => 0],
            'insertados'     => ['type' => 'INT', 'null' => false, 'default' => 0],
            'actualizados'   => ['type' => 'INT', 'null' => false, 'default' => 0],
            'usuario_id'     => ['type' => 'INT', 'null' => true],
            'usuario_nombre' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],

            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['evento_id']);
        $this->forge->addForeignKey('evento_id', 'eventos', 'id', '', 'SET NULL');
        $this->forge->createTable('importaciones');
    }

    public function down()
    {
        $this->forge->dropTable('importaciones');
    }
}
