<?php
namespace App\Controllers;

use App\Models\JourCompteRenduModel;
use App\Core\Form;

class JourCompteRenduController extends Controller
{
    /**
     * Modifier un Jour pour ajouter/supprimer un ticket
     * @return void 
     */
    public function modifierTicket(){
        // On vérifie si l'utilisateur est connecté et accède via le formulaire
        if(isset($_SESSION['user']) && !empty($_SESSION['user']['id']) && $_SERVER['REQUEST_METHOD'] === 'POST'){
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

            // On vérifie si l'utilisateur est propriétaire du compte rendu ou admin
            if($jour->ID_SALARIE != $_SESSION['user']['id']){
                if(!in_array('ROLE_ADMIN', $_SESSION['user']['roles'])){
                    $_SESSION['erreur'] = "Vous ne pouvez pas modifier ce compte rendu";
                    header('Location: /compteRendu');
                    exit;
                }
            }

            // On traite le formulaire
            if(Form::validate($_POST, ['date', 'id_salarie'])){
                // On se protège contre les failles XSS
                $date = strip_tags($_POST['date']);
                $id_salarie = strip_tags($_POST['id_salarie']);
                $ticket = strip_tags($_POST['ticket']);

                // On stocke le jour
                $jourCompteRenduModif = new JourCompteRenduModel;

                // On hydrate le jour
                $jourCompteRenduModif->setId_salarie($jour->ID_SALARIE)
                    ->setDate_jour($date)
                    ->setTicket($ticket);

                // On met à jour le jour du compte rendu
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

    /**
     * Modifier un Jour
     * @param int $id_salarie
     * @param string $date
     * @return void 
     */
    public function modifierJour(int $id_salarie, string $date){
        // On vérifie si l'utilisateur est connecté
        if(isset($_SESSION['user']) && !empty($_SESSION['user']['id'])){
            // On va vérifier si le jour existe dans la base
            // On instancie notre modèle
            $jourCompteRenduModel = new JourCompteRenduModel;

            // On cherche l'annonce avec l'id $id
            $jour = $jourCompteRenduModel->findByDateAndSalarie($date, $id_salarie);

            // Si le jour n'existe pas, on retourne au compte rendu
            if(!$jour){
                http_response_code(404);
                $_SESSION['erreur'] = "Le jour choisi n'est pas modifiable";
                header('Location: /compteRendu');
                exit;
            }

            // On vérifie si l'utilisateur est propriétaire du compte rendu ou admin
            if($jour->ID_SALARIE != $_SESSION['user']['id']){
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
                $notes = strip_tags($_POST['notes']);
                $frais = strip_tags($_POST['frais']);
                $km_perso = strip_tags($_POST['km_perso']);
                $km_pro = strip_tags($_POST['km_pro']);

                // On stocke le jour
                $jourCompteRenduModif = new JourCompteRenduModel;

                // On hydrate le jour
                $jourCompteRenduModif->setId_salarie($jour->ID_SALARIE)
                    ->setDate_jour($date)
                    ->setNotes_jour($notes)
                    ->setFrais_jour($frais)
                    ->setKm_vehicule_perso($km_perso)
                    ->setKm_vehicule_pro($km_pro);

                // On met à jour le jour du compte rendu
                $jourCompteRenduModif->update();

                // On redirige
                $_SESSION['message'] = "Note du {$date} mise à jour avec succès";
                header('Location: /compteRendu');
                exit;
            }

            $form = new Form;

            $form->debutForm()
                ->ajoutLabelFor('date', 'Date :')
                ->ajoutInput('date', 'date', [
                    'id' => 'date',
                    'class' => 'form-control',
                    'value' => $jour->DATE_JOUR
                ])
                ->ajoutLabelFor('notes', 'Notes :')
                ->ajoutTextarea('notes', isset($jour->NOTES_JOUR) ? $jour->NOTES_JOUR : '', [
                    'id' => 'description',
                    'class' => 'form-control',
                    'maxlength' => 255
                ])
                ->ajoutLabelFor('frais', 'Frais :')
                ->ajoutInput('number', 'frais', [
                    'id' => 'frais',
                    'class' => 'form-control',
                    'value' => $jour->FRAIS_JOUR,
                    'min' => 0,
                    'max' => 100000,
                    'step' => 0.01
                ])
                ->ajoutLabelFor('km_perso', 'Km Perso :')
                ->ajoutInput('number', 'km_perso', [
                    'id' => 'km_perso',
                    'class' => 'form-control',
                    'value' => $jour->KM_VEHICULE_PERSO,
                    'min' => 0,
                    'max' => 100000,
                    'step' => 0.01
                ])
                ->ajoutLabelFor('km_pro', 'Km Pro :')
                ->ajoutInput('number', 'km_pro', [
                    'id' => 'km_pro',
                    'class' => 'form-control',
                    'value' => $jour->KM_VEHICULE_PRO,
                    'min' => 0,
                    'max' => 100000,
                    'step' => 0.01
                ])
                ->ajoutBouton('Modifier', ['class' => 'btn btn-primary'])
                ->finForm()
            ;

            $this->render('jourCompteRendu/modifier', ['form' => $form->create()]);
        }else{
            // L'utilisateur n'est pas connecté
            $_SESSION['erreur'] = "Vous devez être connecté(e) pour accéder à cette page";
            header('Location: /users/login');
            exit;
        }
    }
}