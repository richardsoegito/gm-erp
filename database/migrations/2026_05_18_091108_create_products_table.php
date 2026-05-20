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
        Schema::create('products', function (Blueprint $table) {
            // ---- Identity ----
            $table->string('id', 20)->primary();          // e.g. PRD-0001
            $table->string('name', 255);
            $table->string('slug', 255)->unique();
            $table->string('brand_id', 20)->nullable();
            $table->string('category_id', 20)->nullable();
            $table->string('unit_id', 20)->nullable();
 
            // ---- Relations ----
            $table->foreign('brand_id')
                 ->references('id')
                 ->on('product_brands')
                 ->nullOnDelete();

            $table->foreign('category_id')
                 ->references('id')
                 ->on('product_categories')
                 ->nullOnDelete();

            $table->foreign('unit_id')
                 ->references('id')
                 ->on('product_units')
                 ->nullOnDelete();
 
            // ---- Details ----
            $table->text('description')->nullable();
 
            // ---- Media ----
            $table->string('thumbnail')->nullable();      // path to thumbnail image
            $table->string('video')->nullable();          // path to product video
 
            // ---- SEO ----
            $table->string('meta_title', 255)->nullable();
            $table->text('meta_description')->nullable();
 
            // ---- Status ----
            $table->boolean('status')->default(true);     // true = active
 
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
