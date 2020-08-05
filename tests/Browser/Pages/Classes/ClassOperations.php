<?php


namespace Tests\Browser\Pages\Classes;


use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\TeacherDashboard;
use Tests\CreatesApplication;
use Tests\DuskTestCase;
use Faker;
class ClassOperations extends DuskTestCase
{
    use CreatesApplication;
    use DatabaseMigrations;

    public function testEditClassPageAssertName()
    {
        $this->browse(function (Browser $browser) {
            $faker = Faker\Factory::create();
            $name =  $faker->text($maxNbChars = 191);
            $description =  $faker->text($maxNbChars = 191);
            $browser->visit(new TeacherDashboard)
                ->createTeacher()
                ->createClass($name,$description)
                ->visit('/classes')
                ->click('.dropdown-trigger')
                ->pause('500')
                ->click('#dropdown2 li:first-child a')
                ->waitFor('.edit-class-page')
                ->pause('500')
                ->assertSourceHas($name);
        });
    }
    public function testEditClassPageAssertDescription()
    {
        $this->browse(function (Browser $browser) {
            $faker = Faker\Factory::create();
            $name =  $faker->text($maxNbChars = 191);
            $description =  $faker->text($maxNbChars = 191);
            $browser->visit(new TeacherDashboard)
                ->createTeacher()
                ->createClass($name,$description)
                ->visit('/classes')
                ->click('.dropdown-trigger')
                ->pause('500')
                ->click('#dropdown2 li:first-child a')
                ->waitFor('.edit-class-page')
                ->pause('500')
                ->assertSourceHas($description);
        });
    }

    public function testEditClassName()
    {
        $this->browse(function (Browser $browser) {
            $faker = Faker\Factory::create();
            $name =  $faker->text($maxNbChars = 191);
            $new_name = $faker->text($maxNbChars = 191);
            $description =  $faker->text($maxNbChars = 191);
            $browser->visit(new TeacherDashboard)
                ->createTeacher()
                ->createClass($name,$description)
                ->visit('/classes')
                ->click('.dropdown-trigger')
                ->pause('500')
                ->click('#dropdown2 li:first-child a')
                ->waitFor('.edit-class-page')
                ->type('#class_name',$new_name)
                ->click('.edit-class-btn')
                ->waitForLocation('/classes')
                ->assertSee('The class has been updated.');
        });
    }
    public function testEditClassDescription()
    {
        $this->browse(function (Browser $browser) {
            $faker = Faker\Factory::create();
            $name =  $faker->text($maxNbChars = 191);
            $new_description = $faker->text($maxNbChars = 191);
            $description =  $faker->text($maxNbChars = 191);
            $browser->visit(new TeacherDashboard)
                ->createTeacher()
                ->createClass($name,$description)
                ->visit('/classes')
                ->click('.dropdown-trigger')
                ->pause('500')
                ->click('#dropdown2 li:first-child a')
                ->waitFor('.edit-class-page')
                ->type('#class_description',$new_description)
                ->click('.edit-class-btn')
                ->waitForLocation('/classes')
                ->assertSee('The class has been updated.');
        });
    }

    public function testClassDuplicatePage()
    {
        $this->browse(function (Browser $browser) {
            $faker = Faker\Factory::create();
            $name =  $faker->text($maxNbChars = 191);
            $new_description = $faker->text($maxNbChars = 191);
            $description =  $faker->text($maxNbChars = 191);
            $browser->visit(new TeacherDashboard)
                ->createTeacher()
                ->createClass($name,$description)
                ->visit('/classes')
                ->click('.dropdown-trigger')
                ->pause('500')
                ->click('#dropdown2 li:nth-child(2) a')
                ->waitFor('.copy-class-page')
                ->assertSee('Copy a class');
        });
    }

    public function testClassDuplicateActionBasic()
    {
        $this->browse(function (Browser $browser) {
            $faker = Faker\Factory::create();
            $name =  $faker->text($maxNbChars = 191);
            $new_name = $faker->text($maxNbChars = 191);
            $description =  $faker->text($maxNbChars = 191);
            $browser->visit(new TeacherDashboard)
                ->createTeacher()
                ->createClass($name,$description)
                ->visit('/classes')
                ->click('.dropdown-trigger')
                ->pause('500')
                ->click('#dropdown2 li:nth-child(2) a')
                ->waitFor('.copy-class-page')
                ->click('.copy-class-btn')
                ->assertSee('Copy a class');
        });
    }
    //TODO test duplicate class
    //TODO test delete class

   //TODO test edit class quizzes
   //TODO test edit class participants

}
