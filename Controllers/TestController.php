<?php
namespace App\Controllers;

class TestController extends Controller
{
    public function index()
    {
        $this->render('test', ['test' => $_SERVER['SERVER_NAME']]);
    }
}