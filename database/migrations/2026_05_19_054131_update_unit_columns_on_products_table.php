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
        Schema::table('products', function (Blueprint $table) {
            // 1. Hapus foreign key constraint terlebih dahulu, lalu hapus kolom 'unit_id'
            $table->dropForeign(['unit_id']);
            $table->dropColumn('unit_id');

            // 2. Tambahkan kolom satuan besar (large_unit_id) dan satuan kecil (small_unit_id)
            $table->string('sku', 100)->nullable()->after('name');
            $table->string('large_unit_id', 20)->nullable()->after('category_id');
            $table->string('small_unit_id', 20)->nullable()->after('large_unit_id');

            // 3. Tambahkan relasi foreign key untuk kedua kolom baru tersebut
            $table->foreign('large_unit_id')
                  ->references('id')
                  ->on('product_units')
                  ->nullOnDelete();

            $table->foreign('small_unit_id')
                  ->references('id')
                  ->on('product_units')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // 1. Hapus foreign key dari kolom baru
            $table->dropForeign(['large_unit_id']);
            $table->dropForeign(['small_unit_id']);
            
            // 2. Hapus kolom baru
            $table->dropColumn(['large_unit_id', 'small_unit_id']);

            // 3. Kembalikan kolom 'unit_id' yang lama beserta relasinya
            $table->string('unit_id', 20)->nullable()->after('category_id');
            $table->foreign('unit_id')
                  ->references('id')
                  ->on('product_units')
                  ->nullOnDelete();
        });
    }
};