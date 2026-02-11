<?php

namespace App\Core\Controllers;

use App\Core\Services\ModuleService;
use App\Core\DTO\JsonResponse;
use App\Core\Enum\FormMethod;
use App\Http\Resources\ModuleResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ModuleController
{
    protected $moduleService;

    public function __construct(ModuleService $moduleService)
    {
        $this->moduleService = $moduleService;
    }

    public function ilist(Request $request)
    {
        $returnData = [];

        $q = $request->q;
        $name = $request->name;
        $l = $request->l;
        $paginate = $request->paginate;
        $per_page = $request->per_page;
        $company_id = $request->company_id;
        try {
            $extra = [ 'q' => $q, 'company_id' => $company_id];
            if($paginate) {
                $extra['paginate'] = $paginate;
                $extra['per_page'] = $per_page;
            }
            $listOfItem = $this->moduleService->search(null, $name, $l, $extra);
            if($listOfItem->isNotEmpty()) {
                $resource = ModuleResource::collection($listOfItem)->response()->getData(true);
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
            $item = $this->moduleService->one($id, null, [ 'with_company_logo' => 'yes' ]);

            if(!empty($item)) {
                $returnData = [ 'item' => new ModuleResource($item) ];
            }

            return JsonResponse::get(JsonResponse::$OK, 'Item', $returnData);
        } catch (\Exception $e) {
            return response()->json(JsonResponse::get(JsonResponse::$ERROR, $e->getMessage()));
        }

    }

    public function formAction(Request $request)
    {
        $output = JsonResponse::get(JsonResponse::$ERROR, 'Ooops something went wrong !!');
        $rules = $this->moduleService->validationRules();

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

                    $output = $this->moduleService->transaction(function () use ($id, &$request) {
                        $module = $this->moduleService->get($id);

                        if ($module) {
                            $module = $this->moduleService->update($request, $id);
                            return JsonResponse::get(JsonResponse::$OK, "Module updated successful", new ModuleResource($module));
                        }

                        return JsonResponse::get(JsonResponse::$ERROR, "The module you're trying to update could not be found on the server!");
                    });

                    return response()->json($output);
                    break;

                case FormMethod::get('SAVE/value') :

                    $output = $this->moduleService->transaction(function () use ($request) {
                        $request['uuid'] = Str::uuid();

                        $module = $this->moduleService->save($request);

                        return JsonResponse::get(JsonResponse::$OK, "Module saved successful", new ModuleResource($module));
                    });

                    return response()->json($output);
                    break;

                case FormMethod::get('DELETE/value') :

                    $output = $this->moduleService->transaction(function () use ($id) {
                        $module = $this->moduleService->get($id);
                        if ($module) {
                            $output = $this->moduleService->delete($id);
                            return JsonResponse::get(JsonResponse::$OK, "Module deleted successful", $output);
                        }
                        return JsonResponse::get(JsonResponse::$ERROR, "The module you're trying to delete could not be found on the server!");
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