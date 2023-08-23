<?php
namespace App\Controllers;

class MainController extends Controller
{
    public function index(){
        // L'utilisateur n'est pas connecté
        header('Location: /users/login');
        exit;
    }
}