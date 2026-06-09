<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adiciona as colunas necessárias para a integração Z-API na tabela conta_con.
 *
 * Campos adicionados:
 *   - fl_zapi_ativo_con         : boolean — habilita a integração Z-API para esta conta
 *   - ds_zapi_instance_id_con   : string  — ID da instância no painel Z-API
 *   - ds_zapi_token_con         : string  — Token da instância Z-API
 *   - ds_zapi_client_token_con  : string  — Client-Token de segurança (cabeçalho extra)
 *
 * A integração Z-API tem prioridade sobre a ChatPro (via WhatsappDispatcher).
 * O ChatPro continua funcionando como fallback enquanto houver contas configuradas.
 */
class AddZapiFieldsToContaConTable extends Migration
{
    public function up()
    {
        Schema::table('conta_con', function (Blueprint $table) {
            $table->boolean('fl_zapi_ativo_con')->default(false)->nullable()->after('ds_chatpro_token_con');
            $table->string('ds_zapi_instance_id_con', 100)->nullable()->after('fl_zapi_ativo_con');
            $table->string('ds_zapi_token_con', 255)->nullable()->after('ds_zapi_instance_id_con');
            $table->string('ds_zapi_client_token_con', 255)->nullable()->after('ds_zapi_token_con');
        });
    }

    public function down()
    {
        Schema::table('conta_con', function (Blueprint $table) {
            $table->dropColumn([
                'fl_zapi_ativo_con',
                'ds_zapi_instance_id_con',
                'ds_zapi_token_con',
                'ds_zapi_client_token_con',
            ]);
        });
    }
}
