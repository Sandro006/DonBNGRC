<?php

namespace app\controllers;

use flight\Engine;

class HomeController
{
    private $app;

    public function __construct(Engine $app)
    {
        $this->app = $app;
    }

    /**
     * Display welcome page
     */
    public function index()
    {
        $this->app->render('welcome');
    }

    /**
     * Display hello world page
     */
    public function helloWorld($name)
    {
        echo '<h1>Hello world! Oh hey ' . htmlspecialchars($name) . '!</h1>';
    }
}
