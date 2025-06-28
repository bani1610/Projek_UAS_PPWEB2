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
            $table->foreignId('group_id')->nullable()->constrained('groups')->onDelete('cascade')->after('receiver_id');
            // PENTING: Jika pesan HANYA bisa ke user ATAU grup, Anda mungkin perlu membuat `receiver_id` nullable.
            // Jika tidak, biarkan `receiver_id` tetap NOT NULL untuk pesan personal, dan `group_id` untuk grup.
            // Atau pertimbangkan hubungan polimorfik untuk target pesan.
            // Untuk kesederhanaan awal, kita asumsikan bisa salah satu.
            // $table->foreignId('receiver_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropConstrainedForeignId('group_id');
            // $table->foreignId('receiver_id')->change(); // Kembalikan ke NOT NULL jika diubah di atas
        });
    }
};
