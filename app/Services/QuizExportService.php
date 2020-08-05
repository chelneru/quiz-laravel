<?php


namespace App\Services;

use App\Repositories\QuizExport\QuizExportRepository;
use Config;
use League\Csv\CannotInsertRecord;
use League\Csv\Writer;
use SplTempFileObject;

class QuizExportService
{
//    public static function
    /**
     * @param $session_id
     * @param bool $show_correctness
     * @param bool $exclude_incomplete
     * @return array
     */
    public static function ExportSession($session_id, bool $show_correctness, bool $exclude_incomplete)
    {

        $session_data = QuizService::GetSessionQuizData($session_id);
        $session_data = json_decode($session_data, true);
        $rows = self::GetResponsesRowsForExport($session_data, $session_id, $show_correctness, $exclude_incomplete);
        return $rows;
    }


    public static function GenerateHeaders($session_data, $show_correctness)
    {
        $headers = ['user_id', 't_start', 't_end'];
        $has_prep = false;
        $ad_questions_counters = [];
        $ad_counter = 1;
        $outside_additional_question = [];
        foreach ($session_data['questions'] as $question_index => $question) {
            $index = $question_index + 1;
            $headers[] = 'q' . $index . '_id';
            $headers[] = 'q' . $index . '_i';
            $headers[] = 'q' . $index . '_r';
            //acc questions Headers
            $has_justification = false;
            $has_initial_ph_confidence = false;
            $has_rev_ph_confidence = false;
            $inside_other_questions_headers = [];

            foreach ($session_data['acc_questions'] as &$acc_question) {
                if (is_string($acc_question['feedback'])) {
                    $acc_question['feedback'] = explode(',', $acc_question['feedback']);
                }
                if ($acc_question['type'] == 1) {
                    $has_prep = true;
                } else if ($acc_question['type'] == 3) {
                    //justification headers
                    if (in_array($question['id'], $acc_question['feedback'], false)) {
                        $has_justification = true;
                    }
                } else if ($acc_question['type'] == 2) {
                    //conf headers
                    if (in_array($question['id'], $acc_question['feedback'], false)) {
                        $has_initial_ph_confidence = true;
                    }
                    if (in_array($question['id'], $acc_question['positions'], false)) {
                        $has_rev_ph_confidence = true;
                    }
                } else if ($acc_question['type'] == 4) {
                    if ($acc_question['other_question_position'] == 2) {
                        //inside AD
                        if (in_array($question['id'], $acc_question['feedback'], false)) {
                            $inside_other_questions_headers[] = 'q' . $index . '_ADD_' . shortenedString($acc_question['name'], 7, false) . '_i';
                        }
                        //this has been commented as currently no other questions can have answers in the revision phase
//                        if (in_array($question['id'], $acc_question['positions'], false)) {
//                            $inside_other_questions_headers[] = 'q' . $index . '_ADD_' . shortenedString($acc_question['name'], 4, false) . '_r';
//                        }
                    } else {
                        //outside AD
                        if (!in_array('ADD_' . shortenedString($acc_question['name'], 7, false), $outside_additional_question, false)) {
                            $outside_additional_question[] = 'ADD_' . shortenedString($acc_question['name'], 7, false);
                        }
                    }
                }
            }
            unset($acc_question);


            if ($has_justification) {
                $headers[] = 'q' . $index . '_j';

            }
            if ($has_initial_ph_confidence) {
                $headers[] = 'q' . $index . '_c_i';

            }
            if ($has_rev_ph_confidence) {
                $headers[] = 'q' . $index . '_c_r';

            }
            $headers = array_merge($headers, $inside_other_questions_headers);
            if ($show_correctness) {
                $headers[] = 'q' . $index . '_i_c';
                $headers[] = 'q' . $index . '_r_c';
            }
        }
        if ($has_prep == true) {
            $headers[] = 'prep';
        }
        $headers = array_merge($headers, $outside_additional_question);
        return $headers;
    }

    private static function GetResponsesRowsForExport($session_data, $session_id, $show_correctness, $exclude_incomplete)
    {
        $is_assessed = false;
        if (isset($session_data['is_assessed']) && $session_data['is_assessed'] == 1) {
            $is_assessed = true;
        }
        $participants = QuizExportRepository::GetSessionResponses($session_id, $exclude_incomplete,$is_assessed);
        $rows = [];
        $outside_quiz_other_questions = [];
        $inside_quiz_other_questions = [];
        foreach ($participants as $participant) {
            // we have name
            if (isset($session_data['is_assessed'], $participant['first_name'], $participant['last_name']) && $is_assessed && $participant['first_name'] !== null && $participant['last_name'] !== null) {
                $participant_identifier  = $participant['first_name'].' '.$participant['last_name'];
            }
            else {
                $participant_identifier = $participant['id'];

            }
            $result_row = [$participant_identifier, $participant['started_at'], $participant['finished_at']];

            foreach ($session_data['questions'] as $question) {
                $initial_answer = null;
                $revision_answer = null;

                $confidence_initial = null;
                $has_confidence_initial = false;


                $confidence_revision = null;
                $has_confidence_revision = false;

                $just_answer_initial = null;
                $has_justification_initial = false;

                $outside_quiz_other_questions = [];
                $inside_quiz_other_questions = [];

                $fill_acc_response = static function ($acc_question_id, $response, $phase) use (&$inside_quiz_other_questions, &$outside_quiz_other_questions) {
                    foreach ($inside_quiz_other_questions as &$other_question) {
                        if ($other_question['acc_question_id'] == $acc_question_id && $other_question['phase'] == $phase) {
                            $other_question['response'] = $response;

                        }
                    }
                    unset($other_question);
                    foreach ($outside_quiz_other_questions as &$other_question) {
                        if ($other_question['acc_question_id'] == $acc_question_id) {
                            $other_question['response'] = $response;
                        }
                    }
                };
                foreach ($session_data['acc_questions'] as $acc_question) {
                    if (is_string($acc_question['feedback'])) {
                        $acc_question['feedback'] = explode(',', $acc_question['feedback']);
                    }
                    if ($acc_question['type'] == 2) {
                        if (in_array($question['id'], $acc_question['feedback'], false)) {
                            $has_confidence_initial = true;
                        }
                        if (in_array($question['id'], $acc_question['positions'], false)) {
                            $has_confidence_revision = true;
                        }

                    } else if ($acc_question['type'] == 3) {
                        if (in_array($question['id'], $acc_question['feedback'], false)) {
                            $has_justification_initial = true;
                        }
                    } else if ($acc_question['type'] == 4) {
                        if ($acc_question['structure'] == 2) {
                            if ($acc_question['other_question_position'] == 2) {
                                //its a rating question inside the quiz so we do similar to confidence
                                $phase = null;
                                if (in_array($question['id'], $acc_question['feedback'], false)) {
                                    $phase = 1;
                                    $inside_quiz_other_questions[] = ['phase' => $phase, 'acc_question_id' => $acc_question['id']];

                                } else if (in_array($question['id'], $acc_question['positions'], false)) {
                                    $phase = 2;

                                }

                            } else if ($acc_question['other_question_position'] == 1 || $acc_question['other_question_position'] == 3) {
                                //its a rating question outside the quiz
                                $outside_quiz_other_questions[] = ['acc_question_id' => $acc_question['id'], 'question_id' => $question['id']];
                            }

                        } else if ($acc_question['structure'] == 3) {
                            if ($acc_question['other_question_position'] == 2) {
                                $phase = null;
                                if (in_array($question['id'], $acc_question['feedback'], false)) {
                                    $phase = 1;
                                    $inside_quiz_other_questions[] = ['phase' => $phase, 'acc_question_id' => $acc_question['id']];

                                } else if (in_array($question['id'], $acc_question['positions'], false)) {
                                    $phase = 2;

                                }
                                //its a text question inside the quiz so we do similar to justification

                            } else if ($acc_question['other_question_position'] == 1 || $acc_question['other_question_position'] == 3) {
                                //its a text question outside the quiz
                                $outside_quiz_other_questions[] = ['acc_question_id' => $acc_question['id']];
                            }
                        }
                    }
                }


                foreach ($participant['responses'] as $response) {
                    if ($response['question_id'] == $question['id']) {
                        if ($response['phase'] == Config::get('defines.QUIZ_INITIAL_PHASE')) {
                            $initial_answer = $response['answer_index'];
                        } else if ($response['phase'] == Config::get('defines.QUIZ_REVISION_PHASE')) {
                            $revision_answer = $response['answer_index'];
                        }
                        //check if there should be any acc responses for current response
                        foreach ($response['acc_responses'] as $acc_response) {
                            foreach ($session_data['acc_questions'] as $acc_question) {
                                if (is_string($acc_question['feedback'])) {
                                    $acc_question['feedback'] = explode(',', $acc_question['feedback']);
                                }
                                if ($acc_response['acc_question_id'] == $acc_question['id']) {
                                    //check for conf response
                                    if ($acc_question['type'] == 2) {
                                        if (in_array($question['id'], $acc_question['feedback'], false)) {

                                            if ($response['phase'] == Config::get('defines.QUIZ_INITIAL_PHASE')) {
                                                $confidence_initial = $acc_response['answer_index'];
                                            }
                                        }
                                        if (in_array($question['id'], $acc_question['positions'], false)) {
                                            if ($response['phase'] == Config::get('defines.QUIZ_REVISION_PHASE')) {
                                                $confidence_revision = $acc_response['answer_index'];
                                            }
                                        }

                                    } //check for just response
                                    else if ($acc_question['type'] == 3) {
                                        if (in_array($question['id'], $acc_question['feedback'], false)) {
                                            if ($response['phase'] == Config::get('defines.QUIZ_INITIAL_PHASE')) {
                                                $just_answer_initial = $acc_response['answer_content'];
                                            }
                                        }
                                    } //check for other questions inside the quiz
                                    else if ($acc_question['type'] == 4) {
                                        if ($acc_question['structure'] == 2) {
                                            $fill_acc_response($acc_question['id'], $acc_response['answer_index'], $response['phase']);

                                        } else if ($acc_question['structure'] == 3) {
                                            $fill_acc_response($acc_question['id'], $acc_response['answer_content'], $response['phase']);
                                        }

                                    }
                                }
                            }
                        }
                    }

                }

                //sort the additional questions that are inside the quiz so we display them by their id and the phase
                usort($inside_quiz_other_questions, function ($item1, $item2) {
                    if ($item1['acc_question_id'] == $item2['acc_question_id']) {
                        return $item1['phase'] <=> $item2['phase'];
                    }
                    return $item1['acc_question_id'] <=> $item2['acc_question_id'];
                });
                //sort the additional questions that are outside the quiz so we display them by their id
                usort($outside_quiz_other_questions, function ($item1, $item2) {
                    return $item1['acc_question_id'] <=> $item2['acc_question_id'];
                });

                $result_row[] = $question['id'];
                $result_row[] = $initial_answer ?? '';
                $result_row[] = $revision_answer ?? '';

                if ($has_justification_initial) {
                    $result_row[] = $just_answer_initial ?? '';
                }
                if ($has_confidence_initial) {
                    $result_row[] = $confidence_initial ?? '';
                }
                if ($has_confidence_revision) {
                    $result_row[] = $confidence_revision ?? '';
                }

                //add the additional questions responses
                foreach (array_column($inside_quiz_other_questions, 'response') as $ad_response) {
                    $result_row[] = $ad_response;
                }
                if ($show_correctness == true) {
                    if ($initial_answer !== null) {
                        if ($question['right_answer'] == $initial_answer) {
                            $result_row[] = 1;
                        } else {
                            $result_row[] = '0';
                        }
                    } else {
                        $result_row[] = '';
                    }
                    if ($revision_answer !== null) {

                        if ($question['right_answer'] == $revision_answer) {
                            $result_row[] = 1;
                        } else {
                            $result_row[] = '0';
                        }
                    } else {
                        $result_row[] = '';
                    }

                }
            }
            $prep_answer = null;
            foreach ($participant['acc_responses'] as $acc_response) {
                foreach ($session_data['acc_questions'] as $acc_question) {
                    if ($acc_response['acc_question_id'] == $acc_question['id']) {
                        if ($acc_question['type'] == 1) {
                            $prep_answer = $acc_response['answer_index'];
                        }
                        if ($acc_question['type'] == 4) {
                            //other questions responses outside of the quiz
                            if ($acc_question['structure'] == 2) {
                                $fill_acc_response($acc_question['id'], $acc_response['answer_index'], null);

                            } else if ($acc_question['structure'] == 3) {
                                $fill_acc_response($acc_question['id'], $acc_response['answer_content'], null);
                            }
                        }
                    }
                }
            }
            if ($prep_answer !== null) {
                $result_row[] = $prep_answer;
            }
            if (count($outside_quiz_other_questions) > 0) {
                //add the outer quiz additional questions responses
                foreach (array_column($outside_quiz_other_questions, 'response') as $ad_response) {
                    $result_row[] = $ad_response;
                }
            }
            $rows[] = $result_row;
        }
        return $rows;
    }

    public static function ExportSessionQuizInfo(int $session_id)
    {
        $rows = [];
        $session_data = json_decode(QuizService::GetSessionQuizData($session_id), true);
        // add the first part with the main questions
        foreach ($session_data['questions'] as $index => $question) {
            $new_row = self::GetQuestionRowForQuizExport($question);
            array_unshift($new_row, 'Q' . ($index + 1));
            $rows[] = $new_row;
        }
        // add the second  part with the acc questions
        $rows[] = self::GetAccQuestionsSectionRowsForQuizInfoExport($session_data);
        $rows[] = [['']];
        // add the third part with the details about where the acc questions are displayed
        $rows[] = self::GetAccQuestionsDisplayPositionSectionRowsForQuizInfoExport($session_data);

        return $rows;
    }

    private static function GetAccQuestionsSectionRowsForQuizInfoExport($session_data)
    {
        $rows = [];
        $pre_acc_question_rows = [];
        $inside_acc_question_rows = [];
        $post_acc_question_rows = [];
        foreach ($session_data['acc_questions'] as $acc_question) {

            if ($acc_question['type'] == 1 || ($acc_question['type'] == 4 && $acc_question['other_question_position'] == 1)) {
                $pre_acc_question_rows[] = self::GetAccQuestionRowForQuizExport($acc_question);
            } else if ($acc_question['type'] == 2 || $acc_question['type'] == 3 || ($acc_question['type'] == 4 && $acc_question['other_question_position'] == 2)) {
                $inside_acc_question_rows[] = self::GetAccQuestionRowForQuizExport($acc_question);
                //prepare the structure for normal questions row

            } else if ($acc_question['type'] == 4 && $acc_question['other_question_position'] == 3) {
                $post_acc_question_rows[] = self::GetAccQuestionRowForQuizExport($acc_question);

            }
        }
        // for each first row of each category , the first cell will have the name of the category
        //we check if we have any rows and assume the first cell always exists
        if (count($pre_acc_question_rows) > 0) {
            $pre_acc_question_rows[0][0] = 'Prephase';
            $rows = array_merge($rows, [['']], $pre_acc_question_rows);

        }
        if (count($inside_acc_question_rows) > 0) {
            $inside_acc_question_rows[0][0] = 'Accompanying';
            $rows = array_merge($rows, [['']], $inside_acc_question_rows);

        }
        if (count($post_acc_question_rows) > 0) {
            $post_acc_question_rows[0][0] = 'Postphase';
            $rows = array_merge($rows, [['']], $post_acc_question_rows);

        }
        return $rows;
    }

    private static function GetAccQuestionsDisplayPositionSectionRowsForQuizInfoExport($session_data)
    {
        $pre_acc_question_rows = [];
        $inside_acc_question_rows = [];
        $post_acc_question_rows = [];
        foreach ($session_data['acc_questions'] as $acc_question) {
            $acc_question_name = $acc_question['name'];
            switch ($acc_question['type']) {
                case 1:
                    $acc_question_name = 'Preparation';
                    break;
                case 2:
                    $acc_question_name = 'Confidence';
                    break;
                case 3:
                    $acc_question_name = 'Justification';
                    break;
            }

            $new_acc_row = [$acc_question_name, ''];
            if ($acc_question['type'] == 1 || ($acc_question['type'] == 4 && $acc_question['other_question_position'] == 1)) {

                foreach ($session_data['questions'] as $index => $question) {
                    $new_acc_row[] = 'Q' . ($index + 1);
                }
                $pre_acc_question_rows[] = $new_acc_row;

            } else if ($acc_question['type'] == 2 || $acc_question['type'] == 3 || ($acc_question['type'] == 4 && $acc_question['other_question_position'] == 2)) {
                $new_acc_row[1] = 'Init';
                $new_acc_rev_row = ['', 'Rev'];
                foreach ($session_data['questions'] as $index => $question) {
                    if (QuizService::AccQuestionDisplayInQuestion($acc_question, $question['id'], Config::get('defines.QUIZ_INITIAL_PHASE'))) {
                        $new_acc_row[] = 'Q' . ($index + 1);
                    } else {
                        $new_acc_row[] = '';
                    }
                    if (QuizService::AccQuestionDisplayInQuestion($acc_question, $question['id'], Config::get('defines.QUIZ_REVISION_PHASE'))) {
                        $new_acc_rev_row[] = 'Q' . ($index + 1);
                    } else {
                        $new_acc_rev_row[] = '';
                    }
                }
                $inside_acc_question_rows[] = $new_acc_row;
                $inside_acc_question_rows[] = $new_acc_rev_row;

            } else if ($acc_question['type'] == 4 && $acc_question['other_question_position'] == 3) {
                foreach ($session_data['questions'] as $index => $question) {
                    $new_acc_row[] = 'Q' . ($index + 1);
                }
                $post_acc_question_rows[] = $new_acc_row;

            }
        }
        $rows = array_merge($pre_acc_question_rows, $inside_acc_question_rows, $post_acc_question_rows);
        return $rows;
    }

    private
    static function GetAccQuestionRowForQuizExport($question)
    {
        $new_row = [];
        $new_row[] = '';

        switch ($question['type']) {
            case 1:
                $new_row[] = 'Preparation';
                break;
            case 2:
                $new_row[] = 'Confidence';
                break;
            case 3:
                $new_row[] = 'Justification';
                break;
            case 4:
                $new_row[] = $question['name'];
                break;
        }

        $new_row[] = $question['text'];
        if ($question['structure'] < 3) {
            foreach ($question['answers'] as $index => $answer) {
                $new_row[] = strtoupper(chr(64 + $index + 1)) . '. ' . $answer['text'];
            }
        }
        return $new_row;
    }

    private static function GetQuestionRowForQuizExport($question)
    {
        $new_row = [];
        $new_row[] = strtoupper(chr(64 + ($question['right_answer'] - 1) + 1));
        $new_row[] = $question['text'];

        foreach ($question['answers'] as $index => $answer) {
            $new_row[] = strtoupper(chr(64 + $index + 1)) . '. ' . $answer['text'];
        }

        return $new_row;
    }

    public
    static function ExportSessionResults(int $session_id)
    {
        $results = [];

        $session_timestamps = QuizService::GetSessionTimestamps($session_id);
        $responses = QuizExportRepository::GetSessionResultsResponses($session_id);
        $quiz_data = QuizService::GetSessionQuizData($session_id);
        $quiz_data = json_decode($quiz_data, true);
        $results[] = ['', 'Timeline', '', ''];
        $results[] = [''];
        $individual_responses = [];
        if ($session_timestamps != null) {
            $results[] = ['Quiz started at', $session_timestamps->start];
            $results[] = ['Revision phase started at', $session_timestamps->revision_start];
            $results[] = ['Reveal answers phase started at', $session_timestamps->reveal_start];
            $results[] = ['Quiz stopped at', $session_timestamps->stop];
        }
        $results[] = [''];

        $headings = ['userID', 'Score'];
        $questions_count = count($quiz_data['questions']);
        $question_ids = [];
        for ($iter = 0; $iter < $questions_count; $iter++) {
            $headings[] = 'Q' . ($iter + 1);
            $headings[] = 'Q' . ($iter + 1);
            $question_ids[] = $quiz_data['questions'][$iter]['id'];
        }
        $results[] = $headings;
        foreach ($responses as $response) {
            foreach ($quiz_data['questions'] as $index => $question) {
                if ($question['id'] == $response->question_id) {

                    $correctness = '0';
                    if ($question['right_answer'] == $response->answer_index) {
                        $correctness = '1';
                    }

                    if (!isset($individual_responses[$response->user_id])) {
                        $individual_responses [$response->user_id] = ['responses' => [['question_id' => $response->question_id, 'correctness' => $correctness, 'timestamp' => $response->date]]];
                    } else {
                        $found_duplicate = false;
                        foreach ($individual_responses [$response->user_id]['responses'] as $existing_response) {
                            if ($existing_response['question_id'] == $response->question_id &&
                                $existing_response['correctness'] == $correctness) {
                                $found_duplicate = true;
                            }
                        }
                        if (!$found_duplicate) {
                            $individual_responses [$response->user_id]['responses'][] = ['question_id' => $response->question_id, 'correctness' => $correctness, 'timestamp' => $response->date];
                        }
                    }
                    if ($correctness == '1') {

                        if (strtotime($response->date) <= strtotime($session_timestamps->reveal_start)) {

                            if (!isset($individual_responses [$response->user_id]['score'])) {
                                $individual_responses [$response->user_id]['score'] = 1;
                            } else {
                                $individual_responses [$response->user_id]['score']++;

                            }
                        }
                    }
                }
            }
        }


        ksort($individual_responses);
        foreach ($individual_responses as &$individual_respons) {

            usort($individual_respons['responses'], function ($item1, $item2) {
                return $item1['question_id'] <=> $item2['question_id'];
            });
        }
        unset($individual_respons);


        foreach ($individual_responses as $index => $individual_respons) {

            if (!isset($individual_respons['score'])) {
                $individual_respons['score'] = 0;
            }
            $row = [$index, $individual_respons['score']];

            foreach ($question_ids as $question_id) {
                $found_question = false;
                $temp_array = [];
                foreach ($individual_respons['responses'] as $question_index => $response) {
                    if ($response['question_id'] == $question_id) {
                        $temp_array[] = $response['correctness'];
                        $temp_array[] = $response['timestamp'];
                        $found_question = true;
                        break;
                    }
                }
                if ($found_question == false) {
                    $temp_array = ['', ''];
                }
                $row = array_merge($row, $temp_array);

            }

            $results [] = $row;

        }

        return $results;

    }


}
