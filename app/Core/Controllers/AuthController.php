<?php

namespace App\Core\Controllers;

use App\Core\DTO\JsonResponse;
use App\Core\Services\AuthService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(
        AuthService $authService,
    ) {
        $this->authService = $authService;
    }

    function login(Request $request)
    {
        $output = JsonResponse::get(JsonResponse::$ERROR, "Ooops something went wrong !!");
        $rules = $this->authService->loginValidationRules();
        try {
            $error = Validator::make($request->all(), $rules);
            if ($error->fails()) {
                $output = JsonResponse::get(JsonResponse::$ERROR, $error->errors()->first());
                return response()->json($output);
            }

            $email = $request->email;
            $password = $request->password;

            $user = $this->authService->login($email, $password);
            if (!$user) {
                return JsonResponse::get(JsonResponse::$ERROR, "Invalid email or password. Please try again later !!");
            }

            $user = $this->authService->getCurrentUser();
            $user['accessToken'] = $this->authService->createUserToken($user);
            $returnData = [
                'access_token' => $user->accessToken,
            ];
            return JsonResponse::get(JsonResponse::$OK, "Signin successfully.!!", $returnData);
        } catch (\Exception $e) {
            return response()->json(JsonResponse::get(JsonResponse::$ERROR, $e->getMessage()));
        }
    }

    function logout(Request $request)
    {
        $output = JsonResponse::get(JsonResponse::$ERROR, "Ooops something went wrong !!");
        
        // Check if the user exists and has an active token
        if (!$request->user() || !$request->user()->currentAccessToken()) {
            $output = JsonResponse::get(JsonResponse::$ERROR, "User not authenticated or no active session.");
            return response()->json($output);
        }

        // Revoke the user's current access token
        $request->user()->currentAccessToken()->delete();

        return JsonResponse::get(JsonResponse::$OK, "Logout successfully.!!");
    }
}
