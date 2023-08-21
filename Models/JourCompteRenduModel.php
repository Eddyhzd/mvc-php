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

    public function __construct()
    {
        $this->table = 'RH_CR_JOURS';
        $this->base = 'ESI';
    }

    public function findAllByDate(string $date){
        return $this->requete("SELECT * FROM {$this->table} WHERE DATE_JOUR = ?", [$date])->fetch();
    }

    public function findByMounthAndSalarie(string $dateDebut, string $dateFin, int $id_salarie){
        return $this->requete("SELECT * FROM {$this->table} WHERE DATE_JOUR BETWEEN ? AND ? AND ID_SALARIE = ?", [$dateDebut, $dateFin, $id_salarie])->fetchAll();
    }

    public function findByDateAndSalarie(string $date, int $id_salarie){
        return $this->requete("SELECT * FROM {$this->table} WHERE DATE_JOUR = ? AND ID_SALARIE = ?", [$date, $id_salarie])->fetch();
    }

    public function update(){
        $champs = [];
        $valeurs = [];

        // On boucle pour éclater le tableau
        foreach ($this as $champ => $valeur) {
            // on retire les valeurs null et les champs qui ne doivent pas être modifiés 
            if ($valeur !== null && !in_array($champ, ['db', 'table', 'id', 'id_salarie', 'date_jour'])) {
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

    public function setId($id){
        $this->id = $id;

        return $this;
    }

    public function getId_salarie(){
        return $this->id_salarie;
    }

    public function setId_salarie($id_salarie){
        $this->id_salarie = $id_salarie;

        return $this;
    }
    
    public function getDate_jour(){
        return $this->date_jour;
    }

    public function setDate_jour($date_jour){
        $this->date_jour = $date_jour;

        return $this;
    }

    public function getTicket(){
        return $this->ticket;
    }

    public function setTicket($ticket){
        $this->ticket = $ticket;

        return $this;
    }

    public function getNotes_jour(){
        return $this->notes_jour;
    }

    public function setNotes_jour($notes_jour){
        $this->notes_jour = $notes_jour;

        return $this;
    }

    public function getFrais_jour(){
        return $this->frais_jour;
    }

    public function setFrais_jour($frais_jour){
        $this->frais_jour = $frais_jour;

        return $this;
    }

    public function getKm_vehicule_pro(){
        return $this->km_vehicule_pro;
    }

    public function setKm_vehicule_pro($km_vehicule_pro){
        $this->km_vehicule_pro = $km_vehicule_pro;

        return $this;
    }

    public function getKm_vehicule_perso(){
        return $this->km_vehicule_perso;
    }

    public function setKm_vehicule_perso($km_vehicule_perso){
        $this->km_vehicule_perso = $km_vehicule_perso;

        return $this;
    }

    // TODO : les autre getters et setters
}