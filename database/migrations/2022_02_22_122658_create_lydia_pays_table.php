<?php

use DataGrade\LydiaPay\Models\LydiaPay;
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
        Schema::create('lydia_pays', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->index()->nullable();
            $table->string('driver')->index();
            $table->string('order_id')->index();
            $table->integer('foreign_id')->index()->nullable();
            $table->integer('status')->index()->default(LydiaPay::status_waiting);
            $table->decimal('price');
            $table->string('currency')->nullable();
            $table->string('model')->nullable();
            $table->json('response')->nullable();
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
        Schema::dropIfExists('lydia_pays');
    }
};
