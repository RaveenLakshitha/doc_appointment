<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class CheckDoctor extends Command
{
    protected $signature = 'check:doctor';
    protected $description = 'Check if browser test doctor was created';

    public function handle()
    {
        $u = User::where('email', 'browser.doctor@example.com')->with('roles')->first();
        if ($u) {
            $this->info("USER FOUND. Roles: " . $u->roles->pluck('name')->join(', '));
        }
        else {
            $this->error("NOT FOUND");
        }
    }
}
