<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();

            $table->string('product_id', 20);

            $table->string('size'); 
            $table->integer('box_qty')->nullable();
            $table->integer('carton_qty')->nullable();

            $table->boolean('status')->default(1);

            $table->timestamps();

            $table->foreign('product_id')
                ->references('id')
                ->on('products');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
