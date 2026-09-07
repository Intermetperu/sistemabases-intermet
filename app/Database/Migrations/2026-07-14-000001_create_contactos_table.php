<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateContactosTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'auto_increment' => true],

            'nombre'                          => ['type' => 'VARCHAR', 'constraint' => 100,  'null' => true],
            'nombre_2'                        => ['type' => 'VARCHAR', 'constraint' => 100,  'null' => true],
            'nombre_3'                        => ['type' => 'VARCHAR', 'constraint' => 100,  'null' => true],
            'nombres_completos'               => ['type' => 'VARCHAR', 'constraint' => 300,  'null' => true],
            'apellido'                        => ['type' => 'VARCHAR', 'constraint' => 200,  'null' => true],
            'apellido_2'                      => ['type' => 'VARCHAR', 'constraint' => 200,  'null' => true],
            'apellidos_completos'             => ['type' => 'VARCHAR', 'constraint' => 400,  'null' => true],
            'nombres_y_apellidos_completos'   => ['type' => 'VARCHAR', 'constraint' => 700,  'null' => true],
            'tipo_documento_identidad'        => ['type' => 'VARCHAR', 'constraint' => 100,  'null' => true],
            'nro_documento'                   => ['type' => 'VARCHAR', 'constraint' => 20,   'null' => true],
            'celular'                         => ['type' => 'VARCHAR', 'constraint' => 20,   'null' => true],
            'celular_2'                       => ['type' => 'VARCHAR', 'constraint' => 20,   'null' => true],
            'telefono'                        => ['type' => 'VARCHAR', 'constraint' => 20,   'null' => true],
            'empresa'                         => ['type' => 'VARCHAR', 'constraint' => 200,  'null' => true],
            'sector'                          => ['type' => 'VARCHAR', 'constraint' => 200,  'null' => true],
            'direccion_empresa'               => ['type' => 'TEXT',    'null' => true],
            'web_empresa'                     => ['type' => 'VARCHAR', 'constraint' => 200,  'null' => true],
            'ruc_empresa'                     => ['type' => 'VARCHAR', 'constraint' => 30,   'null' => true],
            'cargo'                           => ['type' => 'VARCHAR', 'constraint' => 200,  'null' => true],
            'linkedin'                        => ['type' => 'VARCHAR', 'constraint' => 200,  'null' => true],
            'correo_electronico'              => ['type' => 'VARCHAR', 'constraint' => 150,  'null' => true],
            'correo_electronico_2'            => ['type' => 'VARCHAR', 'constraint' => 150,  'null' => true],
            'correo_corporativo'              => ['type' => 'VARCHAR', 'constraint' => 150,  'null' => true],
            'correo_corporativo_2'            => ['type' => 'VARCHAR', 'constraint' => 150,  'null' => true],
            'status_correo_electronico'       => ['type' => 'VARCHAR', 'constraint' => 20,   'null' => true],
            'status_correo_corporativo'       => ['type' => 'VARCHAR', 'constraint' => 20,   'null' => true],
            'profesion'                       => ['type' => 'VARCHAR', 'constraint' => 200,  'null' => true],
            'observacion'                     => ['type' => 'TEXT',    'null' => true],
            'pais'                            => ['type' => 'VARCHAR', 'constraint' => 200,  'null' => true],
            'nota_origen'                     => ['type' => 'TEXT',    'null' => true],
            'unidad_negocio'                  => ['type' => 'VARCHAR', 'constraint' => 100,  'null' => true],

            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['empresa']);
        $this->forge->addKey(['pais']);
        $this->forge->addKey(['nro_documento']);
        $this->forge->addKey(['correo_electronico']);
        $this->forge->addKey(['correo_corporativo']);
        $this->forge->createTable('contactos');
    }

    public function down()
    {
        $this->forge->dropTable('contactos');
    }
}