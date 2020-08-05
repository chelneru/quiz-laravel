<?php

use Illuminate\Database\Seeder;
use DB;

class UsersAndClassesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $no_of_participants = 50;
        $no_of_classes = 20;

        $faker = Faker\Factory::create();
        $users_ids = [];
        $teachers_ids = [];
        $classes_ids = [];
        $participants_ids = [];
        //create the users
        try {
            DB::beginTransaction();

            for ($x = 0; $x <= $no_of_participants; $x++) {
                $first_name = $faker->firstName;
                $last_name = $faker->lastName;
                $role_id = $faker->numberBetween(1, 3);


                $new_user_id = DB::table('users')->insertGetId([
                    'name' => $first_name . ' ' . $last_name,
                    'email' => $faker->email,
                    'password' => bcrypt('secret'),
                    'u_role' => $role_id,
                    'u_department' => $faker->company,
                    'u_position' => $faker->jobTitle,

                ]);
                if ($role_id == 1) {
                    $participants_ids[] = $new_user_id;
                } else if ($role_id == 2) {
                    $teachers_ids[] = $new_user_id;
                }
                $users_ids[] = $new_user_id;
            }

            //create classes
            for ($x = 0; $x <= $no_of_classes; $x++) {
                $new_class_id = DB::table('classes')->insertGetId([
                    'class_name'=>$faker->company,
                    'class_created_by'=> $teachers_ids[array_rand($teachers_ids)],
                    'class_created_on'=>$faker->dateTime,
                    'class_active'=> 1

                ]);

                $classes_ids[] = $new_class_id;
            }
            //add participants and teachers to class
            foreach ($classes_ids as $classes_id) {
                $class_participants_no = $faker->numberBetween(1, 20);

                for($x = 0; $x<= $class_participants_no; $x++) {
                    DB::table('class_users')
                        ->insert(
                            [
                                'cu_class_id'=>$classes_id,
                                'cu_user_id'=> $participants_ids[array_rand($participants_ids)],
                                'cu_created_on'=>$faker->dateTime
                            ]
                        );
                }
            }


            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            echo $e->getMessage() . ' in file: ' . $e->getFile() . ' at line: ' . $e->getLine();
            return null;
        }


    }
}
