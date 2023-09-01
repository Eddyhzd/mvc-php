<?php
namespace App\Models;

class JourCompteRenduModel extends Model
{
    protected $id;
    protected $id_salarie;
    protected $ticket;
    protected $date_jour;
    protected $notes_jour;
    protected $frais_jour;
    protected $km_vehicule_pro;
    protected $km_vehicule_perso;
    protected $conges;
    protected $matin;
    protected $apresmidi;
    protected $conges_matin;
    protected $conges_apresmidi;
    protected $color_matin;
    protected $color_apresmidi;

    private static $colors = array(
        'CP' => '#FFD26C',
        'PRI' => '#FFD26C',
        'JDR' => '#ffeaa7',
        'RTT' => '#ffbfdf',
        'EVJ' => '#b8e994',
        'XX0' => '#a6ffa6',
        'MLJ' => '#00AAF2',
        'ADI' => '#fab1a0',
        'JTR' => '#0080ff',
        'Ferie' => '#cd84f1',
        'Week-end' => '#dfe6e9',
        'Default' => '#ffffff'
    );

    public function __construct(){
        $this->table = 'RH_CR_JOURS';
        $this->base = 'ESI';
        $this->color_matin = self::$colors['Default'];
        $this->color_apresmidi = self::$colors['Default'];
    }

    public function findAllByDate(string $date){
        return $this->requete("SELECT * FROM {$this->table} WHERE DATE_JOUR = ?", [$date])->fetch();
    }

    public function findByMonthAndSalarie(string $dateDebut, string $dateFin, int $id_salarie){
        return $this->requete("SELECT * FROM {$this->table} WHERE DATE_JOUR BETWEEN ? AND ? AND ID_SALARIE = ?", [$dateDebut, $dateFin, $id_salarie])->fetchAll();
    }

    public function findByDateAndSalarie(string $date, int $id_salarie){
        return $this->requete("SELECT * FROM {$this->table} WHERE DATE_JOUR = ? AND ID_SALARIE = ?", [$date, $id_salarie])->fetch();
    }

    public function mergeConges(array $jours, array $conges){
        $congeCurr = array_shift($conges);
        $debutAbs = date('Y-m-d', strtotime($congeCurr->PCN_DATEDEBUTABS));
        $finAbs = date('Y-m-d', strtotime($congeCurr->PCN_DATEFINABS));
        $newJours = [];
        foreach ($jours as $key => $jour) {
            $dateCurr = date('Y-m-d', strtotime($jour->getDate_jour()));
            // Premier jour période de conges
            if($dateCurr == $debutAbs){
                if($congeCurr->PCN_DEBUTDJ == 'MAT'){
                    $jour = $jour->setConges_matin($congeCurr->PCN_TYPECONGE)->setColor_matin($congeCurr->PCN_TYPECONGE);
                }
                if($debutAbs != $finAbs) {
                    $jour = $jour->setConges_apresmidi($congeCurr->PCN_TYPECONGE)->setColor_apresmidi($congeCurr->PCN_TYPECONGE);
                }
            }
            // Milieu période de conges
            if (($dateCurr > $debutAbs) && ($dateCurr < $finAbs)){
                $jour = $jour->setConges_matin($congeCurr->PCN_TYPECONGE)->setConges_apresmidi($congeCurr->PCN_TYPECONGE)->setColor_matin($congeCurr->PCN_TYPECONGE)->setColor_apresmidi($congeCurr->PCN_TYPECONGE);
            }
            // Dernier jour période de conges
            if($dateCurr == $finAbs){
                if($congeCurr->PCN_FINDJ == 'PAM') {
                    $jour = $jour->setConges_apresmidi($congeCurr->PCN_TYPECONGE)->setColor_apresmidi($congeCurr->PCN_TYPECONGE);
                }
                if($debutAbs != $finAbs){
                    $jour = $jour->setConges_matin($congeCurr->PCN_TYPECONGE)->setColor_matin($congeCurr->PCN_TYPECONGE);
                }
                if(!empty($conges)){
                    $congeCurr = array_shift($conges);
                    $debutAbs = date('Y-m-d', strtotime($congeCurr->PCN_DATEDEBUTABS));
                    $finAbs = date('Y-m-d', strtotime($congeCurr->PCN_DATEFINABS));
                }
            }
            array_push($newJours, $jour);
        }
        return $newJours;
    }

    public function update(){
        $champs = [];
        $valeurs = [];

        // On boucle pour éclater le tableau
        foreach ($this as $champ => $valeur) {
            // on retire les valeurs null et les champs qui ne doivent pas être modifiés 
            if ($valeur !== null && !in_array($champ, ['db', 'table', 'id', 'base', 'id_salarie', 'date_jour', 'color_matin', 'color_apresmidi'])) {
                $champs[] = strtoupper($champ) ." = ?";
                $valeurs[] = $valeur;
            }
        }
        $valeurs[] = $this->date_jour;
        $valeurs[] = $this->id_salarie;

        // On transforme le tableau "champs" en une chaine de caractères
        $str_champs = implode(', ', $champs);

        // On exécute la requête
        return $this->requete('UPDATE ' . $this->table . ' SET ' . $str_champs . ' WHERE DATE_JOUR = ? AND ID_SALARIE = ?', $valeurs);
    }

    public function getId(){
        return $this->id;
    }

    public function setId($id):self{
        $this->id = $id;

        return $this;
    }

    public function getId_salarie(){
        return $this->id_salarie;
    }

    public function setId_salarie($id_salarie):self{
        $this->id_salarie = $id_salarie;

        return $this;
    }
    
    public function getDate_jour(){
        return $this->date_jour;
    }

    public function setDate_jour($date_jour):self{
        $this->date_jour = $date_jour;

        return $this;
    }

    public function getTicket(){
        return $this->ticket;
    }

    public function setTicket($ticket):self{
        $this->ticket = $ticket;

        return $this;
    }

    public function getNotes_jour(){
        return $this->notes_jour;
    }

    public function setNotes_jour($notes_jour):self{
        $this->notes_jour = $notes_jour;

        return $this;
    }

    public function getFrais_jour(){
        return $this->frais_jour;
    }

    public function setFrais_jour($frais_jour):self{
        $this->frais_jour = round($frais_jour, 2);

        return $this;
    }

    public function getKm_vehicule_pro(){
        return $this->km_vehicule_pro;
    }

    public function setKm_vehicule_pro($km_vehicule_pro):self{
        $this->km_vehicule_pro = round($km_vehicule_pro);

        return $this;
    }

    public function getKm_vehicule_perso(){
        return $this->km_vehicule_perso;
    }

    public function setKm_vehicule_perso($km_vehicule_perso):self{
        $this->km_vehicule_perso = round($km_vehicule_perso);

        return $this;
    }

    public function getConges_matin(){
        return $this->conges_matin;
    }

    public function setConges_matin($conges_matin):self{
        $this->conges_matin = $conges_matin;

        return $this;
    }

    public function getConges_apresmidi(){
        return $this->conges_apresmidi;
    }

    public function setConges_apresmidi($conges_apresmidi):self{
        $this->conges_apresmidi = $conges_apresmidi;

        return $this;
    }

    public function getColor_matin(){
        return $this->color_matin;
    }

    public function setColor_matin($color):self{
        $this->color_matin = self::$colors[$color];

        return $this;
    }

    public function getColor_apresmidi(){
        return $this->color_apresmidi;
    }

    public function setColor_apresmidi($color):self{
        $this->color_apresmidi = self::$colors[$color];

        return $this;
    }

    // TODO : les autre getters et setters
}