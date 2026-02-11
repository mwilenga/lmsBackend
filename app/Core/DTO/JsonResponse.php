<?php
namespace App\Core\DTO;


class JsonResponse {

    public static $OK = "OK";
    public static $ERROR = "ERROR";

    public static function get($status, $errorMessage, $returnData = null){
        return array(
            'status' => $status,
            'errorMessage' => $errorMessage,
            'returnData' => $returnData,
        );
    }

    public static function validationErrorMessage($validator){
        $errors = array();
        $messages = $validator->errors();
        foreach($messages->all() as $message){
            array_push($errors, $message);
        }
        return JsonResponse::get(self::$ERROR, "Validation Error", array("errors"=>$errors));
    }

    public  static function safeEncode($value){
        if (version_compare(PHP_VERSION, '5.4.0') >= 0) {
            $encoded = json_encode($value, JSON_PRETTY_PRINT);
        } else {
            $encoded = json_encode($value);
        }
        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                return $encoded;
            case JSON_ERROR_DEPTH:
                return 'Maximum stack depth exceeded'; // or trigger_error() or throw new Exception()
            case JSON_ERROR_STATE_MISMATCH:
                return 'Underflow or the modes mismatch'; // or trigger_error() or throw new Exception()
            case JSON_ERROR_CTRL_CHAR:
                return 'Unexpected control character found';
            case JSON_ERROR_SYNTAX:
                return 'Syntax error, malformed JSON'; // or trigger_error() or throw new Exception()
            case JSON_ERROR_UTF8:
                $clean = self::utf8ize($value);
                return self::safeEncode($clean);
            default:
                return 'Unknown error'; // or trigger_error() or throw new
                // Exception();
        }
    }

    public  static function utf8ize($mixed) {
        if (is_array($mixed)) {
            foreach ($mixed as $key => $value) {
                $mixed[$key] = self::utf8ize($value);
            }
        } else if (is_string ($mixed)) {
            return utf8_encode($mixed);
        }
        return $mixed;
    }

    public static function jsonPermissionError($role = ""){
        $roleMsg = "";
        if(!empty($role)){
            $role = str_replace("ROLE_", " ", $role);
            $role = str_replace("_", " ", $role);
            $roleMsg = "<br/><br/> <b>" . $role . "</b>";
        }

        self::get(self::$ERROR, "You do not have permission to perform this action. Please contact Administrator.", $roleMsg);
    }

    public static function throwPermissionError($role = ""){
        $roleMsg = "";
        if(!empty($role)){
            $role = str_replace("ROLE_", " ", $role);
            $role = str_replace("_", " ", $role);
            $roleMsg = "<br/><br/> <b>" . $role . "</b>";
        }

        throw new \Exception("You do not have permission to perform this action. Please contact Administrator. {$roleMsg}");
    }

}
