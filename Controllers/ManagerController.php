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
     * @param int $id_salarie
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
     * Affiche la liste des annonces sous forme de tableau
     * @return void 
     */
    public function annonces()
    {
        if($this->isManager()){
            $compteRenduModel = new CompteRenduModel;
            $usersModel = new UsersModel;

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
        if($this->isManager()){
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
        if($this->isManager()){
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