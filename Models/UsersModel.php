<?php
namespace App\Models;

class UsersModel extends Model
{
    protected $id;
    protected $email;
    protected $nom;
    protected $prenom;
    protected $roles;

    public function __construct()
    {
        $this->table = 'SALARIE';
        $this->base = 'PGI';
    }

    /**
     * Récupérer un user à partir de son e-mail
     * @param string $email 
     * @return mixed 
     */
    public function findOneByEmail(string $email){
        return $this->requete(
            "WITH TEMP AS (
                SELECT DISTINCT sal.PSA_SALARIE,
                sal.PSA_LIBELLE,
                sal.PSA_PRENOM,
                S.PSE_EMAILPROF
                FROM SALARIES sal
                INNER JOIN DEPORTSAL S ON S.PSE_SALARIE=SAL.PSA_SALARIE
                UNION
                SELECT DISTINCT sal.PSA_SALARIE,
                sal.PSA_LIBELLE,
                sal.PSA_PRENOM,
                S.PSE_EMAILPROF
                FROM TECMATEL.DBO.SALARIES sal
                INNER JOIN TECMATEL.[dbo].DEPORTSAL S ON S.PSE_SALARIE=SAL.PSA_SALARIE
            )
            SELECT DISTINCT RIGHT(sal.PSA_SALARIE, 6) AS PSA_ID,
            *
            FROM TEMP SAL
            WHERE PSE_EMAILPROF = ?"
            , [$email])->fetch()
        ;
    }

    /**
     * Récupérer un user à partir de son id
     * @param string $id
     * @return mixed 
     */
    public function findOneById(string $id_salarie){
        return $this->requete(
            "WITH TEMP AS (
                SELECT DISTINCT sal.PSA_SALARIE,
                sal.PSA_LIBELLE,
                sal.PSA_PRENOM,
                S.PSE_EMAILPROF
                FROM SALARIES sal
                INNER JOIN DEPORTSAL S ON S.PSE_SALARIE=sal.PSA_SALARIE
                UNION
                SELECT DISTINCT sal.PSA_SALARIE,
                sal.PSA_LIBELLE,
                sal.PSA_PRENOM,
                S.PSE_EMAILPROF
                FROM TECMATEL.DBO.SALARIES sal
                INNER JOIN TECMATEL.[dbo].DEPORTSAL S ON S.PSE_SALARIE=sal.PSA_SALARIE
            )
            SELECT DISTINCT RIGHT(sal.PSA_SALARIE, 6) AS PSA_ID,
            *
            FROM TEMP sal
            WHERE RIGHT(sal.PSA_SALARIE, 6) = ?"
            , [$id_salarie])->fetch()
        ;
    }

    /**
     * Crée la session de l'utilisateur
     * @return void 
     */
    public function setSession(){
        $_SESSION['user'] = [
            'id' => $this->id,
            'email' => $this->email,
            'prenom' => $this->prenom,
            'nom' => $this->nom,
            'roles' => $this->roles
        ];
    }

    /**
     * Get the value of id
     */ 
    public function getId(){
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */ 
    public function setId($id):self{
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of email
     */ 
    public function getEmail(){
        return $this->email;
    }

    /**
     * Set the value of email
     *
     * @return  self
     */ 
    public function setEmail($email):self{
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of nom
     */ 
    public function getNom(){
        return $this->nom;
    }

    /**
     * Set the value of nom
     *
     * @return  self
     */ 
    public function setNom($nom):self{
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get the value of nom
     */ 
    public function getPrenom(){
        return $this->prenom;
    }

    /**
     * Set the value of nom
     *
     * @return  self
     */ 
    public function setPrenom($prenom):self{
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get the value of roles
     */ 
    public function getRoles():array{
        $roles = $this->roles;

        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * Set the value of roles
     *
     * @return  self
     */ 
    public function setRoles($roles):self{
        $this->roles = $roles;

        return $this;
    }
}