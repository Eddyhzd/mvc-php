<?php
namespace App\Controllers;

class MainController extends Controller
{
    public function index(){
        // On vérifie si l'utilisateur est connecté
        if(isset($_SESSION['user']) && !empty($_SESSION['user']['id'])){
            // On instancie le modèle correspondant au compte rendu et au jour pour les comptes rendu
            $compteRenduModel = new CompteRenduModel;
            $jourCompteRenduModel = new JourCompteRenduModel;

            // On va chercher le compte rendu courant rendu de l'utilisateur 
            $cr = $compteRenduModel->findByDateAndSalarie(date('Y-m-01'), $_SESSION['user']['id']);

            // On va chercher les jours du compte rendu courant rendu de l'utilisateur 
            $jcr = $jourCompteRenduModel->findByMounthAndSalarie(date('Y-m-01'), date('Y-m-t'), $_SESSION['user']['id']);

            $prenom = 'Eddy';
            $nom = 'HAZARD';

            // On génère la vue
            $this->render('compteRendu/index', compact('prenom', 'nom', 'cr', 'jcr'));
        }else{
            // L'utilisateur n'est pas connecté
            header('Location: /users/login');
            exit;
        }
    }
}