<?php

namespace App\Console\Commands;

use App\User;
use DB;
use Illuminate\Console\Command;

class AddAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add:admin {--email=} {--db=} {--pass=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add admin';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $email = $this->option('email');
        $db = $this->option('db');
        $pass = $this->option('pass');
        if ($email) {
            $this->line('Create Admin with email='.$email);
            DB::connection($db)->table('users')->insert([
                'email' => $email,
                'permission_id' => 1,
                'username' => 'Admin',
                'password' => md5($pass)
            ]);
            $this->line('Done');
        }

    }
}
