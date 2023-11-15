<?php 

namespace Src\Database\Migrations;

use GTG\MVC\DB\Migration;

class m0001_initial extends Migration 
{
    public function up(): void
    {
        $this->db->createTable('config', function ($table) {
            $table->id();
            $table->string('meta', 50);
            $table->text('value')->nullable();
        });

        $this->db->createTable('campeonato', function ($table) {
            $table->id();
            $table->integer('usu_id');
            $table->integer('jog_id');
            $table->string('name', 100);
            $table->integer('c_status');
            $table->text('metadata')->nullable();
            $table->timestamps();
        });

        $this->db->createTable('competidor', function ($table) {
            $table->id();
            $table->integer('usu_id');
            $table->integer('cam_id');
            $table->string('name', 100);
            $table->string('img', 255)->nullable();
            $table->timestamps();
        });

        $this->db->createTable('confronto', function ($table) {
            $table->id();
            $table->integer('usu_id');
            $table->integer('cam_id');
            $table->integer('level');
            $table->integer('position');
            $table->integer('com1_id')->nullable();
            $table->integer('com2_id')->nullable();
            $table->integer('winner')->nullable();
            $table->integer('c_status');
            $table->timestamps();
        });

        $this->db->createTable('jogo', function ($table) {
            $table->id();
            $table->integer('usu_id');
            $table->string('name', 100);
            $table->timestamps();
        });

        $this->db->createTable('usuario', function ($table) {
            $table->id();
            $table->integer('utip_id');
            $table->string('name', 50);
            $table->string('email', 100);
            $table->string('password', 100);
            $table->string('token', 100);
            $table->string('slug', 100);
            $table->timestamps();
        });

        $this->db->createTable('usuario_meta', function ($table) {
            $table->id();
            $table->integer('usu_id');
            $table->string('meta', 50);
            $table->text('value')->nullable();
        });

        $this->db->createTable('usuario_tipo', function ($table) {
            $table->id();
            $table->string('name_sing', 50);
            $table->string('name_plur', 50);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        $this->db->dropTableIfExists('config');
        $this->db->dropTableIfExists('campeonato');
        $this->db->dropTableIfExists('competidor');
        $this->db->dropTableIfExists('confronto');
        $this->db->dropTableIfExists('jogo');
        $this->db->dropTableIfExists('usuario');
        $this->db->dropTableIfExists('usuario_meta');
        $this->db->dropTableIfExists('usuario_tipo');
    }
}