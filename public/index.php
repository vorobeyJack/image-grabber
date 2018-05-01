<?php

require '../vendor/autoload.php';

try{
    $grabber = new \vrba\App\ImageManager('http://www.wwwallaboutcats.com/top-cat-blogs');
    dump($grabber->parse());die;
}catch (\Throwable $exception){
    dump($exception);die;
}
