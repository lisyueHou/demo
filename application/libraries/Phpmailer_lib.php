<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * CodeIgniter PHPMailer Class
 *
 * This class enables SMTP email with PHPMailer
 *
 * @category    Libraries
 * @author      CodexWorld
 * @link        https://www.codexworld.com
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class PHPMailer_Lib
{
    public function __construct()
    {
        log_message('Debug', 'PHPMailer class is loaded.');
    }

    public function load()
    {
        // Include PHPMailer library files
        require 'vendor/autoload.php';

        $mail = new PHPMailer;
        // SMTP configuration
        $mail->isSMTP();
        // 信件內容的編碼方式
        $mail->CharSet = PHPMailer::CHARSET_UTF8;
        $mail->Encoding = 'base64';
        $mail->Host     = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'twmyshine@gmail.com';
        $mail->Password = 'dcsewqjjpxucngqv';
        $mail->SMTPSecure = 'ssl';
        $mail->Port     = 465;

        $mail->setFrom('twmyshine@gmail.com', '管線清潔機器人管理系統');
        $mail->addReplyTo('twmyshine@gmail.com', '管線清潔機器人管理系統');

        return $mail;
    }
}
