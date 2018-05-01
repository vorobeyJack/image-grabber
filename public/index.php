<?php

require '../vendor/autoload.php';

try{
    $grabber = new \vrba\App\ImageManager('http://somelink');
    dump($grabber->parse());die;
}catch (\Throwable $exception){
    dump($exception);die;
}
