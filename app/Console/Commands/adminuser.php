<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\Role;
use Hash;
class adminuser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:adminuser {name} {email} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates an admin user';

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
        $name = $this->argument('name');
        $email = $this->argument('email');
        $password = $this->argument('password');
        $user = new User();
        $user->name = $name;
        $user->email = $email;
        $user->password = Hash::make($password);
        $user->save();
        
        $adminIdentifier = 'urn:lti:sysrole:ims/lis/Administrator';

        $adminRole = Role::where('lti_identifier', $adminIdentifier)->get();
        if(count($adminRole) ==0) {
            $adminRole = new Role();
            $adminRole->name = 'Administrator';
            $adminRole->lti_identifier = $adminIdentifier;
            $adminRole->save();
        }

        $user->roles()->attach($adminRole);
    }
}
