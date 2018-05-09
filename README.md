# PHP library for parsing images from sites and saving into local storage/Amazon S3

1.Install

composer require vrba/imager-grabber

2.Run

<?php

require '../vendor/autoload.php';

try{
    $grabber = new \vrba\App\ImageManager('http://somelink');
    dump($grabber->parse());die;
}catch (\Throwable $exception){
    dump($exception);die;
    
    @todo - still, need to add ability to save into S3 storage
