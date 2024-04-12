<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

class formajax {

    const VERSION = '1.0.0';
    private $post;
    private $names;
    private $mail;
    private $error = null;
    private $status = true;

    public function __construct ()
    {
        $rules = [];
        foreach ($_POST as $key => $item)
        {
            $rules[$key] = FILTER_SANITIZE_FULL_SPECIAL_CHARS;
        }
        $this->post = filter_input_array(INPUT_POST, $rules);
        $this->names = isset($this->post['fa_names']) ? json_decode(str_replace('&#34;','"',$this->post['fa_names']),1) : array();
        unset($this->post['fa_names']);
        $this->mail = new PHPMailer;
    }

    private function setSettings ($subject, $fromname, $smtp)
    {
        // Настройки SMTP
        if ($smtp) {
            $this->mail->isSMTP();
            $this->mail->SMTPDebug = 0;
            $this->mail->Host = $smtp['host'];
            $this->mail->SMTPAuth = true;
            $this->mail->Username = $smtp['username'];
            $this->mail->Password = $smtp['password'];
            $this->mail->SMTPSecure = 'ssl';
            $this->mail->Port = 465;
            $this->mail->setFrom($smtp['username'],$fromname);
        } else {
            // От кого пришло
            $host = parse_url('http://'.$_SERVER['HTTP_HOST']);
            $from = 'noreply@' . str_replace(array('http://','www.','https://'),'',$host['host']);
            $this->mail->AddCustomHeader("List-Unsubscribe: <mailto:{$from}?subject=Unsubscribe>");
            $this->mail->setFrom($from,$fromname);
        }
        $this->mail->CharSet = 'utf-8';

        // Тема письма
        $this->mail->Subject = $subject;

        // Тело письма
        $this->mail->isHTML(true);
        $this->mail->Body = $this->getBody();
        $this->mail->AltBody = $this->getText();
    }

    private function getBody ()
    {
        $data = '';
        if (count($this->post)) {
            $data .= '<html><body><div style="background:#f7fbff;border:1px solid #e5e5e5;padding:5px 15px;">';
            foreach ($this->post as $key => $item) {
                if ($item) {
                    $name = isset($this->names[$key]) ? $this->names[$key] : $key;
                    $data .= '<p><strong>' . $name . ':</strong> ' . $item . '<br/></p>';
                }
            }
            $data .= '</div>';
        }
        $data .= '<div style="background:#f5f5f5;border:1px solid #e5e5e5;padding:10px 15px;margin:10px 0;font-size:80%;">IP: ' . $_SERVER['REMOTE_ADDR'] . '<br/> Referer: ' . $_SERVER['HTTP_REFERER'] . '<br/> UserAgent: ' . $_SERVER['HTTP_USER_AGENT'] . '</div></body></html>';
        return $data;
    }

    private function getText()
    {
        $data = '';
        if (count($this->post)) {
            foreach ($this->post as $key => $item) {
                if ($item) {
                    $name = isset($this->names[$key]) ? $this->names[$key] : $key;
                    $data .= $name . ': ' . $item . '; ' . PHP_EOL;
                }
            }
        }
        $data .= PHP_EOL . 'IP: ' . $_SERVER['REMOTE_ADDR'] . '; ' . PHP_EOL.
            'Referer: ' . $_SERVER['HTTP_REFERER'] . '; ' . PHP_EOL .
            'UserAgent: ' . $_SERVER['HTTP_USER_AGENT'] . '; ';
        return $data;
    }

    private function addAttach ($tmp_name, $name, $size)
    {
        if ($size >= 10485760) { // 10 МБ
            $this->error = 'Слишком большой размер файла';
            return false;
        } else {
            $this->mail->AddAttachment($tmp_name, $name);
            return true;
        }
    }

    private function getFiles ()
    {
        foreach ($_FILES as $item) {
            if (is_array($item['name'])) {
                foreach ($item['name'] as $i => $name) {
                    if (!$this->addAttach($item['tmp_name'][$i], $name, $item['size'][$i])) {
                        break;
                    }
                }
            } else {
                if (!$this->addAttach($item['tmp_name'], $item['name'], $item['size'])) {
                    break;
                }
            }
        }
    }

    public function send ($to = null, $subject = 'Обратный звонок', $fromname = null, $smtp = null, $callback = null)
    {
        $subject = isset($this->post['fa_subject']) ? $this->post['fa_subject'] : $subject;
        unset($this->post['fa_subject']);
        $to = isset($this->post['fa_to']) ? $this->post['fa_to'] : $to;
        unset($this->post['fa_to']);
        if ($to && $to !== 'to@to.to') {
            foreach (explode(',', $to) as $item) {
                $this->mail->addAddress(trim($item));
            }
            $this->setSettings($subject, $fromname, $smtp);
            $this->getFiles();
            if ($this->error) {
                $out = $this->error;
                $this->status = false;
            } elseif (!$this->mail->send()) {
                $out = $this->mail->ErrorInfo;
                $this->status = false;
            } else {
                $out = 'Сообщение отправлено';
                if ($callback) {
                    call_user_func($callback, $this->post);
                }
            }
        } else {
            $out = 'Укажите адрес получателя!';
            $this->status = false;
        }

        return json_encode(array(
            'messages' => $out,
            'status' => $this->status
        ), JSON_UNESCAPED_UNICODE);
    }

}
