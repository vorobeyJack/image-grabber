<?php

require '../vendor/autoload.php';

try{
    $grabber = new \vrba\App\ImageManager('http');
    dump($grabber);die;
}catch (\Throwable $exception){
    dump($exception);die;
}