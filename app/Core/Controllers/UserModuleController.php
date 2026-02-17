<?php

namespace App\Core\Controllers;

use App\Core\Services\UserModuleService;
use App\Core\DTO\JsonResponse;
use App\Core\Enum\FormMethod;
use App\Http\Resources\UserModuleResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserModuleController
{
    protected $usermoduleService;

    public function __construct(UserModuleService $usermoduleService)
    {
        $this->usermoduleService = $usermoduleService;
    }

    public function ilist(Request $request)
    {
        $returnData = [];

        $q = $request->q;
        $name = $request->name;
        $l = $request->l;
        $paginate = $request->paginate;
        $per_page = $request->per_page;
        $user_id = $request->user_id;
        try {
            $extra = [ 'q' => $q, 'user_id' => $user_id, 'with_module' => true];
            if($paginate) {
                $extra['paginate'] = $paginate;
                $extra['per_page'] = $per_page;
            }
            $listOfItem = $this->usermoduleService->search(null, $name, $l, $extra);
            if($listOfItem->isNotEmpty()) {
                $resource = UserModuleResource::collection($listOfItem)->response()->getData(true);
                $returnData = [ 'list_of_item' => $resource ];
            }

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
            $item = $this->usermoduleService->one($id, null, [ 'with_company_logo' => 'yes' ]);

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
        $rules = $this->usermoduleService->validationRules();

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

                    $output = $this->usermoduleService->transaction(function () use ($id, &$request) {
                        $usermodule = $this->usermoduleService->get($id);

                        if ($usermodule) {
                            $usermodule = $this->usermoduleService->update($request, $id);
                            return JsonResponse::get(JsonResponse::$OK, "User module updated successful", $usermodule);
                        }

                        return JsonResponse::get(JsonResponse::$ERROR, "The data you're trying to update could not be found on the server!");
                    });

                    return response()->json($output);
                    break;

                case FormMethod::get('SAVE/value') :

                    $output = $this->usermoduleService->transaction(function () use ($request) {
                        $request['uuid'] = Str::uuid();

                        $usermodule = $this->usermoduleService->save($request);

                        return JsonResponse::get(JsonResponse::$OK, "User module saved successful", $usermodule);
                    });

                    return response()->json($output);
                    break;

                case FormMethod::get('DELETE/value') :

                    $output = $this->usermoduleService->transaction(function () use ($id) {
                        $usermodule = $this->usermoduleService->get($id);
                        if ($usermodule) {
                            $output = $this->usermoduleService->delete($id);
                            return JsonResponse::get(JsonResponse::$OK, "User module deleted successful", $output);
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