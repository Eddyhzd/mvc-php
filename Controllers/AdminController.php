<?php
namespace App\Controllers;

use App\Models\CompteRenduModel;
use App\Models\UsersModel;
use App\Models\AnnoncesModel;
use PDOException;

class AdminController extends Controller{

    public function index(){
        // On vérifie si on est admin
        if($this->isAdmin()){
            $compteRenduModel = new CompteRenduModel;
            $usersModel = new UsersModel;

            // Récupération des comptes rendus 
            $crs = $compteRenduModel->findAllByDate(date('Y-m-01'));
            $users = $usersModel->findAllByDate(date('Y-m-d'));

            $this->render('admin/index', ['crs' => $crs, 'users' => $users, 'date' => date('Y-m')]);
        }
    }

    /**
     * Cette méthode affichera une page du compte rendu correspondant aux paramètres
     * @param string $date
     * @return void 
     */
    public function affiche(string $date){
        // On vérifie si on est admin
        if($this->isAdmin()){
            $compteRenduModel = new CompteRenduModel;
            $usersModel = new UsersModel;

            // Récupération des comptes rendus 
            $crs = $compteRenduModel->findAllByDate(date_format(new \Datetime($date), 'Y-m-01'), array_column($_SESSION['user']['subs'], 'ID_SALARIE'));
            $users = $usersModel->findAllByDate($date, $_SESSION['user']['id']);

            $this->render('admin/index', ['crs' => $crs, 'users' => $users, 'date' => $date]);
        }
    }

    /**
     * Affiche la liste des annonces sous forme de tableau
     * @return void 
     */
    public function annonces()
    {
        if($this->isAdmin()){
            $annoncesModel = new AnnoncesModel;

            $annonces = $annoncesModel->findAll();

            $this->render('admin/annonces', compact('annonces'), 'admin');
        }
    }

    /**
     * Supprime une annonce si on est admin
     * @param int $id 
     * @return void 
     */
    public function supprimeAnnonce(int $id)
    {
        if($this->isAdmin()){
            $annonce = new AnnoncesModel;

            $annonce->delete($id);

            header('Location: /'.$_SERVER['HTTP_REFERER']);
        }
    }

    /**
     * Active ou désactive une annonce
     * @param int $id 
     * @return void 
     */
    public function activeAnnonce(int $id)
    {
        if($this->isAdmin()){
            $annoncesModel = new AnnoncesModel;

            $annonceArray = $annoncesModel->find($id);

            if($annonceArray){
                $annonce = $annoncesModel->hydrate($annonceArray);

                // if($annonce->getActif()){
                //     $annonce->setActif(0);
                // }else{
                //     $annonce->setActif(1);
                // }

                $annonce->setActif($annonce->getActif() ? 0 : 1);

                $annonce->update();
            }
        }
    }


    /**
     * Vérifie si on est admin
     * @return true 
     */
    private function isAdmin()
    {
        // On vérifie si on est connecté et si "ADMIN" est dans nos rôles
        if(isset($_SESSION['user']) && in_array('ADMIN', $_SESSION['user']['roles'])){
            // On est admin
            return true;
        }else{
            // On n'est pas admin
            $_SESSION['erreur'] = "Vous n'avez pas accès à cette zone";
            header('Location: /compteRendu');
            exit;
        }
    }

}