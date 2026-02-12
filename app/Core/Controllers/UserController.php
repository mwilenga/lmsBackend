<?php

namespace App\Core\Controllers;

use App\Core\DTO\JsonResponse;
use App\Core\Services\UserService;
use App\Core\Enum\FormMethod;
use App\Http\Controllers\Controller;
use App\Http\Resources\UsersResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class UserController extends Controller
{
    protected $userService;

    public function __construct(
        UserService $userService,
    ) {
        $this->userService = $userService;
    }

    public function register(Request $request)
    {
        $output = JsonResponse::get(JsonResponse::$ERROR, 'Ooops something went wrong !!');
        $rules = $this->userService->validationRules();

        try {
            $error = Validator::make($request->all(), $rules);
            if ($error->fails()) {
                $output = JsonResponse::get(JsonResponse::$ERROR, $error->errors()->all());
                return response()->json($output);
            }

            $request['active_user'] = 1; // admin

            $output = $this->userService->transaction(function () use ($request) {
                $request['uuid'] = Str::uuid();
                $user = $this->userService->save($request);

                return JsonResponse::get(JsonResponse::$OK, "Registration completed successful", new UsersResource($user));
            });

            return response()->json($output);

        } catch (\Exception $e) {
            return response()->json(JsonResponse::get(JsonResponse::$ERROR, $e->getMessage()));
        }
    }

    public function ilist(Request $request)
    {
        $returnData = [];

        $q = $request->q;
        $name = $request->name;
        $l = $request->l;
        $paginate = $request->paginate;
        $per_page = $request->per_page ?? 10;
        $company_id = $request->company_id;
        try {
            $extra = [ 'q' => $q, 'company_id' => $company_id];
            if($paginate) {
                $extra['paginate'] = $paginate;
                $extra['per_page'] = $per_page;
            }
            $listOfItem = $this->userService->search(null, $name, $l, $extra);
            if($listOfItem->isNotEmpty()) {
                $resource = UsersResource::collection($listOfItem)->response()->getData(true);
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
            $item = $this->userService->one($id, null, [ 'with_company_logo' => 'yes' ]);

            if(!empty($item)) {
                $returnData = [ 'item' => new UsersResource($item) ];
            }

            return JsonResponse::get(JsonResponse::$OK, 'Item details', $returnData);
        } catch (\Exception $e) {
            return response()->json(JsonResponse::get(JsonResponse::$ERROR, $e->getMessage()));
        }

    }

    public function formAction(Request $request)
    {
        $output = JsonResponse::get(JsonResponse::$ERROR, 'Ooops something went wrong !!');
        $rules = $this->userService->validationRules();

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

                    $output = $this->userService->transaction(function () use ($id, &$request) {
                        $books = $this->userService->get($id);
                        if ($books) {
                            $newBooks = $this->userService->update($request, $id);
                            return JsonResponse::get(JsonResponse::$OK, "User updated successful", new UsersResource($newBooks));
                        }
                        return JsonResponse::get(JsonResponse::$ERROR, "The user you're trying to update could not be found on the server!");
                    });

                    return response()->json($output);
                    break;

                case FormMethod::get('SAVE/value') :

                    $output = $this->userService->transaction(function () use ($request) {
                        $request['uuid'] = Str::uuid();
                        $books = $this->userService->save($request);

                        return JsonResponse::get(JsonResponse::$OK, "User saved successful", new UsersResource($books));
                    });

                    return response()->json($output);
                    break;

                case FormMethod::get('DELETE/value') :

                    $output = $this->userService->transaction(function () use ($id) {
                        $books = $this->userService->get($id);
                        if ($books) {
                            $output = $this->userService->delete($id);
                            return JsonResponse::get(JsonResponse::$OK, "User deleted successful", $output);
                        }
                        return JsonResponse::get(JsonResponse::$ERROR, "The user you're trying to delete could not be found on the server!");
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
