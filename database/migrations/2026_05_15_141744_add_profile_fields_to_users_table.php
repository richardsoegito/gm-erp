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
        Schema::table('users', function (Blueprint $table) {

            $table->string('username')
                ->unique()
                ->after('name');

            $table->string('phone', 30)
                ->nullable()
                ->after('email');

            $table->string('avatar')
                ->nullable()
                ->after('phone');

            // alternatif nama:
            // profile_picture

            $table->timestamp('last_login_at')
                ->nullable()
                ->after('remember_token');

            $table->tinyInteger('status')
                ->default(1)
                ->comment('1=active,0=inactive')
                ->after('password');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->dropColumn([
                'username',
                'phone',
                'avatar',
                'last_login_at',
                'status'
            ]);

        });
    }
};
