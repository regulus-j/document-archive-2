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
        Schema::create('workflow_delegation_chain', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('workflow_id');
            $table->unsignedBigInteger('delegator_workflow_id');
            $table->unsignedBigInteger('delegator_user_id');
            $table->unsignedBigInteger('delegate_user_id');
            $table->enum('delegation_type', ['retain', 'delegate']);
            $table->integer('depth_level')->default(0);
            $table->timestamps();

            $table->foreign('workflow_id')->references('id')->on('document_workflows')->onDelete('cascade');
            $table->foreign('delegator_workflow_id')->references('id')->on('document_workflows')->onDelete('cascade');
            $table->foreign('delegator_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('delegate_user_id')->references('id')->on('users')->onDelete('cascade');

            $table->index('workflow_id');
            $table->index('delegator_workflow_id');
            $table->index('depth_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_delegation_chain');
    }
};
