<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {

            // UUID (unique identifier tambahan selain ID)
            $table->uuid('uuid')->nullable()->unique()->after('id');

            // Audit fields
            $table->string('created_by', 20)->nullable()->after('created_at');
            $table->string('updated_by', 20)->nullable()->after('created_by');
            $table->string('deleted_by', 20)->nullable()->after('updated_by');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {

            $table->dropColumn([
                'uuid',
                'created_by',
                'updated_by',
                'deleted_by'
            ]);
        });
    }
};
