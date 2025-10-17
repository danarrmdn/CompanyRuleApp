<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class UpdateUserPasswords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-user-passwords';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update passwords for users where password_change_at is NULL';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::whereNull('password_change_at')->get();

        if ($users->isEmpty()) {
            $this->info('No users found with password_change_at as NULL.');
            return;
        }

        foreach ($users as $user) {
            $user->password = Hash::make('shokubai');
            $user->save();
        }

        $this->info($users->count() . ' user passwords have been updated to "shokubai".');
    }
}