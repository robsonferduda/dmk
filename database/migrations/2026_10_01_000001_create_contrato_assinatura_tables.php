<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContratoAssinaturaTables extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('contrato_assinatura_cas')) {
            Schema::create('contrato_assinatura_cas', function (Blueprint $table) {
                $table->bigIncrements('cd_contrato_assinatura_cas');
                $table->integer('cd_conta_correspondente_ccr')->index();
                $table->integer('cd_conta_con')->index();
                $table->string('cd_documento_autentique_cas', 100)->nullable()->unique();
                $table->string('dc_status_cas', 20)->index();
                $table->string('dc_canal_cas', 10);
                $table->string('dc_caminho_original_cas', 255);
                $table->string('dc_caminho_assinado_cas', 255)->nullable();
                $table->boolean('fl_sandbox_cas')->default(false);
                $table->text('dc_erro_cas')->nullable();
                $table->integer('cd_usuario_envio_cas')->nullable();
                $table->timestamp('dt_envio_cas')->nullable();
                $table->timestamp('dt_concluido_cas')->nullable();
                $table->timestamp('dt_recusado_cas')->nullable();
                $table->timestamp('dt_cancelado_cas')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('contrato_signatario_csi')) {
            Schema::create('contrato_signatario_csi', function (Blueprint $table) {
                $table->bigIncrements('cd_contrato_signatario_csi');
                $table->bigInteger('cd_contrato_assinatura_cas')->index();
                $table->string('dc_papel_csi', 20);
                $table->smallInteger('nu_ordem_csi');
                $table->string('nm_signatario_csi', 255)->nullable();
                $table->string('dc_email_csi', 255)->nullable();
                $table->string('dc_telefone_csi', 30)->nullable();
                $table->string('dc_public_id_csi', 100)->nullable()->index();
                $table->string('dc_link_csi', 255)->nullable();
                $table->timestamp('dt_visualizado_csi')->nullable();
                $table->timestamp('dt_assinado_csi')->nullable();
                $table->timestamp('dt_recusado_csi')->nullable();
                $table->timestamp('dt_falha_entrega_csi')->nullable();
                $table->text('dc_motivo_csi')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('autentique_evento_aev')) {
            Schema::create('autentique_evento_aev', function (Blueprint $table) {
                $table->bigIncrements('cd_autentique_evento_aev');
                $table->string('dc_evento_id_aev', 100)->unique();
                $table->string('dc_tipo_aev', 60)->index();
                $table->string('cd_documento_autentique_aev', 100)->nullable()->index();
                $table->text('js_payload_aev');
                $table->timestamp('dt_processado_aev')->nullable();
                $table->text('dc_erro_aev')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('autentique_evento_aev');
        Schema::dropIfExists('contrato_signatario_csi');
        Schema::dropIfExists('contrato_assinatura_cas');
    }
}
