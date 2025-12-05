<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestAdminAccess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-admin-access';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user = \App\Models\User::first();

        if ($user) {
            $this->info('User found: ' . $user->name . ' (' . $user->email . ')');

            // Check if user has Super Admin role
            if ($user->hasRole('Super Admin')) {
                $this->info('User has Super Admin role');
            } else {
                $this->info('User does not have Super Admin role');
                // Assign the role
                $user->assignRole('Super Admin');
                $this->info('Assigned Super Admin role to user');
            }
        } else {
            $this->error('No user found');
        }
    }
}