<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->fill_users_table();
    }

    public function fill_users_table()
    {
        DB::table('users')->insert([
            'name' => 'mazen',
            'email' => 'mazensayed@gmail.com',
            'password' => bcrypt('mazen@gmail.com')
        ]);
    }

}