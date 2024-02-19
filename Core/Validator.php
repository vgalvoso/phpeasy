<?php

namespace Core\Validator;

use function Core\Helper\response;

/**
 * Validate inputs, return true if valid, 
 * exit and return 400 status code and error details if not
 * @param array $inputs Array of variables to be validated
 * @param array $validations Array of validation rules
 * @return boolean
 */
function validate($inputs, $validations) {
    $errors = [];

    foreach ($validations as $field => $rules) {
        $rules = explode('|', $rules);
        foreach ($rules as $rule) {
            $ruleParts = explode(':', $rule);
            $ruleName = $ruleParts[0];
            $params = isset($ruleParts[1]) ? explode(',', $ruleParts[1]) : [];
            switch ($ruleName) {
                case 'required':
                    if (!isset($inputs[$field]) || empty(trim($inputs[$field]))) {
                        $errors[$field][] = "The $field is required.";
                    }
                    break;

                case 'numeric':
                    if (isset($inputs[$field]) && !is_numeric($inputs[$field])) {
                        $errors[$field][] = "The $field must be numeric.";
                    }
                    break;

                case 'string':
                    if (isset($inputs[$field]) && (!is_string($inputs[$field]) || is_numeric($inputs[$field]))) {
                        $errors[$field][] = "The $field must be a string.";
                    }
                    break;

                case 'maxLen':
                    if (isset($inputs[$field]) && strlen($inputs[$field]) > $params[0]) {
                        $errors[$field][] = "The $field may not be greater than $params[0] characters.";
                    }
                    break;

                case 'len':
                    if (isset($inputs[$field]) && strlen($inputs[$field]) !== (int)$params[0]) {
                        $errors[$field][] = "The $field must be exactly $params[0] characters long.";
                    }
                    break;
                
                case 'max':
                    if (isset($inputs[$field]) && $inputs[$field] > $params[0]) {
                        $errors[$field][] = "The $field may not be greater than $params[0].";
                    }
                    break;
                
                case 'min':
                    if (isset($inputs[$field]) && $inputs[$field] < $params[0]) {
                        $errors[$field][] = "The $field may not be less than $params[0].";
                    }
                    break;

                case 'values':
                    if (isset($inputs[$field]) && !in_array($inputs[$field],$params)) {
                        $errors[$field][] = "The $field value must be in this list [".implode(",",$params)."]";
                    }
                    break;
            }
        }
    }
    if (!empty($errors)) {
        // Handle validation errors
        $data = [
            "status" => "Failed",
            "error(s):" => array_reduce($errors,'array_merge',[])
        ];
        response($data,400);
        exit();
    }
    return true;
}
