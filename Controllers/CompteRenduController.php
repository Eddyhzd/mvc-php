<?php
namespace App\Controllers;

use App\Models\CompteRenduModel;
use App\Models\JourCompteRenduModel;
use App\Models\CongeModel;
use App\Models\UsersModel;
use App\Models\PDFModel;
use App\Core\Form;

class CompteRenduController extends Controller{
    /**
     * Cette méthode affichera une page du compte rendu courant de l'utilisateur
     * @return void 
     */
    public function index(){
        // On vérifie si l'utilisateur est connecté
        if($this->isLogin()){
            $this->affiche($_SESSION['user']['id'], date('Y-m-01'));
        }
    }

    /**
     * Cette méthode affichera une page du compte rendu correspondant aux paramètres
     * @param int $id_salarie
     * @param string $date
     * @return void 
     */
    public function affiche(int $id_salarie, string $date){
        // On vérifie si l'utilisateur est connecté
        if($this->isLogin()){
            // On vérifie si l'utilisateur est propriétaire du compte rendu
            if($id_salarie != $_SESSION['user']['id']){
                $_SESSION['erreur'] = "Vous n'êtes pas autorisé à accéder ce compte rendu";
                header('Location: /compteRendu');
                exit;
            }

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
                header('Location: /compteRendu');
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

            $chemin = 'compteRendu/affiche';

            // On génère la vue
            $this->render('compteRendu/index', compact('prenom', 'nom', 'cr', 'jours', 'conges', 'chemin'));
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
        if($this->isLogin()){

            // On vérifie si l'utilisateur est propriétaire du compte rendu, manager ou admin
            $chemin = 'compteRendu/affiche';
            if($id_salarie != $_SESSION['user']['id']){
                if(in_array('MANAGER', $_SESSION['user']['roles']) && in_array($id_salarie, array_column($_SESSION['user']['subs'], 'ID_SALARIE'))){
                    $chemin = 'manager/compteRendu';
                }elseif(in_array('ADMIN', $_SESSION['user']['roles'])){
                    $chemin = 'admin/compteRendu';
                }else{
                    $_SESSION['erreur'] = "Vous n'êtes pas autorisé à modifier ces informations";
                    header('Location: /compteRendu');
                    exit;
                }
            }

            // On instancie le modèle correspondant au compte rendu
            $compteRenduModel = new CompteRenduModel;

            // On va chercher le compte rendu courant de l'utilisateur 
            $cr = $compteRenduModel->findByDateAndSalarie($date, $id_salarie);

            // Si le compte rendu n'existe pas, on retourne au compte rendu courant
            if(!$cr){
                http_response_code(404);
                $_SESSION['erreur'] = "Le compte rendu choisi n'est pas modifiable";
                header("Location: /compteRendu/affiche/{$id_salarie}/{$date}");
                exit;
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
                header("Location: /{$chemin}/{$id_salarie}/{$date}");
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
                    'value' => round($cr->KM_DEBUT),
                    'min' => 0,
                    'max' => 1000000,
                    'step' => 1
                ])
                ->ajoutLabelFor('km_fin', 'Kms Fin :')
                ->ajoutInput('number', 'km_fin', [
                    'id' => 'km_fin',
                    'class' => 'form-control',
                    'value' => round($cr->KM_FIN),
                    'min' => 0,
                    'max' => 1000000,
                    'step' => 1
                ])
                ->ajoutLabelFor('qte_carburant', 'Quantité de carburant (L) :')
                ->ajoutInput('number', 'qte_carburant', [
                    'id' => 'qte_carburant',
                    'class' => 'form-control',
                    'value' => $cr->QTE_CARBURANT,
                    'min' => 0,
                    'max' => 1000,
                    'step' => 1
                ])
                ->ajoutRetour('Retour', '/compteRendu/affiche/' .$id_salarie. '/' . $date , ['class' => 'btn btn-outline-danger'])
                ->ajoutBouton('Modifier', ['class' => 'btn btn-primary pull-right'])
                ->finForm()
            ;

            // On génère la vue
            $this->render('compteRendu/modifier', ['form' => $form->create(), 'date' => $date, 'id_salarie' => $id_salarie]);
        }
    }

    /**
     * Cette méthode génere un pdf et l'envoie par mail
     * @param int $id_salarie
     * @param string $date
     * @return void 
     */
    public function envoyer(int $id_salarie, string $date){
        // On vérifie si l'utilisateur est connecté
        if($this->isLogin()){

            // On vérifie si l'utilisateur est propriétaire du compte rendu, manager ou admin
            $chemin = 'compteRendu/affiche';
            if($id_salarie != $_SESSION['user']['id']){
                if(in_array('MANAGER', $_SESSION['user']['roles']) && in_array($id_salarie, array_column($_SESSION['user']['subs'], 'ID_SALARIE'))){
                    $chemin = 'manager/compteRendu';
                }elseif(in_array('ADMIN', $_SESSION['user']['roles'])){
                    $chemin = 'admin/compteRendu';
                }else{
                    $_SESSION['erreur'] = "Vous n'êtes pas autorisé à modifier ces informations";
                    header('Location: /compteRendu');
                    exit;
                }
            }

            // On instancie le modèle correspondant au compte rendu, au jour pour les comptes rendu et au user
            $compteRendu = new CompteRenduModel;
            $jourCompteRenduModel = new JourCompteRenduModel;
            $user = new UsersModel;
            $congeModel = new CongeModel;

            // On va chercher le compte rendu de l'utilisateur 
            $compteRenduInfos = $compteRendu->findByDateAndSalarie(date_format(new \Datetime($date), 'Y-m-01'), $id_salarie);
            $compteRendu->hydrate($compteRenduInfos);
            $compteRendu->setMois(CompteRenduModel::$MOIS[date_format(new \Datetime($date), 'm')-1]);

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
            $userArray = $user->findOneById($id_salarie);
            $user->hydrate($userArray);

            // Si le compte rendu n'existe pas, on retourne au compte rendu courant
            if(!$compteRendu || !$jours || !$user){
                http_response_code(404);
                $_SESSION['erreur'] = "Le compte rendu choisi est introuvable";
                header('Location: /compteRendu');
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

                // TODO: Refléchir à la réel importance de stocker les congés dans la table. Avec cette architecture, ce n'est pas obligatiore
                // Update en prévention des congés arrivées pendant le mois courant
                foreach ($jours as $key => $jour) {
                    $jour->update();
                }
            }

            // Génération du pdf
            $pdf = new PDFModel($user, $compteRendu, $jours);
            $pdf->generer();

            // On redirige
            $_SESSION['message'] = "Compte rendu du " . date_format(new \Datetime($compteRendu->getDate_cr()), 'Y-m') . " envoyé avec succes";
            header("Location: /{$chemin}/{$id_salarie}/{$date}");
            exit;
        }
    }
}