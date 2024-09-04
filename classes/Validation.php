<?php
class Validation
{
    public static function validateMobile($mobile, &$error = null)
    {
        if (empty($mobile)) {
            $error = '*Mobile Number is Required';
            return false;
        } else {
            $pattern = "/^[0][1-9][0-9]{8}$/";
            if (preg_match($pattern, $mobile)) {
                return $mobile;
            } else {
                $error = '*Enter a correct mobile number';
                return false;
            }
        }
    }

    public static function validateEmail($email, &$error = null)
    {
        if (empty($email)) {
            $error = '*Email is Required';
            return false;
        } else {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return $email;
            } else {
                $error = '*Enter the correct email format';
                return false;
            }
        }
    }

    public static function validateAmount($amount, &$error = null)
    {
        if (empty($amount)) {
            $error = '*Amount is required';
            return false;
        } else {
            if (preg_match("/^[0-9]+(\.[0-9]{1,2})?$/", $amount)) {
                return $amount;
            } else {
                $error = '*Enter the amount in correct format (e.g: 123.45)';
                return false;
            }
        }
    }

    public static function validateNumberField($number, &$error = null)
    {
        if (empty($number)) {
            $error = '*This field is required';
            return false;
        } else {
            if (preg_match("/^[0-9]+$/", $number)) {
                return $number;
            } else {
                $error = '*Please enter a valid number';
                return false;
            }
        }
    }

    public static function validateTextField($text, &$error = null)
    {
        if (empty($text)) {
            $error = '*This field is required';
            return false;
        } else {
            if (preg_match("/^[a-zA-Z0-9\s\-]+$/", $text)) {
                return $text;
            } else {
                $error = '*Please enter the input in correct format';
                return false;
            }
        }
    }

    public static function validatePasswordField($password, &$error = null)
    {
        if (empty($password)) {
            $error = '*This field is required';
            return false;
        } else {
            if (preg_match("/^[a-zA-Z0-9\s\-@]+$/", $password)) { 
                return $password;
            } else {
                $error = '*Please enter the input in correct format(You can only use letters,numbers,spaces,hypens and at symbols)';
                return false;
            }
        }
    }

    public static function validateAddressField($address, &$error = null)
    {
        if (empty($address)) {
            $error = '*This field is required';
            return false;
        } else {
            if (preg_match("/^[a-zA-Z0-9\s\-,\/]+$/", $address)) { 
                return $address;
            } else {
                $error = '*Please enter the Address in correct format';
                return false;
            }
        }
    }
}
