<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Str;

class UserTest extends TestCase
{
    public function test_register_happy_scenario()
    {
        $response = $this->post('/api/register', [
            'name' => 'Mazen',
            'email' => Str::random(10) . '@gmail.com',
            'password' => '123456',
            'password_confirmation' => '123456'
            ]);
        
        $response->assertStatus(201);
    }

    public function test_register_existing_email()
    {
        $response = $this->post('/api/register', [
            'name' => 'Mazen',
            'email' => 'mazensayed@gmail.com',
            'password' => '123456',
            'password_confirmation' => '123456'
            ]);
        
        $response->assertStatus(404);
    }
}
