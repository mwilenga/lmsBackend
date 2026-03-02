<?php

namespace App\Core\Controllers;

use App\Core\Services\QuizAnswerService;
use App\Core\DTO\JsonResponse;
use App\Core\Enum\FormMethod;
use App\Core\Services\QuizService;
use App\Core\Services\UserModuleService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class QuizAnswerController
{
    protected $quizanswerService;
    protected $quizService;
    protected $userModuleService;

    public function __construct(QuizAnswerService $quizanswerService, QuizService $quizService, UserModuleService $userModuleService)
    {
        $this->quizanswerService = $quizanswerService;
        $this->quizService = $quizService;
        $this->userModuleService = $userModuleService;
    }

    public function iresults(Request $request)
    {
        $returnData = [];

        $q = $request->q;
        $paginate = $request->paginate;
        $per_page = $request->per_page;
        $company_id = $request->company_id;
        $user_id = $request->user_id;
        $module_id = $request->module_id;
        try {
            $extra = [ 'q' => $q, 'company_id' => $company_id, 'user_id' => $user_id, 'module_id' => $module_id];
            if($paginate) {
                $extra['paginate'] = $paginate;
                $extra['per_page'] = $per_page;
            }

            $listOfQuestion = $this->quizService->search(null, null, null, [ 'module_id' => $module_id ]);
            if($listOfQuestion->isEmpty()) {
                return JsonResponse::get(JsonResponse::$ERROR, 'No questions found for this module', $returnData);
            }

            $countCorrectAnswer = 0;
            // append user answer to each question user has answered
            foreach($listOfQuestion as $question) {
                $userAnswer = $this->quizanswerService->one(null, null, [ 'user_id' => $user_id, 'question_id' => $question->id ]);
                if($userAnswer) {
                    $question->user_answer = $userAnswer->answer;
                    $question->is_user_answer_correct = $userAnswer->answer === $question->correct_option ? true : false;
                    if($question->is_user_answer_correct) {
                        $countCorrectAnswer++;
                    }
                }
            }

            $totalQuestions = $listOfQuestion->count();
            $returnData['count_correct_answer'] = $countCorrectAnswer;
            $returnData['total_questions'] = $totalQuestions;
            $returnData['passPercentage'] = ($countCorrectAnswer / $totalQuestions) * 100;
            $returnData['list_of_item'] = $listOfQuestion;

        return JsonResponse::get(JsonResponse::$OK, 'List of Item', $returnData);

        } catch (\Exception $e) {
            return response()->json(JsonResponse::get(JsonResponse::$ERROR, $e->getMessage()));
        }
        
    }

    public function iget(Request $request)
    {
        $returnData = [];
        $id = $request->id;
        try {
            $item = $this->quizanswerService->one($id, null, [ 'with_company_logo' => 'yes' ]);

            if(!empty($item)) {
                $returnData = [ 'item' => $item ];
            }

            return JsonResponse::get(JsonResponse::$OK, 'Item', $returnData);
        } catch (\Exception $e) {
            return response()->json(JsonResponse::get(JsonResponse::$ERROR, $e->getMessage()));
        }

    }

    public function formAction(Request $request)
    {
        $output = JsonResponse::get(JsonResponse::$ERROR, 'Ooops something went wrong !!');
        $rules = $this->quizanswerService->validationRules();

        try {
            $formMethod = $request->form_method;

            if (in_array($formMethod, [FormMethod::get('UPDATE/value'), FormMethod::get('SAVE/value')])) {
                $error = Validator::make($request->all(), $rules);

                if ($error->fails()) {
                    $output = JsonResponse::get(JsonResponse::$ERROR, $error->errors()->all());
                    return response()->json($output);
                }
            }

            $id = $request->id;
            $currentUser = auth()->user();
            $request['active_user'] = $currentUser->id;
            info('CURREENT USER', [
                'TOKEN USER' => $currentUser, 
                'REQUEST_USER' => $request->user_id, 
                'ACTIVE_USER' => $request->active_user,
                'FORM_METHOD' => $formMethod
            ]);

            switch ($formMethod) {
                case FormMethod::get('UPDATE/value') :

                    $output = $this->quizanswerService->transaction(function () use ($id, &$request) {
                        $quizanswer = $this->quizanswerService->get($id);

                        if ($quizanswer) {
                            $quizanswer = $this->quizanswerService->update($request, $id);
                            return JsonResponse::get(JsonResponse::$OK, "Record updated successful");
                        }

                        return JsonResponse::get(JsonResponse::$ERROR, "The data you're trying to update could not be found on the server!");
                    });

                    return response()->json($output);
                    break;

                case FormMethod::get('SAVE/value') :

                    info('CURREENT USER', [
                        'TOKEN USER' => $currentUser, 
                        'REQUEST_USER' => $request->user_id, 
                        'ACTIVE_USER' => $request->active_user,
                        'FORM_METHOD' => $formMethod
                    ]);
                    $output = $this->quizanswerService->transaction(function () use ($request) {
                        
                        $listOfAnswer = $request->answers;
                        foreach ($listOfAnswer as $answer) {
                            $request['uuid'] = Str::uuid();
                            $request['question_id'] = $answer['question_id'];
                            $request['answer'] = $answer['answer'];

                            info('SAVING REQUEST DATA', [
                                'SAVING REQUEST DATA' => $request->all()
                            ]);
                            $this->quizanswerService->save($request);
                        }

                        // update user module status to completed
                        $this->userModuleService->updateUserModuleStatus((object)['status' => 'completed'], $request->user_id);

                        return JsonResponse::get(JsonResponse::$OK, "Quiz answers saved successful");
                    });

                    return response()->json($output);
                    break;

                case FormMethod::get('DELETE/value') :

                    $output = $this->quizanswerService->transaction(function () use ($id) {
                        $quizanswer = $this->quizanswerService->get($id);
                        if ($quizanswer) {
                            $output = $this->quizanswerService->delete($id);
                            return JsonResponse::get(JsonResponse::$OK, "Record deleted successful");
                        }
                        return JsonResponse::get(JsonResponse::$ERROR, "The data you're trying to delete could not be found on the server!");
                    });

                    return response()->json($output);
                    break;

                default:
                    return response()->json($output);
                    break;
            }

        } catch (\Exception $e) {
            return response()->json(JsonResponse::get(JsonResponse::$ERROR, $e->getMessage()));
        }
    }

}