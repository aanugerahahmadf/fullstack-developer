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

            // Check if user has super-admin role
            if ($user->hasRole('super-admin')) {
                $this->info('User has super-admin role');
            } else {
                $this->info('User does not have super-admin role');
                // Assign the role
                $user->assignRole('super-admin');
                $this->info('Assigned super-admin role to user');
            }
        } else {
            $this->error('No user found');
        }
    }
}
