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
        Schema::table('chat_messages', function (Blueprint $table) {
            // Ubah kolom receiver_id menjadi nullable
            $table->foreignId('receiver_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            // Ubah kembali kolom receiver_id menjadi not nullable (jika ingin)
            // Ini mungkin memerlukan nilai default atau penanganan data yang sudah ada
            $table->foreignId('receiver_id')->nullable(false)->change();
        });
    }
};
