<?php
/**
 * Created by PhpStorm.
 * User: au614698
 * Date: 14-03-2019
 * Time: 11:45
 */

namespace App\Services;

use Config;
use DB;
use Faker;

class FakerService
{

    public static function FillQuizSessionWithResponses($session_id, $no_of_participants,$insert_prep_responses)
    {
        ini_set('max_execution_time', 0);
        try {
            DB::beginTransaction();
            $faker = Faker\Factory::create();

            $session_data = DB::table('quiz_session')
                ->where('qs_id', '=', $session_id)
                ->select('qs_quiz_id as quiz_id', 'qs_quiz_data as quiz_data', 'qs_started_at as started_at')
                ->first();
            $session_data = json_decode(json_encode($session_data),true);
            $quiz_id = $session_data['quiz_id'];
            $session_started_at = $session_data['started_at'];
            $session_data = json_decode($session_data['quiz_data'],true);
            $quiz_users = DB::table('users')
                    ->join('class_users', 'cu_user_id', '=', 'u_id')
                    ->join('class_quizzes', 'cq_class_id', 'cu_class_id')
                    ->where('cq_quiz_id', '=', $quiz_id)
                    ->where('u_role', '=', 1)
                    ->select('u_id')
                    ->pluck('u_id')->toArray();

            $participants_ids = [];
            for ($iter= 0; $iter <= $no_of_participants;$iter++) {
                $user = $faker->randomElement($quiz_users);
                $finished_at = $faker->dateTimeBetween('+2 days','+3days');
                $participant_id = DB::table('participants_progress')
                    ->insertGetId([
                      'pr_user_id'=>$user,
                      'pr_session_id'=>  $session_id,
                        'pr_index'=> count($session_data['questions']),
                        'pr_phase'=>Config::get('defines.QUIZ_REVISION_PHASE'),
                        'pr_finished'=>1,
                        'pr_displayed_message'=>1,
                        'pr_started_at'=>$faker->dateTimeBetween('-2 days','-1days'),
                        'pr_updated_at'=>$finished_at,
                        'pr_finished_at'=>$finished_at,
                    ]);
                $participants_ids[] = $participant_id;
            }

            foreach($participants_ids as $participant_id) {
            foreach ([1, 2] as $phase) {
                foreach ($session_data['questions'] as $question) {
                        $response_id = DB::table('quiz_responses')
                            ->insertGetId([
                                'qr_phase' => $phase,
                                'qr_session_id'=>$session_id,
                                'qr_question_id' => $question['id'],
                                'qr_answer_index' => $faker->randomElement(range(1, count($question['answers']))),
                                'qr_qp_id' => $participant_id,
                                'qr_date' => $faker->dateTimeInInterval($session_started_at, '+ 2 days', 'Europe/Copenhagen'),
                                'qr_duration'=>$faker->randomElement(range(10,30))
                            ]);

                        foreach ($session_data['acc_questions'] as $acc_question) {
                            //add only for conf and just questions
                            if ($acc_question['type'] != 1 && $acc_question['type'] != 4) {

                                //check if the current question has acc questions
                                if ($acc_question['structure'] == 1) {
                                    if (array_search($question['id'], $acc_question['positions'])) {
                                        $answer_index = $faker->randomElement(range(1, count($acc_question['answers'])));
                                        DB::table('quiz_responses_acc_questions')
                                            ->insertGetId([
                                                'qraq_session_id' => $session_id,
                                                'qraq_response_id' => $response_id,
                                                'qraq_acc_id' => $acc_question['id'],
                                                'qraq_acc_answer_index' => $answer_index,
                                                'qraq_acc_answer_content' => $acc_question['answers'][$answer_index - 1]['text'],
                                                'qraq_qp_id'=>$participant_id

                                            ]);
                                    }
                                } else if ($acc_question['structure'] == 3) {
                                    DB::table('quiz_responses_acc_questions')
                                        ->insertGetId([
                                            'qraq_session_id' => $session_id,
                                            'qraq_response_id' => $response_id,
                                            'qraq_acc_id' => $acc_question['id'],
                                            'qraq_acc_answer_index' => null,
                                            'qraq_acc_answer_content' => $faker->sentence($nbWords = 6, true),
                                            'qraq_qp_id'=>$participant_id
                                        ]);
                                }
                            }
                        }
                    }
                }
            }
            //insert prep responses

            $prep_question = null;
            foreach ($session_data['acc_questions'] as $acc_question) {
                if ($acc_question['type'] == 1) {
                    $prep_question = $acc_question;
                    break;
                }
            }
            if ($insert_prep_responses == true && $prep_question) {
                foreach($participants_ids as $participant_id) {

                    $answer_index = $faker->randomElement(range(1, count($prep_question['answers'])));
                    DB::table('quiz_responses_acc_questions')
                        ->insertGetId([
                            'qraq_session_id' => $session_id,
                            'qraq_response_id' => null,
                            'qraq_acc_id' => $prep_question['id'],
                            'qraq_acc_answer_index' => $answer_index,
                            'qraq_acc_answer_content' => $prep_question['answers'][$answer_index - 1]['text'],
                            'qraq_qp_id'=>$participant_id

                        ]);
                }
            }
            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            return json_encode(['status' => false, 'message' => $e->getMessage() . ' in file: ' . $e->getFile() . ' at line: ' . $e->getLine()]);


        }
        return json_encode(['status' => true, 'message' => 'success']);

    }
}