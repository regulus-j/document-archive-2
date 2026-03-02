<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds auto-archive scheduling support for office leads:
     *   - archive_schedule_days: how many days after resolution to auto-archive (null = disabled)
     *   - archive_last_run_at:   timestamp of the last auto-archive sweep for this office
     */
    public function up(): void
    {
        Schema::table('offices', function (Blueprint $table) {
            $table->unsignedSmallInteger('archive_schedule_days')
                  ->nullable()
                  ->after('office_lead')
                  ->comment('Auto-archive resolved docs after this many days (null = disabled)');

            $table->timestamp('archive_last_run_at')
                  ->nullable()
                  ->after('archive_schedule_days')
                  ->comment('Timestamp of the most recent auto-archive run for this office');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offices', function (Blueprint $table) {
            $table->dropColumn(['archive_schedule_days', 'archive_last_run_at']);
        });
    }
};
