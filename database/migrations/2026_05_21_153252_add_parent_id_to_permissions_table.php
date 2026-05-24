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
        Schema::table('permissions', function (Blueprint $table) {
            // Menambahkan kolom parent_id yang bersifat nullable
            $table->unsignedBigInteger('parent_id')->nullable()->after('name');

            // Opsional: Menghubungkan sebagai Foreign Key ke tabel permission_parent_group
            // Jika data di tabel parent dihapus, parent_id di permissions akan otomatis diset NULL
            $table->foreign('parent_id')
                  ->references('id')
                  ->on('permission_parent_group')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            // Hapus foreign key terlebih dahulu sebelum menghapus kolom
            $table->dropForeign(['parent_id']);
            $table->dropColumn('parent_id');
        });
    }
};
