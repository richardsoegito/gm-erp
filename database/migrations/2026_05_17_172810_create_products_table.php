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

            $table->string('id', 20)->primary();

            $table->uuid('uuid')->unique();

            /*
            |--------------------------------------------------------------------------
            | Product Info
            |--------------------------------------------------------------------------
            */

            $table->string('code', 50)->unique();

            $table->string('name');

            $table->text('description')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Relations
            |--------------------------------------------------------------------------
            */

            $table->string('brand_id', 20)
                ->nullable();

            $table->string('category_id', 20)
                ->nullable();

            $table->string('unit_id', 20)
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Media
            |--------------------------------------------------------------------------
            */

            $table->string('thumbnail')
                ->nullable();

            $table->string('video')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            $table->tinyInteger('status')
                ->default(1);

            /*
            |--------------------------------------------------------------------------
            | Audit
            |--------------------------------------------------------------------------
            */

            $table->string('created_by', 20)
                ->nullable();

            $table->string('updated_by', 20)
                ->nullable();

            $table->string('deleted_by', 20)
                ->nullable();

            $table->timestamp('deleted_at')
                ->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Index
            |--------------------------------------------------------------------------
            */

            $table->index('brand_id');

            $table->index('category_id');

            $table->index('unit_id');

            /*
            |--------------------------------------------------------------------------
            | Foreign Key
            |--------------------------------------------------------------------------
            */

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