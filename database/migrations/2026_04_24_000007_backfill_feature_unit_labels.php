<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $labels = [
            'user-limits' => 'users',
            'team-limits' => 'teams',
            'custom-roles' => 'roles',
            'storage-limits' => 'GB',
        ];

        foreach ($labels as $key => $unitLabel) {
            DB::table('features')
                ->where('key', $key)
                ->update(['unit_label' => $unitLabel]);
        }
    }

    public function down(): void
    {
        DB::table('features')
            ->whereIn('key', ['user-limits', 'team-limits', 'custom-roles', 'storage-limits'])
            ->update(['unit_label' => null]);
    }
};