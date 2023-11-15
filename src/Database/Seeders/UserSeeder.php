<?php 

namespace Src\Database\Seeders;

use GTG\MVC\DB\Seeder;
use Src\Models\User;

class UserSeeder extends Seeder 
{
    public function run(): void 
    {
        User::insertMany([
            [
                'utip_id' => User::UT_ADMIN,
                'name' => 'Admin',
                'password' => 'viva@net12',
                'email' => 'admin@vivadoracao.online',
                'slug' => 'adm',
                'token' => md5('admin@vivadoracao.online')
            ]
        ]);
    }
}