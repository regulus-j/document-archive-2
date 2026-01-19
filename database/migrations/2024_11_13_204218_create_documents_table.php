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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('uploader');
            $table->string('title');
            $table->longText('description');
            $table->string('category')->nullable();
            $table->string('purpose')->nullable();
            $table->string('classification')->nullable();
            $table->text('content')->nullable();
            $table->string('path');
            $table->softDeletes();
            $table->boolean('is_archived')->default(false);
            $table->timestamps();

            $table->foreign('uploader')->references('id')->on('users');
        });

        Schema::create('document_versions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('doc_id');
            $table->unsignedBigInteger('prevdoc_id')->nullable();
            $table->unsignedBigInteger('nextdoc_id')->nullable();

            $table->foreign('doc_id')->references('id')->on('documents');
            $table->foreign('prevdoc_id')->references('id')->on('documents');
            $table->foreign('nextdoc_id')->references('id')->on('documents');
        });

        Schema::create('document_status', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('doc_id');
            $table->string('status');
            $table->timestamps();

            $table->foreign('doc_id')->references('id')->on('documents');
        });

        Schema::create('document_trackingnumbers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('doc_id');
            $table->string('tracking_number');
            $table->timestamps();

            $table->foreign('doc_id')->references('id')->on('documents');
        });

        Schema::create('document_categories', function (Blueprint $table) {
            $table->id();
            $table->string('category');
            $table->timestamps();
        });

        Schema::create('document_category', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('doc_id');
            $table->unsignedBigInteger('category_id');

            $table->foreign('doc_id')->references('id')->on('documents');
            $table->foreign('category_id')->references('id')->on('document_categories');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
