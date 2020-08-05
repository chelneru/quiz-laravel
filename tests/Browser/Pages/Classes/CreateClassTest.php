<?php

namespace Tests\Feature\Classes;

use Codeception\PHPUnit\Constraint\Page;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\TeacherDashboard;
use Tests\CreatesApplication;
use Tests\DuskTestCase;
use Faker;


class  CreateClassTest extends DuskTestCase
{
    use CreatesApplication;
    use DatabaseMigrations;

    public function testAccessCreateClassPage()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new TeacherDashboard)
                ->createTeacher()
                ->click('.desktop-menu .classes')
                ->waitForLocation('/classes')
                ->click('.action-buttons-space .btn')
                ->waitForLocation('/class/create-class')
                ->assertSee('Create a class');
        });
    }
    public function testCreateClassAction() {
        $this->browse(function (Browser $browser) {
            $faker = Faker\Factory::create();
            $name = $faker->text($maxNbChars = 191);
            $browser->visit(new TeacherDashboard)
                ->createTeacher()
                ->click('.desktop-menu .classes')
                ->waitForLocation('/classes')
                ->click('.action-buttons-space .btn')
                ->waitForLocation('/class/create-class')
                ->type('#class_name',$name )
                ->type('#class_description', $faker->text($maxNbChars = 191))
                ->screenshot('test')
                ->click('.create-class-btn')
                ->waitFor('.class-additional-info-page',7)
                ->assertSee('Class '.$name.' has been created.');

        });
    }
    public function testCreateClassActionValidation() {
        $this->browse(function (Browser $browser) {
            $faker = Faker\Factory::create();
            $browser->visit(new TeacherDashboard)
                ->createTeacher()
                ->click('.desktop-menu .classes')
                ->waitForLocation('/classes')
                ->click('.action-buttons-space .btn')
                ->waitForLocation('/class/create-class')
                ->type('#class_description', $faker->text($maxNbChars = 191))
                ->click('.create-class-btn')
                ->assertSee('The name of the class is missing.');

        });
    }

    public function testCreateClassActionTextOverflow() {
        $this->browse(function (Browser $browser) {
            $faker = Faker\Factory::create();
            $name = $faker->sentence($nbWords = 255);
            $browser->visit(new TeacherDashboard)
                ->createTeacher()
                ->click('.desktop-menu .classes')
                ->waitForLocation('/classes')
                ->click('.action-buttons-space .btn')
                ->waitForLocation('/class/create-class')
                ->type('#class_name',$name )
                ->type('#class_description', $faker->text($maxNbChars = 255))
                ->click('.create-class-btn')
                ->waitFor('.class-additional-info-page',7)
                ->assertSee('Class '.substr($name,0,191).' has been created.');

        });
    }
    public function testCreateClassActionAddNewQuizPage() {
        $this->browse(function (Browser $browser) {
            $faker = Faker\Factory::create();
            $name =  $faker->text($maxNbChars = 20);
            $browser->visit(new TeacherDashboard)
                ->createTeacher()
                ->click('.desktop-menu .classes')
                ->waitForLocation('/classes')
                ->click('.action-buttons-space .btn')
                ->waitForLocation('/class/create-class')
                ->type('#class_name',$name )
                ->type('#class_description', $faker->text($maxNbChars = 255))
                ->click('.create-class-btn')
                ->waitFor('.class-additional-info-page',7)
                ->click('.create-new-quiz')
                ->waitFor('.create-quiz-page')
                ->assertSourceHas($name);

        });
    }

    public function testCreateClassActionAddExistingQuizPage() {
        $this->browse(function (Browser $browser) {
            $faker = Faker\Factory::create();
            $name =  $faker->text($maxNbChars = 20);
            $browser->visit(new TeacherDashboard)
                ->createTeacher()
                ->click('.desktop-menu .classes')
                ->waitForLocation('/classes')
                ->click('.action-buttons-space .btn')
                ->waitForLocation('/class/create-class')
                ->type('#class_name',$name )
                ->type('#class_description', $faker->text($maxNbChars = 255))
                ->click('.create-class-btn')
                ->waitFor('.class-additional-info-page',7)
                ->click('#add_quizzes')
                ->pause(1000)
                ->assertSee('ADD QUIZZES TO THE CLASS');

        });
    }
    public function testCreateClassActionInviteParticipantsPage() {
        $this->browse(function (Browser $browser) {
            $faker = Faker\Factory::create();
            $name =  $faker->text($maxNbChars = 20);
            $browser->visit(new TeacherDashboard)
                ->createTeacher()
                ->click('.desktop-menu .classes')
                ->waitForLocation('/classes')
                ->click('.action-buttons-space .btn')
                ->waitForLocation('/class/create-class')
                ->type('#class_name',$name )
                ->type('#class_description', $faker->text($maxNbChars = 255))
                ->click('.create-class-btn')
                ->waitFor('.class-additional-info-page',7)
                ->click('.invite-participants')
                ->waitFor('.invite-participants-page',7)
                ->assertSee('Invite participants');
        });
    }

}
