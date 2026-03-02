<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Change payment_method from enum to string(50) to support 'paymongo' and future methods
        DB::statement("ALTER TABLE subscription_payments MODIFY COLUMN payment_method VARCHAR(50) NOT NULL DEFAULT 'other'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE subscription_payments MODIFY COLUMN payment_method ENUM('credit_card','paypal','bank_transfer','gcash','other') NOT NULL DEFAULT 'other'");
    }
};
