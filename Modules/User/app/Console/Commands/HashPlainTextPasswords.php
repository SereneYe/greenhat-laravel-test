<?php

namespace Modules\User\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Modules\User\Models\User;

class HashPlainTextPasswords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:hash-passwords';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hash plain text passwords for existing users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for users with plain text passwords...');

        $users = User::all();
        $count = 0;

        foreach ($users as $user) {
            // Check if the password is not hashed (doesn't start with $2y$)
            if (!str_starts_with($user->password, '$2y$')) {
                $plainPassword = $user->password;

                // Hash the plain text password
                $user->password = Hash::make($plainPassword);
                $user->save();

                $count++;
                $this->info("Fixed password for user: {$user->email}");
            }
        }

        if ($count > 0) {
            $this->info("Successfully hashed passwords for {$count} users.");
        } else {
            $this->info("No users with plain text passwords found.");
        }

        return Command::SUCCESS;
    }
}
