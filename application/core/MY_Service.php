<?php

/**
 * Created by PhpStorm
 * Project name: ServiceLayer4CodeIgniter
 * File name: MY_Service.php
 * Author: Rick Lu
 * E-mail: acerest.lu@gmail.com
 * Date: 2017/4/17
 * Time: 13:48
 */
class MY_Service
{
    public function __construct()
    {
        log_message('debug', "Service Class Initialized");
    }

    function __get($key)
    {
        $CI = &get_instance();
        return $CI->$key;
    }

    public function logWriter($data, $__msg = '', $__file = null, $__line = null)
    {
        if ($__msg !== '') {
            $len = strlen($__msg);
            log_message('error', str_repeat("#", $len + 10));
            log_message('error', sprintf("#### %s ####", str_repeat(" ", $len)));
            log_message('error', sprintf("#### %s ####", $__msg));
            log_message('error', sprintf("#### %s ####", str_repeat(" ", $len)));
            log_message('error', str_repeat("#", $len + 10));
        }
        if ($__file && $__line) {
            log_message('error', "\n--------------- {$__file} at {$__line} ---------------\n");
        }
        log_message('error', json_encode($data, JSON_UNESCAPED_UNICODE));
        if ($__file && $__line) {
            log_message('error', "\n--------------- {$__file} at {$__line} ---------------\n");
        }
    }
}
