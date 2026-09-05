<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('picks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->index('picks_user_id_foreign');
            $table->float('score');
            $table->unsignedBigInteger('d1_id')->nullable()->index('picks_d1_id_foreign');
            $table->unsignedBigInteger('d2_id')->nullable()->index('picks_d2_id_foreign');
            $table->unsignedBigInteger('d3_id')->nullable()->index('picks_d3_id_foreign');
            $table->double('bonus');
            $table->integer('session_key');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('picks');
    }
};
