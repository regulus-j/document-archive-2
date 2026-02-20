<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('company_accounts', function (Blueprint $table) {
            $table->string('logo')->nullable()->after('company_phone');
            $table->string('color_theme')->default('blue')->after('logo');
        });
    }

    public function down(): void
    {
        Schema::table('company_accounts', function (Blueprint $table) {
            $table->dropColumn(['logo', 'color_theme']);
        });
    }
};
