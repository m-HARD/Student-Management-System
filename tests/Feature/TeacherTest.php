<?php

use App\Models\Teacher;
use App\Models\User;

describe('Teachers Crud', function () {
    test('Unauthenticated users cant view the Index page', function () {
        $response = $this->get('/teachers');

        $response->assertRedirect('/login');
    });

    test('authenticated users can view the Index page', function () {
        $response = $this->signIn()->get('/teachers');

        $response->assertOk();
        $response->assertStatus(200);
    });
    test('authenticated users can view teachers details', function () {
        $teacher = Teacher::factory()->create();
        $response = $this->signIn()->get('/teachers');

        $response->assertOk();
        $response->assertSee($teacher->name);
    });
    test('authenticated users can view the create page', function () {
        $response = $this->signIn()->get('/teachers/create');

        $response->assertOk();
        $response->assertStatus(200);
    });

    test('authenticated users can add teachers', function () {
        $teacher = [
            'name' => 'Ahmed',
            'email' => 'ahmed@example.com',
            'phone' => '01234567890',
            'date_of_recruit' => now(),
            'base_salary'  => 20000,
            'specialty' => 'Arabic'
        ];
        $response = $this->signIn()->post('/teachers', $teacher);
        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/teachers');
        $this->assertDatabaseHas('teachers', [
            'name' => 'Ahmed'
        ]);
    });

    test('authenticated users can modify teachers details', function () {
        $teacher = Teacher::factory()->create([
            'name' => 'Ali Omer',
            'email' => 'ali@example.com',
            'phone' => '01234567890',
            'date_of_recruit' => now(),
            'base_salary'  => 40000,
            'specialty' => 'Arabic'
        ]);
        $newTeacher = [
            'name' => 'Ali Adil',
            'email' => 'ali.eng@example.com',
            'phone' => '01234567890',
            'date_of_recruit' => now(),
            'base_salary'  => 40000,
            'specialty' => 'Arabic'
        ];
        $response = $this->signIn()->put('/teachers/' . $teacher->id, $newTeacher);
        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/teachers');
        $this->assertDatabaseHas('teachers', [
            'email' => 'ali.eng@example.com'
        ]);
    });

    test('teachers can be deleted', function () {
        $teacher = [
            'id' => 1,
            'name' => 'Ahmed',
            'email' => 'ahmed@example.com',
            'phone' => '01234567890',
            'date_of_recruit' => now(),
            'base_salary'  => 20000,
            'specialty' => 'Arabic'
        ];
        $response = $this->signIn()->post('/teachers', $teacher);

        $deleteResponse = $this->signIn()->delete('/teachers/' . $teacher['id'], $teacher);
        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/teachers');
    });
});
