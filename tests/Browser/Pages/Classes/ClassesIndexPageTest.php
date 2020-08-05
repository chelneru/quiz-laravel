<?php

namespace Tests\Browser\Pages\Classes;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\TeacherDashboard;
use Tests\CreatesApplication;
use Tests\DuskTestCase;


class  ClassesIndexPageTest extends DuskTestCase
{
    use CreatesApplication;
    use DatabaseMigrations;


    /**
     * A basic test example.
     *
     * @return void
     */
    public function testAccessClassesIndexPage()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new TeacherDashboard)
                ->createTeacher()
                ->click('.desktop-menu .classes')
                ->waitForLocation('/classes')
                ->assertSee('Classes');
        });
    }

}
