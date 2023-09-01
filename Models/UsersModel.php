<?php
namespace App\Models;

class UsersModel extends Model
{
    protected $id;
    protected $email;
    protected $nom;
    protected $prenom;
    protected $roles;
    protected $subs;
    protected $forfait;

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
                SELECT DISTINCT sal.PSA_SALARIE AS ID,
                sal.PSA_LIBELLE AS NOM,
                sal.PSA_PRENOM AS PRENOM,
                sal.PSA_PROFILREM AS FORFAIT,
                S.PSE_EMAILPROF AS EMAIL
                FROM SALARIES sal
                INNER JOIN DEPORTSAL S ON S.PSE_SALARIE=SAL.PSA_SALARIE
                UNION
                SELECT DISTINCT sal.PSA_SALARIE AS ID,
                sal.PSA_LIBELLE AS NOM,
                sal.PSA_PRENOM AS PRENOM,
                sal.PSA_PROFILREM AS FORFAIT,
                S.PSE_EMAILPROF AS EMAIL
                FROM TECMATEL.DBO.SALARIES sal
                INNER JOIN TECMATEL.[dbo].DEPORTSAL S ON S.PSE_SALARIE=SAL.PSA_SALARIE
            )
            SELECT DISTINCT RIGHT(sal.ID, 6) AS ID,
            *
            FROM TEMP SAL
            WHERE EMAIL = ?"
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
                SELECT DISTINCT sal.PSA_SALARIE AS ID,
                sal.PSA_LIBELLE AS NOM,
                sal.PSA_PRENOM AS PRENOM,
                sal.PSA_PROFILREM AS FORFAIT,
                S.PSE_EMAILPROF AS EMAIL
                FROM SALARIES sal
                INNER JOIN DEPORTSAL S ON S.PSE_SALARIE=sal.PSA_SALARIE
                UNION
                SELECT DISTINCT sal.PSA_SALARIE AS ID,
                sal.PSA_LIBELLE AS NOM,
                sal.PSA_PRENOM AS PRENOM,
                sal.PSA_PROFILREM AS FORFAIT,
                S.PSE_EMAILPROF AS EMAIL
                FROM TECMATEL.DBO.SALARIES sal
                INNER JOIN TECMATEL.[dbo].DEPORTSAL S ON S.PSE_SALARIE=sal.PSA_SALARIE
            )
            SELECT DISTINCT RIGHT(sal.ID, 6) AS ID,
            *
            FROM TEMP sal
            WHERE RIGHT(sal.ID, 6) = ?"
            , [$id_salarie])->fetch()
        ;
    }

    /**
     * Récupérer les users subordonnés à un autre user par son id.
     * @param string $date
     * @param string $id_salarie
     * @return mixed 
     */
    public function findSubByDate(string $date, string $id_salarie){
        return $this->requete(
            "WITH TEMP AS (
                SELECT DISTINCT RIGHT(PFH_SALARIE, 6) AS ID
                    ,sal.PSA_LIBELLE AS NOM
                    ,sal.PSA_PRENOM AS PRENOM
                    ,[PFH_REFERENTRH]
                    ,sal.PSA_DATESORTIE
                    ,sal.PSA_DATEENTREE
                FROM [EILYPS].[dbo].[PGAFFECTROLERH] 
                inner JOIN SALARIES sal ON PFH_SALARIE = sal.PSA_SALARIE
                UNION
                SELECT DISTINCT RIGHT(PFH_SALARIE, 6) AS ID
                    ,sal.PSA_LIBELLE AS NOM
                    ,sal.PSA_PRENOM AS PRENOM
                    ,[PFH_REFERENTRH]
                    ,sal.PSA_DATESORTIE
                    ,sal.PSA_DATEENTREE
                FROM [TECMATEL].[dbo].[PGAFFECTROLERH] 
                inner JOIN [TECMATEL].[dbo].SALARIES sal ON PFH_SALARIE = sal.PSA_SALARIE
            )
            SELECT DISTINCT ID, NOM, PRENOM FROM TEMP
            WHERE RIGHT(PFH_REFERENTRH, 6) = ?
            AND (YEAR(PSA_DATESORTIE) = 1900
            OR ? between EOMONTH(PSA_DATEENTREE, -1) and EOMONTH(PSA_DATESORTIE))"
            , [$id_salarie, $date])->fetchAll()
        ;
    }

    /**
     * Récupérer les users pour un mois donné
     * @param string $id
     * @return mixed 
     */
    public function findAllByDate(string $date){
        return $this->requete(
            "WITH TEMP AS (
                SELECT DISTINCT RIGHT(PSA_SALARIE, 6) AS ID
                    ,sal.PSA_LIBELLE AS NOM
                    ,sal.PSA_PRENOM AS PRENOM
                    ,sal.PSA_DATEENTREE
                    ,sal.PSA_DATESORTIE
                FROM SALARIES sal
                UNION
                SELECT DISTINCT RIGHT(PSA_SALARIE, 6) AS ID
                    ,sal.PSA_LIBELLE AS NOM
                    ,sal.PSA_PRENOM AS PRENOM
                    ,sal.PSA_DATEENTREE
                    ,sal.PSA_DATESORTIE
                FROM [TECMATEL].[dbo].SALARIES sal
            )
            SELECT DISTINCT ID, NOM, PRENOM FROM TEMP
            WHERE YEAR(PSA_DATESORTIE) = 1900
            OR ? between EOMONTH(PSA_DATEENTREE, -1) and EOMONTH(PSA_DATESORTIE)"
            , [$date])->fetchAll()
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
            'roles' => $this->roles,
            'subs' => $this->subs
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
        return array_unique($this->roles);
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

    /**
     * Get the value of subs
     */ 
    public function getSubs():array{
        return array_unique($this->subs);
    }

    /**
     * Set the value of subs
     *
     * @return  self
     */ 
    public function setSubs($subs):self{
        $this->subs = $subs;

        return $this;
    }
    
    /**
     * Get the value of forfait
     */ 
    public function getForfait(){
        return $this->forfait;
    }

    /**
     * Set the value of forfait
     * @param $forfait
     * @return  self
     */ 
    public function setForfait($forfait):self{
        $this->forfait = $forfait;

        return $this;
    }
}