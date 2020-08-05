<?php


namespace Tests\Browser\Pages;

use Faker;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\CreatesApplication;

class TeacherDashboard extends Page
{
    use CreatesApplication;
    use DatabaseMigrations;
     public function url()
     {
         return '/login';
     }

     public function createTeacher(Browser $browser)
    {
        $faker = Faker\Factory::create();
        $fake_pass = $faker->password;
        $browser->visit('/register')
            ->click('.teacher-reg')
            ->type('@email', $faker->email)
            ->type('@password', $fake_pass)
            ->type('@password_confirmation', $fake_pass)
            ->type('@first_name', $faker->firstName)
            ->type('@last_name', $faker->lastName)
            ->click('button[type=submit]')
            ->waitForLocation('/home');
    }
    public function createClass(Browser $browser,$name,$description)
    {
        $browser->visit('/class/create-class')
            ->waitForLocation('/class/create-class')
            ->type('#class_name',$name )
            ->type('#class_description', $description)
            ->click('.create-class-btn')
            ->waitFor('.class-additional-info-page',7);
    }
}
