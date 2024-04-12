<?php
require 'formajax.php';

function not_request($error = null) {
    header('HTTP/1.0 404 Not Found');
    die($error);
}
if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND $_SERVER['HTTP_X_REQUESTED_WITH'] == 'FormAjaxRequest') {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'POST': {
            echo (new formajax())->send(
                'to@to.to',
                'Тема письма',
                'Сообщение с сайта',

                // smtp
                /*array(
                    'host' => 'smtp.yandex.ru',
                    'username' => '@yandex.ru',
                    'password' => ''
                )*/
                null,

                // callback после успешной отправки
                function($fields) {
                    return $fields;
                }
            );
            break;
        }

        default: {
            not_request('Only post request');
        }
    }
} else {
    not_request();
}
