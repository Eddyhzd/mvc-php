<?php
namespace App\Controllers;

use App\Core\Form;
use App\Models\UsersModel;

class UsersController extends Controller
{
    /**
     * Connexion des utilisateurs
     * @return void 
     */
    public function login(){
        // On vérifie si on atteint la page avec une requête post
        if (isset($_POST['email'], $_POST['password'])){
            // On vérifie si le formulaire est complet
            if(Form::validate($_POST, ['email', 'password'])){
                // Le formulaire est complet
                $usersModel = new UsersModel;

                $ldapconn = ldap_connect("10.200.160.1", "389") or die("Could not connect to LDAP server.");

                ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
                ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);

                $authenticated = @ldap_bind($ldapconn, $_POST['email'], $_POST['password']);

                ldap_unbind($ldapconn);

                // On vérifie si le mot de passe est correct
                if($authenticated){
                    // Le mot de passe est bon
                    // On crée la session
                    $user = $usersModel->hydrate([
                        'id' => 1,
                        'email' => $_POST['email'],
                        'roles' => 'ROLE'
                    ]);
                    $user->setSession();
                    header('Location: /');
                    exit;
                }else{
                    // Mauvais mot de passe
                    $_SESSION['erreur'] = 'L\'adresse e-mail et/ou le mot de passe est incorrect';
                    header('Location: /users/login');
                    exit;
                }
            } else {
                // formulaire invalide/incomplet
                $_SESSION['erreur'] = 'Information manquante ou non conforme';
                header('Location: /users/login');
                exit;
            }
        }
        
        
        $form = new Form;

        $form->debutForm()
            ->ajoutLabelFor('email', 'E-mail :')
            ->ajoutInput('email', 'email', ['class' => 'form-control', 'id' => 'email'])
            ->ajoutLabelFor('pass', 'Mot de passe :')
            ->ajoutInput('password', 'password', ['id' => 'pass', 'class' => 'form-control'])
            ->ajoutBouton('Me connecter', ['class' => 'btn btn-primary'])
            ->finForm();

        $this->render('users/login', ['loginForm' => $form->create()]);
       
    }

    /**
     * Déconnexion de l'utilisateur
     * @return exit 
     */
    public function logout(){
        unset($_SESSION['user']);
        header('Location: /'. $_SERVER['HTTP_REFERER']);
        exit;
    }

}