<?php

namespace App\Core;

// On "importe" PDO
use PDO;
use PDOException;

class Db extends PDO
{
    // Instances unique de la classe
    private static $instanceESI;
    private static $instancePGI;

    // Paramètres de connexion à la base de données ESI_REC
    private const DB_HOST_ESI = '10.200.162.85';
    private const DB_NAME_ESI = 'ESI_REC';
    private const DB_NAME_QLIK = 'QLIK_REC';
    private const DB_USER_ESI = 'DEV_EILYPS';
    private const DB_PASS_ESI = 'NotreMdPDev3517*';
    private const DB_PORT_ESI = '30010';

    // Paramètres de connexion à la base de données PGI
    private const DB_HOST_PGI = '10.200.235.212';
    private const DB_NAME_PGI = 'EILYPS';
    private const DB_USER_PGI = 'PGI_Batch';
    private const DB_PASS_PGI = 'PgiAccess$22';

    private function __construct(string $base){
        // On appelle le constructeur de la classe PDO
        try{
            if($base == 'ESI'){
                parent::__construct('sqlsrv:Server=' . self::DB_HOST_ESI . ',' . self::DB_PORT_ESI.';Database=' . self::DB_NAME_ESI, self::DB_USER_ESI, self::DB_PASS_ESI);
            }elseif ($base == 'PGI'){
                parent::__construct('sqlsrv:Server=' . self::DB_HOST_PGI . ';Database=' . self::DB_NAME_PGI, self::DB_USER_PGI, self::DB_PASS_PGI);
            }
            $this->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, 'SET NAMES utf8');
            $this->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
            $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }catch(PDOException $e){
            die($e->getMessage());
        }
    }


    public static function getInstance(string $base):self{
        $getter = 'getInstance' . strtoupper($base);
        return self::$getter();
    }

    private static function getInstanceESI():self{
        if(self::$instanceESI === null){
            self::$instanceESI = new self('ESI');
        }
        return self::$instanceESI;
    }

    private static function getInstancePGI():self{
        if(self::$instancePGI === null){
            self::$instancePGI = new self('PGI');
        }
        return self::$instancePGI;
    }
}