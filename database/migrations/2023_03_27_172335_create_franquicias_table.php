<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('franquicias', function (Blueprint $table) {
            $table->id();
            $table->string('localidad');
            $table->string('nombre');
            $table->string('barrio');
            $table->string('direccion');
            $table->bigInteger('telefono');
            $table->string('urlIframeMap',500);
            $table->string('urlGoogleMaps',500);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('franquicias');
    }
};
