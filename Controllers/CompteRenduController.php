<?php
namespace App\Controllers;

use App\Models\CompteRenduModel;
use App\Models\JourCompteRenduModel;

class CompteRenduController extends Controller
{
    /**
     * Cette méthode affichera une page du compte rendu courant de l'utilisateur
     * @return void 
     */
    public function index()
    {
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
            $_SESSION['erreur'] = "Vous devez être connecté(e) pour accéder à cette page";
            header('Location: /users/login');
            exit;
        }
    }

    public function modifierTicket(){
        // On vérifie si l'utilisateur est connecté
        if(isset($_SESSION['user']) && !empty($_SESSION['user']['id'])){
            // On va vérifier si le jour existe dans la base
            // On instancie notre modèle
            $jourCompteRenduModel = new JourCompteRenduModel;

            // On cherche l'annonce avec l'id $id
            $jour = $jourCompteRenduModel->findByDateAndSalarie($_POST['date'], $_POST['id_salarie']);

            // Si le jour n'existe pas, on retourne au compte rendu
            if(!$jour){
                http_response_code(404);
                $_SESSION['erreur'] = "Le jour choisi n'est pas modifiable";
                header('Location: /compteRendu');
                exit;
            }

            // On vérifie si l'utilisateur est propriétaire de l'annonce ou admin
            if($jour->ID_SALARIE !== $_SESSION['user']['id']){
                if(!in_array('ROLE_ADMIN', $_SESSION['user']['roles'])){
                    $_SESSION['erreur'] = "Vous n'avez pas accès à cette page";
                    header('Location: /compteRendu');
                    exit;
                }
            }

            // On traite le formulaire
            if(Form::validate($_POST, ['date', 'id_salarie', 'ticket'])){
                // On se protège contre les failles XSS
                $date = strip_tags($_POST['date']);
                $id_salarie = strip_tags($_POST['id_salarie']);
                $ticket = strip_tags($_POST['ticket']);

                // On stocke l'annonce
                $jourCompteRenduModif = new JourCompteRenduModel;

                // On hydrate
                $jourCompteRenduModif->setId_salarie($jour->ID_SALARIE)
                    ->setDate_jour($date)
                    ->setTicket($ticket);

                // On met à jour l'annonce
                $jourCompteRenduModif->update();
            }

            // On redirige
            header('Location: /compteRendu');
            exit;
        }else{
            // L'utilisateur n'est pas connecté
            $_SESSION['erreur'] = "Vous devez être connecté(e) pour accéder à cette page";
            header('Location: /users/login');
            exit;
        }
    }
}