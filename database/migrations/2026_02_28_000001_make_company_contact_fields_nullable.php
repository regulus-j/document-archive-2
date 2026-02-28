<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Make company_email and company_phone nullable so they are only required
     * when the user opts to include address details during registration.
     */
    public function up(): void
    {
        Schema::table('company_accounts', function (Blueprint $table) {
            $table->string('company_email')->nullable()->change();
            $table->string('company_phone')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_accounts', function (Blueprint $table) {
            $table->string('company_email')->nullable(false)->change();
            $table->string('company_phone')->nullable(false)->change();
        });
    }
};
