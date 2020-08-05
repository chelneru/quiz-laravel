<?php

use Illuminate\Database\Seeder;
use DB;

/**
 * Created by PhpStorm.
 * User: au614698
 * Date: 03-12-2018
 * Time: 15:42
 */
class QuizAndQuestionsSeeder extends Seeder
{
    public function run()
    {
        $no_of_questions = 200;
        $no_of_quizzes = 100;
        $no_of_quizzes_added = 80;
        $faker = Faker\Factory::create();
        try {
            DB::beginTransaction();
            $classes_ids = DB::table('classes')
                ->select('class_id as id')
                ->pluck('id')->toArray();
            $questions_ids = [];
            $quizzes_ids = [];
            $teachers_ids = DB::table('users')
                ->select('u_id as id')
                ->where('u_role', '=', 1)
                ->pluck('id')->toArray();
            //create questions
            for ($x = 0; $x <= $no_of_questions; $x++) {
                $no_of_answers = $faker->numberBetween(1, 10);
                $answers_indexes = range(1, $no_of_answers);
                shuffle($answers_indexes);
                $new_question_id = DB::table('questions')
                    ->insertGetId([
                        'question_u_id' => $teachers_ids[array_rand($teachers_ids)],
                        'question_text' => $faker->sentence,
                        'question_right_answer' => $faker->numberBetween(1, $no_of_answers),
                        'question_required' => $faker->numberBetween(0, 1),
                        'question_active' => 1,
                        'question_created_on' => $faker->dateTime

                    ]);

                //create answers
                for ($y = 0; $y < $no_of_answers; $y++) {
                    DB::table('question_answers')
                        ->insert([
                            'qa_text' => $faker->word,
                            'qa_question_id' => $new_question_id,
                            'qa_index' => $answers_indexes[$y],
                            'qa_active' => 1
                        ]);

                }
                $questions_ids[] = $new_question_id;
            }

            //create quizzes
            for ($x = 0; $x < $no_of_quizzes; $x++) {
                $no_of_questions = $faker->numberBetween(1, 10);
                $questions_indexes = range(1, $no_of_questions);
                shuffle($questions_indexes);
                $new_quiz_id = DB::table('quizzes')
                    ->insertGetId([
                        'quiz_title' => $faker->sentence,
                        'quiz_description' => $faker->sentence,
                        "quiz_created_on" => $faker->dateTime,
                        'quiz_created_by' => $teachers_ids[array_rand($teachers_ids)],
                        'quiz_active' => 1

                    ]);
                //add questions to quiz
                for ($y = 0; $y < $no_of_questions; $y++) {
                    DB::table('quiz_questions')
                        ->insert([
                            'qq_quiz_id' => $new_quiz_id,
                            'qq_question_id' => $questions_ids[array_rand($questions_ids)],
                            'qq_created_on' => $faker->dateTime,
                            'qq_question_index'=>$questions_indexes[$y]
                        ]);
                }
                $quizzes_ids[] = $new_quiz_id;

            }
            // add quizzes to classes
            for ($x = 0; $x < $no_of_quizzes_added; $x++) {
                DB::table('class_quizzes')
                    ->insert([
                        'cq_class_id' => $classes_ids[array_rand($classes_ids)],
                        'cq_quiz_id' => $quizzes_ids[array_rand($quizzes_ids)],
                        'cq_created_on' => $faker->dateTime
                    ]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            echo $e->getMessage() . ' in file: ' . $e->getFile() . ' at line: ' . $e->getLine();
            return null;
        }


    }
}