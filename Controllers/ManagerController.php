<?php
namespace App\Controllers;

use App\Models\CompteRenduModel;
use App\Models\UsersModel;

class ManagerController extends Controller
{
    public function index()
    {
        // On vérifie si on est manager
        if($this->isManager()){
            $compteRenduModel = new CompteRenduModel;
            $usersModel = new UsersModel;

            // Récupération des comptes rendus 
            $crs = $compteRenduModel->findByDateAndSalaries(date('Y-m-01'), array_column($_SESSION['user']['subs'], 'ID_SALARIE'));
            $users = $usersModel->findSubByDate(date('Y-m-d'), $_SESSION['user']['id']);

            $this->render('manager/index', ['crs' => $crs, 'users' => $users, 'date' => date('Y-m')]);
        }
    }

    /**
     * Cette méthode affichera une page du compte rendu correspondant aux paramètres
     * @param string $date
     * @return void 
     */
    public function affiche(string $date){
        // On vérifie si on est manager
        if($this->isManager()){
            $compteRenduModel = new CompteRenduModel;
            $usersModel = new UsersModel;

            // Récupération des comptes rendus 
            $crs = $compteRenduModel->findByDateAndSalaries(date_format(new \Datetime($date), 'Y-m-01'), array_column($_SESSION['user']['subs'], 'ID_SALARIE'));
            $users = $usersModel->findSubByDate($date, $_SESSION['user']['id']);

            $this->render('manager/index', ['crs' => $crs, 'users' => $users, 'date' => $date]);
        }
    }

    /**
     * Vérifie si on est manager
     * @return true 
     */
    private function isManager()
    {
        // On vérifie si on est connecté et si "MANAGER" est dans nos rôles
        if(isset($_SESSION['user']) && in_array('MANAGER', $_SESSION['user']['roles'])){
            // On est manager
            return true;
        }else{
            // On n'est pas manager
            $_SESSION['erreur'] = "Vous n'avez pas accès à cette zone";
            header('Location: /compteRendu');
            exit;
        }
    }

}