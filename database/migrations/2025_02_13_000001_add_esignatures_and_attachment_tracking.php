<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add uploaded_by to document_attachments so we know who uploaded each attachment
        Schema::table('document_attachments', function (Blueprint $table) {
            $table->unsignedBigInteger('uploaded_by')->nullable()->after('path');
            $table->foreign('uploaded_by')->references('id')->on('users')->onDelete('set null');
        });

        // Create e-signatures table
        Schema::create('e_signatures', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('document_id');
            $table->unsignedBigInteger('workflow_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->string('action'); // approved, rejected, acknowledged, commented, returned
            $table->string('signature_path'); // stored signature image path
            $table->string('full_name'); // signer's full name at time of signing
            $table->string('position')->nullable(); // signer's position/title
            $table->string('ip_address')->nullable();
            $table->timestamp('signed_at');
            $table->timestamps();

            $table->foreign('document_id')->references('id')->on('documents')->onDelete('cascade');
            $table->foreign('workflow_id')->references('id')->on('document_workflows')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('e_signatures');

        Schema::table('document_attachments', function (Blueprint $table) {
            $table->dropForeign(['uploaded_by']);
            $table->dropColumn('uploaded_by');
        });
    }
};
