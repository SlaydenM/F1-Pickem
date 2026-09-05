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
        Schema::table('picks', function (Blueprint $table) {
            $table->foreign(['d1_id'])->references(['id'])->on('drivers')->onUpdate('no action')->onDelete('set null');
            $table->foreign(['d2_id'])->references(['id'])->on('drivers')->onUpdate('no action')->onDelete('set null');
            $table->foreign(['d3_id'])->references(['id'])->on('drivers')->onUpdate('no action')->onDelete('set null');
            $table->foreign(['user_id'])->references(['id'])->on('users')->onUpdate('no action')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('picks', function (Blueprint $table) {
            $table->dropForeign('picks_d1_id_foreign');
            $table->dropForeign('picks_d2_id_foreign');
            $table->dropForeign('picks_d3_id_foreign');
            $table->dropForeign('picks_user_id_foreign');
        });
    }
};
