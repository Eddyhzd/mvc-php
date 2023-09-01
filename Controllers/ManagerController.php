<?php
namespace App\Controllers;

use App\Models\CompteRenduModel;
use App\Models\JourCompteRenduModel;
use App\Models\CongeModel;
use App\Models\UsersModel;
use App\Models\AnnoncesModel;

class ManagerController extends Controller
{
    public function index(){
        // On vérifie si on est manager
        if($this->isManager()){
            $this->affiche(date('Y-m-01'));
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
     * Cette méthode affichera une page du compte rendu correspondant aux paramètres
     * @param int $id_salarie
     * @param string $date
     * @return void 
     */
    public function compteRendu(int $id_salarie, string $date){
        // On vérifie si l'utilisateur est connecté
        if($this->isManager()){
            // On instancie le modèle correspondant au compte rendu, au jour pour les comptes rendu et au user
            $compteRenduModel = new CompteRenduModel;
            $jourCompteRenduModel = new JourCompteRenduModel;
            $user = new UsersModel;
            $congeModel = new CongeModel;

            // On va chercher le compte rendu de l'utilisateur 
            $cr = $compteRenduModel->findByDateAndSalarie(date_format(new \Datetime($date), 'Y-m-01'), $id_salarie);

            // On va chercher les jours du compte rendu de l'utilisateur 
            $jourCompteRenduModelArray = $jourCompteRenduModel->findByMonthAndSalarie(
                date_format(new \Datetime($date), 'Y-m-01'),
                date_format(new \Datetime($date), 'Y-m-t'),
                $id_salarie
            );

            $jours = [];
            foreach ($jourCompteRenduModelArray as $key => $jourArray) {
                $jour = (new JourCompteRenduModel)->hydrate($jourArray);
                array_push($jours, $jour);
            }

            // On va chercher l'utilisateur 
            $userInfos = $user->findOneById($id_salarie);
            $user->hydrate($userInfos);

            // Si le compte rendu n'existe pas, on retourne au compte rendu courant
            if(!$cr || !$jours || !$user){
                http_response_code(404);
                $_SESSION['erreur'] = "Le compte rendu choisi est introuvable";
                header('Location: /manager');
                exit;
            }

            // On va chercher les conges associé au compte rendu
            $conges = $congeModel->findByDateAndSalarie(
                date_format(new \Datetime($date), 'Y-m-01'),
                date_format(new \Datetime($date), 'Y-m-t'),
                $id_salarie
            );

            if($conges){
                // On merge les conges et les jours compte rendu
                $jours = $jourCompteRenduModel->mergeConges($jours, $conges);
            }

            $prenom = ucfirst($user->getPrenom());
            $nom = strtoupper($user->getNom());

            $chemin = 'manager/compteRendu';

            // On génère la vue
            $this->render('compteRendu/index', compact('prenom', 'nom', 'cr', 'jours', 'conges', 'chemin'));
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