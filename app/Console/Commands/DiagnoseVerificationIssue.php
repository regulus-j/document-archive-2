<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DiagnoseVerificationIssue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'diagnose:email-verification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Diagnose email verification issues on this server';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Email Verification Diagnosis Report');
        $this->line('');
        $this->line('═══════════════════════════════════════════════════════════');
        
        // 1. Check database schema
        $this->line('📊 Database Schema Check:');
        $this->checkDatabaseSchema();
        $this->line('');

        // 2. Check configuration
        $this->line('⚙️  Configuration Check:');
        $this->checkConfiguration();
        $this->line('');

        // 3. Check migrations
        $this->line('🔄 Migration Status:');
        $this->checkMigrationStatus();
        $this->line('');

        // 4. Check email configuration
        $this->line('📧 Mail Configuration Check:');
        $this->checkMailConfiguration();
        $this->line('');

        // 5. Test a user
        $this->line('👤 User Data Sample:');
        $this->testUserData();
        $this->line('');

        $this->info('✅ Diagnosis complete. Check the issues above.');
    }

    private function checkDatabaseSchema()
    {
        $hasVerificationCode = Schema::hasColumn('users', 'verification_code');
        $hasVerificationExpires = Schema::hasColumn('users', 'verification_code_expires_at');
        $hasEmailVerified = Schema::hasColumn('users', 'email_verified_at');

        $this->line('');
        $this->line('Checking users table columns:');
        
        if ($hasVerificationCode) {
            $this->line('  ✅ verification_code column exists');
        } else {
            $this->line('  ❌ verification_code column MISSING (migration not run?)');
        }

        if ($hasVerificationExpires) {
            $this->line('  ✅ verification_code_expires_at column exists');
        } else {
            $this->line('  ❌ verification_code_expires_at column MISSING (migration not run?)');
        }

        if ($hasEmailVerified) {
            $this->line('  ✅ email_verified_at column exists');
        } else {
            $this->line('  ❌ email_verified_at column MISSING');
        }

        if (!$hasVerificationCode || !$hasVerificationExpires) {
            $this->warn('');
            $this->warn('⚠️  ACTION REQUIRED: Run migrations on production:');
            $this->warn('    php artisan migrate --force');
        }
    }

    private function checkConfiguration()
    {
        $this->line('');
        $this->line('Configuration values:');
        
        $this->line('  APP_ENV: ' . config('app.env'));
        $this->line('  APP_DEBUG: ' . (config('app.debug') ? 'true' : 'false'));
        $this->line('  APP_URL: ' . config('app.url'));
        $this->line('  APP_KEY: ' . (strlen(config('app.key')) > 0 ? 'SET (' . strlen(config('app.key')) . ' chars)' : 'NOT SET'));
        $this->line('  MAIL_MAILER: ' . config('mail.default'));
        
        if (config('app.env') === 'production' && config('app.debug')) {
            $this->warn('');
            $this->warn('⚠️  WARNING: Debug mode is enabled in production!');
        }

        // Check APP_URL format
        $url = config('app.url');
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            $this->warn('');
            $this->warn('❌ APP_URL is not a valid URL: ' . $url);
        } else {
            $this->line('  ✅ APP_URL format is valid');
        }
    }

    private function checkMigrationStatus()
    {
        $this->line('');
        
        try {
            $migrations = DB::table('migrations')
                ->where('migration', 'like', '%verification_code%')
                ->get();

            if ($migrations->count() > 0) {
                $this->line('  ✅ Verification code migration found:');
                foreach ($migrations as $migration) {
                    $this->line('     - ' . $migration->migration);
                }
            } else {
                $this->warn('  ❌ No verification code migrations in migration history');
                $this->warn('     Run: php artisan migrate --force');
            }
        } catch (\Exception $e) {
            $this->error('  ❌ Could not read migrations table: ' . $e->getMessage());
        }
    }

    private function checkMailConfiguration()
    {
        $this->line('');
        $this->line('Mail configuration:');
        
        $driver = config('mail.default');
        $this->line('  Driver: ' . $driver);

        if ($driver === 'log') {
            $this->warn('  ⚠️  Using log driver - emails won\'t be sent, only logged');
        } elseif ($driver === 'mailgun') {
            $domain = config('mail.mailers.mailgun.domain');
            $secret = config('mail.mailers.mailgun.secret');
            
            $this->line('  Mailgun Domain: ' . ($domain ? '✅ ' . $domain : '❌ NOT SET'));
            $this->line('  Mailgun Secret: ' . ($secret ? '✅ SET' : '❌ NOT SET'));
        } elseif ($driver === 'smtp') {
            $this->line('  SMTP Host: ' . config('mail.mailers.smtp.host'));
            $this->line('  SMTP Port: ' . config('mail.mailers.smtp.port'));
        }

        $fromAddress = config('mail.from.address');
        if (!$fromAddress) {
            $this->warn('  ❌ MAIL_FROM_ADDRESS is not configured');
        } else {
            $this->line('  From Address: ✅ ' . $fromAddress);
        }
    }

    private function testUserData()
    {
        $this->line('');
        
        try {
            $user = \App\Models\User::first();
            
            if (!$user) {
                $this->line('  No users in database yet');
                return;
            }

            $this->line('  First user: ' . $user->email);
            $this->line('  Email verified at: ' . ($user->email_verified_at ? 'YES' : 'NO'));
            $this->line('  Has verification code: ' . ($user->verification_code ? 'YES' : 'NO'));
            
            if ($user->verification_code_expires_at) {
                $status = $user->verification_code_expires_at->isPast() ? 'EXPIRED' : 'VALID';
                $this->line('  Code expiration: ' . $user->verification_code_expires_at . ' (' . $status . ')');
            }
        } catch (\Exception $e) {
            $this->error('  Error reading user data: ' . $e->getMessage());
        }
    }
}
