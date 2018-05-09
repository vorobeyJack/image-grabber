# PHP library for parsing images from sites and saving into local storage/Amazon S3

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Run

```
composer require vrba/imager-grabber
```

Usage
------------

```

<?php

require '../vendor/autoload.php';

try{
    $grabber = new \vrba\App\ImageManager('http://somelink');
    dump($grabber->parse());die;
}catch (\Throwable $exception){
    dump($exception);die;
    
    @todo - still, need to add ability to save into S3 storage
