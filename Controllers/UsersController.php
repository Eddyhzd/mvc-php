<?php
namespace App\Controllers;

use App\Core\Form;
use App\Models\UsersModel;

class UsersController extends Controller
{
    private static array $ADMINS = [
        'fanny.lejean@eilyps.fr',
        'valerie.cochet@eilyps.fr',
        'chrystele.barbier@eilyps.fr',
        'a5sys',
        'bill.gates@eilyps.fr',
        'eddy.hazard@eilyps.fr'
    ];

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
                // On se protège contre les failles XSS
                $email = strip_tags($_POST['email']);
                $password = strip_tags($_POST['password']);

                $usersModel = new UsersModel;

                $ldapconn = ldap_connect("10.200.160.1", "389") or die("Could not connect to LDAP server.");

                ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
                ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);

                $authenticated = @ldap_bind($ldapconn, $email, $password);

                ldap_unbind($ldapconn);

                // On vérifie si le mot de passe est correct
                if($authenticated){
                    // Le mot de passe est bon
                    $userInfos = $usersModel->findOneByEmail($email);

                    $roles = ['USER'];

                    // Role de manager
                    $subs = $usersModel->findSubByDate(date('Y-m-d'), $userInfos->PSA_ID);
                    if ($subs){
                        array_push($roles, 'MANAGER');
                    }

                    // Role de Admin
                    if (in_array($email, self::$ADMINS)){
                        array_push($roles, 'ADMIN');
                    }

                    // On crée la session
                    $user = $usersModel->hydrate([
                        'id' => $userInfos->PSA_ID,
                        'email' => $userInfos->PSE_EMAILPROF,
                        'nom' => $userInfos->PSA_LIBELLE,
                        'prenom' => $userInfos->PSA_PRENOM,
                        'roles' => $roles,
                        'subs' => $subs
                    ]);
                    $user->setSession();
                    header('Location: /compteRendu');
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

        $this->render('users/login', ['loginForm' => $form->create()], 'home');
       
    }

    /**
     * Déconnexion de l'utilisateur
     * @return exit 
     */
    public function logout(){
        unset($_SESSION['user']);
        header('Location: /');
        exit;
    }

}