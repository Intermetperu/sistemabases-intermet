<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEventosTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'auto_increment' => true],

            'nombre'       => ['type' => 'VARCHAR', 'constraint' => 200, 'null' => false],
            'descripcion'  => ['type' => 'TEXT', 'null' => true],
            'fecha_evento' => ['type' => 'DATE', 'null' => true],
            'lugar'        => ['type' => 'VARCHAR', 'constraint' => 200, 'null' => true],

            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['nombre']);
        $this->forge->createTable('eventos');
    }

    public function down()
    {
        $this->forge->dropTable('eventos');
    }
}
