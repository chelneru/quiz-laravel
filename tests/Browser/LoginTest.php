<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Faker;

class LoginTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testLoginPageDisplay()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->assertSee('login');
        });
    }

    public function testRegisterPageDisplay()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                ->assertSee('register');
        });
    }

    public function testForgotPasswordPage()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/password/reset')
                ->assertSee('Reset Password');
        });
    }

    public function testRegisterBasicTeacherFlow()
    {

        $this->browse(function (Browser $browser) {
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
                ->waitForLocation('/home')
                ->assertSee('Dashboard');
        });
    }

    public function testRegisterFullInformationTeacherFlow()
    {

        $this->browse(function (Browser $browser) {
            $faker = Faker\Factory::create();
            $fake_pass = $faker->password;
            $browser->visit('/register')
                ->click('.teacher-reg')
                ->type('@email', $faker->email)
                ->type('@password', $fake_pass)
                ->type('@password_confirmation', $fake_pass)
                ->type('@first_name', $faker->firstName)
                ->type('@last_name', $faker->lastName)
                ->type('#teacher-department', $faker->company)
                ->type('#teacher-position', $faker->jobTitle)
                ->click('button[type=submit]')
                ->waitForLocation('/home')
                ->assertSee('Dashboard');
        });
    }
    public function testRegisterParticipantFlow()
    {
        $this->browse(function (Browser $browser) {
            $faker = Faker\Factory::create();
            $fake_pass = $faker->password;
            $browser->visit('/register')
                ->click('.participant-reg')
                ->type('@email', $faker->email)
                ->type('@password', $fake_pass)
                ->type('@password_confirmation', $fake_pass)
                ->type('@first_name', $faker->firstName)
                ->type('@last_name', $faker->lastName)
                ->click('button[type=submit]')
                ->waitForLocation('/home')
                ->assertSee('Dashboard');
        });
    }
}
