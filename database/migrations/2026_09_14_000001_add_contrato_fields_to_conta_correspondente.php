<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddContratoFieldsToContaCorrespondente extends Migration
{
    public function up()
    {
        if (! Schema::hasColumn('conta_correspondente_ccr', 'fl_contrato_gerado_ccr')) {
            Schema::table('conta_correspondente_ccr', function (Blueprint $table) {
                $table->boolean('fl_contrato_gerado_ccr')->default(false);
            });
        }

        if (! Schema::hasColumn('conta_correspondente_ccr', 'fl_contrato_assinado_ccr')) {
            Schema::table('conta_correspondente_ccr', function (Blueprint $table) {
                $table->boolean('fl_contrato_assinado_ccr')->default(false);
            });
        }

        if (! Schema::hasColumn('conta_correspondente_ccr', 'dt_contrato_gerado_ccr')) {
            Schema::table('conta_correspondente_ccr', function (Blueprint $table) {
                $table->timestamp('dt_contrato_gerado_ccr')->nullable();
            });
        }

        if (! Schema::hasColumn('conta_correspondente_ccr', 'dc_caminho_contrato_ccr')) {
            Schema::table('conta_correspondente_ccr', function (Blueprint $table) {
                $table->string('dc_caminho_contrato_ccr', 255)->nullable();
            });
        }
    }

    public function down()
    {
        Schema::table('conta_correspondente_ccr', function (Blueprint $table) {
            $cols = [];

            if (Schema::hasColumn('conta_correspondente_ccr', 'dt_contrato_gerado_ccr')) {
                $cols[] = 'dt_contrato_gerado_ccr';
            }
            if (Schema::hasColumn('conta_correspondente_ccr', 'dc_caminho_contrato_ccr')) {
                $cols[] = 'dc_caminho_contrato_ccr';
            }

            if (! empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
}
