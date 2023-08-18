<?php
namespace App\Controllers;

use App\Models\CompteRenduModel;
use App\Models\JourCompteRenduModel;
use App\Core\Form;

class CompteRenduController extends Controller{
    /**
     * Cette méthode affichera une page du compte rendu courant de l'utilisateur
     * @return void 
     */
    public function index(){
        // On vérifie si l'utilisateur est connecté
        if(isset($_SESSION['user']) && !empty($_SESSION['user']['id'])){
            // On instancie le modèle correspondant au compte rendu et au jour pour les comptes rendu
            $compteRenduModel = new CompteRenduModel;
            $jourCompteRenduModel = new JourCompteRenduModel;

            // On va chercher le compte rendu courant de l'utilisateur 
            $cr = $compteRenduModel->findByDateAndSalarie(date('Y-m-01'), $_SESSION['user']['id']);

            // On va chercher les jours du compte rendu courant de l'utilisateur 
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

    /**
     * Modifier infos véhicule
     * @param int $id_salarie
     * @param string $date
     * @return void 
     */
    public function modifierVehicule(int $id_salarie, string $date){
        // On vérifie si l'utilisateur est connecté
        if(isset($_SESSION['user']) && !empty($_SESSION['user']['id'])){
            // On instancie le modèle correspondant au compte rendu
            $compteRenduModel = new CompteRenduModel;

            // On va chercher le compte rendu courant de l'utilisateur 
            $cr = $compteRenduModel->findByDateAndSalarie($date, $id_salarie);

            // Si le compte rendu n'existe pas, on retourne au compte rendu courant
            if(!$cr){
                http_response_code(404);
                $_SESSION['erreur'] = "Le compte rendu choisi n'est pas modifiable";
                header('Location: /compteRendu');
                exit;
            }

            // On vérifie si l'utilisateur est propriétaire du compte rendu ou admin
            if($cr->ID_SALARIE != $_SESSION['user']['id']){
                if(!in_array('ROLE_ADMIN', $_SESSION['user']['roles'])){
                    $_SESSION['erreur'] = "Vous ne pouvez pas modifier ce compte rendu";
                    header('Location: /compteRendu');
                    exit;
                }
            }

            // On traite le formulaire
            if($_SERVER['REQUEST_METHOD'] === 'POST'){
                // On se protège contre les failles XSS
                $date = strip_tags($date);
                $id_salarie = strip_tags($id_salarie);
                $num_vehicule = strip_tags($_POST['num_vehicule']);
                $km_debut = strip_tags($_POST['km_debut']);
                $km_fin = strip_tags($_POST['km_fin']);
                $qte_carburant = strip_tags($_POST['qte_carburant']);

                // On stocke le jour
                $compteRenduModif = new CompteRenduModel;

                // On hydrate le jour
                $compteRenduModif->setId_salarie($cr->ID_SALARIE)
                    ->setDate_cr($date)
                    ->setNum_vehicule($num_vehicule)
                    ->setKm_debut($km_debut)
                    ->setKm_fin($km_fin)
                    ->setQte_carburant($qte_carburant);

                // On met à jour le jour du compte rendu
                $compteRenduModif->update();

                // On redirige
                $_SESSION['message'] = "Infos du compte rendu du " . date_format(new \Datetime($cr->DATE_CR), 'Y-m') . " mise à jour avec succès";
                header('Location: /compteRendu');
                exit;
            }

            $form = new Form;

            $form->debutForm()
                ->ajoutLabelFor('num_vehicule', 'Immatriculation :')
                ->ajoutTextarea('num_vehicule', isset($cr->NUM_VEHICULE) ? $cr->NUM_VEHICULE : '', [
                    'id' => 'num_vehicule',
                    'class' => 'form-control',
                    'maxlength' => 9
                ])
                ->ajoutLabelFor('km_debut', 'Kms Début :')
                ->ajoutInput('number', 'km_debut', [
                    'id' => 'km_debut',
                    'class' => 'form-control',
                    'value' => $cr->KM_DEBUT,
                    'min' => 0,
                    'max' => 1000000
                ])
                ->ajoutLabelFor('km_fin', 'Kms Fin :')
                ->ajoutInput('number', 'km_fin', [
                    'id' => 'km_fin',
                    'class' => 'form-control',
                    'value' => $cr->KM_FIN,
                    'min' => 0,
                    'max' => 1000000
                ])
                ->ajoutLabelFor('qte_carburant', 'Quantité de carburant (L) :')
                ->ajoutInput('number', 'qte_carburant', [
                    'id' => 'qte_carburant',
                    'class' => 'form-control',
                    'value' => $cr->QTE_CARBURANT,
                    'min' => 0,
                    'max' => 1000,
                    'step' => 0.01
                ])
                ->ajoutBouton('Modifier', ['class' => 'btn btn-primary'])
                ->finForm()
            ;

            // On génère la vue
            $this->render('compteRendu/modifier', ['form' => $form->create()]);
        }else{
            // L'utilisateur n'est pas connecté
            $_SESSION['erreur'] = "Vous devez être connecté(e) pour accéder à cette page";
            header('Location: /users/login');
            exit;
        }
    }
}