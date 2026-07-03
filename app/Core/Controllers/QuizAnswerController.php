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
        $user_id = $request->user_id ?? auth()->id();
        $module_id = $request->module_id;

        try {
            if (empty($module_id)) {
                return response()->json(JsonResponse::get(JsonResponse::$ERROR, 'module_id is required'));
            }

            if (empty($user_id)) {
                return response()->json(JsonResponse::get(JsonResponse::$ERROR, 'user_id is required'));
            }

            $results = $this->quizanswerService->getModuleQuizResults($user_id, $module_id);
            if (empty($results)) {
                return JsonResponse::get(JsonResponse::$ERROR, 'No questions found for this module');
            }

            if (!$results['passed']) {
                return JsonResponse::get(
                    JsonResponse::$ERROR,
                    $this->quizanswerService->belowPassMarkMessage(),
                    $results
                );
            }

            return JsonResponse::get(JsonResponse::$OK, 'Quiz results', $results);
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

                    $output = $this->quizanswerService->transaction(function () use ($request) {
                        $submissionUuid = (string) Str::uuid();
                        $request['submission_uuid'] = $submissionUuid;

                        foreach ($request->answers as $answer) {
                            $request['uuid'] = (string) Str::uuid();
                            $request['question_id'] = $answer['question_id'];
                            $request['answer'] = $answer['answer'];
                            $this->quizanswerService->save($request);
                        }

                        $results = $this->quizanswerService->getModuleQuizResults($request->user_id, $request->module_id);

                        if ($results['passed']) {
                            $this->userModuleService->updateUserModuleStatus(
                                (object) ['status' => 'completed', 'module_id' => $request->module_id, 'active_user' => $request->active_user],
                                $request->user_id
                            );
                        }

                        if (!$results['passed']) {
                            return JsonResponse::get(
                                JsonResponse::$ERROR,
                                $this->quizanswerService->belowPassMarkMessage(),
                                $results
                            );
                        }

                        return JsonResponse::get(JsonResponse::$OK, "Quiz answers saved successful", $results);
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
