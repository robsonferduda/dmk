<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCampanhaCadastroTables extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('campanha_cadastro_cmc')) {
            Schema::create('campanha_cadastro_cmc', function (Blueprint $table) {
                $table->bigIncrements('cd_campanha_cadastro_cmc');
                $table->integer('cd_conta_con')->index();
                $table->string('nm_campanha_cmc', 150);
                $table->string('dc_criterio_cmc', 255)->nullable();
                $table->string('dc_status_cmc', 20)->default('pausada');
                $table->integer('nu_prazo_dias_cmc')->default(7);
                $table->integer('cd_usuario_cmc')->nullable();
                $table->timestamp('dt_inicio_cmc')->nullable();
                $table->timestamp('dt_conclusao_cmc')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('campanha_cadastro_envio_cme')) {
            Schema::create('campanha_cadastro_envio_cme', function (Blueprint $table) {
                $table->bigIncrements('cd_campanha_cadastro_envio_cme');
                $table->bigInteger('cd_campanha_cadastro_cmc')->index();
                $table->integer('cd_conta_correspondente_ccr')->index();
                $table->string('nm_destinatario_cme', 255)->nullable();
                $table->string('dc_email_cme', 255);
                $table->date('dt_ultimo_processo_cme')->nullable();
                $table->integer('nu_ordem_cme')->default(0);
                $table->string('dc_token_cme', 64)->unique();
                $table->string('dc_status_cme', 20)->default('pendente')->index();
                $table->integer('nu_tentativas_cme')->default(0);
                $table->integer('nu_envios_cme')->default(0);
                $table->text('dc_erro_cme')->nullable();
                $table->timestamp('dt_envio_cme')->nullable()->index();
                $table->date('dt_prazo_cme')->nullable();
                $table->timestamp('dt_abertura_cme')->nullable();
                $table->timestamp('dt_clique_cme')->nullable();
                $table->timestamp('dt_confirmacao_cme')->nullable();
                $table->text('dc_pendencias_cme')->nullable();
                $table->timestamp('dt_pendencias_cme')->nullable();
                $table->timestamps();

                $table->unique(['cd_campanha_cadastro_cmc', 'cd_conta_correspondente_ccr'], 'campanha_cadastro_envio_cme_unico');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('campanha_cadastro_envio_cme');
        Schema::dropIfExists('campanha_cadastro_cmc');
    }
}
