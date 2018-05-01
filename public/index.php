<?php

require '../vendor/autoload.php';

try{
    $grabber = new \vrba\App\ImageManager('https://habr.com/company/mailru/blog/115163');
    dump($grabber->parse());die;
}catch (\Throwable $exception){
    dump($exception);die;
}
