<?php

namespace App\Core;

// On "importe" PDO
use PDO;
use PDOException;

class Db extends PDO
{
    // Instance unique de la classe
    private static $instance;

    // Informations de connexion
    private const DB_HOST_ESI = '10.200.162.85';
    private const DB_NAME_ESI = 'ESI_REC';
    private const DB_NAME_QLIK = 'QLIK_REC';
    private const DB_USER_ESI = 'DEV_EILYPS';
    private const DB_PASS_ESI = 'NotreMdPDev3517*';
    private const DB_PORT_ESI = '30010';

    private function __construct()
    {
        // On appelle le constructeur de la classe PDO
        try{
            parent::__construct('sqlsrv:Server=' . self::DB_HOST_ESI . ',' . self::DB_PORT_ESI.';Database=' . self::DB_NAME_ESI, self::DB_USER_ESI, self::DB_PASS_ESI);

            $this->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, 'SET NAMES utf8');
            $this->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
            $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }catch(PDOException $e){
            die($e->getMessage());
        }
    }


    public static function getInstance():self
    {
        if(self::$instance === null){
            self::$instance = new self();
        }
        return self::$instance;
    }
}