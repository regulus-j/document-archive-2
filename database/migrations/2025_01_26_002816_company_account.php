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
        //
        Schema::create('company_accounts', function (Blueprint $table) {
            $table->id();

            //owner of the company
            $table->unsignedBigInteger('user_id');

            //company attributes
            $table->string('company_name');
            $table->string('registered_name')->nullable()->default('');
            $table->string('company_email')->nullable()->default('');
            $table->string('company_phone')->nullable()->default('');

            //demographic attributes
            $table->string('industry')->nullable();
            $table->string('company_size')->nullable();

            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('company_addresses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('address');
            $table->string('city');
            $table->string('state');
            $table->string('country');
            $table->string('zip_code');
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('company_accounts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
