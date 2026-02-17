<?php

namespace app\controllers;

use Flight;

class HomeController
{
    /**
     * Display welcome page
     */
    public function index()
    {
        Flight::render('welcome');
    }

    /**
     * Display hello world page
     */
    public function helloWorld($name)
    {
        echo '<h1>Hello world! Oh hey ' . htmlspecialchars($name) . '!</h1>';
    }
}
