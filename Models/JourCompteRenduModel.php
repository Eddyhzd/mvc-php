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
    protected $conges;
    protected $matin;
    protected $apresmidi;
    protected $conges_matin;
    protected $conges_apresmidi;

    public function __construct()
    {
        $this->table = 'RH_CR_JOURS';
    }

    public function findAllByDate(string $date){
        return $this->requete("SELECT * FROM {$this->table} WHERE DATE_JOUR = ?", [$date])->fetch();
    }

    public function findByMounthAndSalarie(string $dateDebut, string $dateFin, int $id_salarie){
        return $this->requete("SELECT * FROM {$this->table} WHERE DATE_JOUR BETWEEN ? AND ? AND ID_SALARIE = ?", [$dateDebut, $dateFin, $id_salarie])->fetchAll();
    }

    public function findByDateAndSalarie(string $date, int $id_salarie){
        return $this->requete("SELECT * FROM {$this->table} WHERE DATE_JOUR = ? AND ID_SALARIE = ?", [$date, $id_salarie])->fetchAll();
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

    public function getMois_cr(){
        return $this->mois_cr;
    }

    public function setMois_cr($mois_cr){
        $this->mois_cr = $mois_cr;

        return $this;
    }

    public function getStatut_cr():array{
        return $this->statut_cr;
    }

    public function setStatut_cr($statut_cr){
        $this->statut_cr = $statut_cr;

        return $this;
    }

    public function getNum_vehicule(){
        return $this->num_vehicule;
    }

    public function setNum_vehicule($num_vehicule){
        $this->num_vehicule = $num_vehicule;

        return $this;
    }

    public function getKm_debut(){
        return $this->km_debut;
    }

    public function setKm_debut($km_debut){
        $this->km_debut = $km_debut;

        return $this;
    }

    public function getKm_fin(){
        return $this->km_debut;
    }

    public function setKm_fin($km_fin){
        $this->km_fin = $km_fin;

        return $this;
    }

    public function getQte_carburant(){
        return $this->km_debut;
    }

    public function setQte_carburant($qte_carburant){
        $this->qte_carburant = $qte_carburant;

        return $this;
    }

    public function getDate_statut(){
        return $this->date_statut;
    }

    public function setDate_statut($date_statut){
        $this->date_statut = $date_statut;

        return $this;
    }

    public function getTicket(){
        return $this->nb_ticket;
    }

    public function setTicket($nb_ticket){
        $this->nb_ticket = $nb_ticket;

        return $this;
    }

    public function getDate_jour(){
        return $this->date_jour;
    }

    public function setDate_jour($date_jour){
        $this->date_jour = $date_jour;

        return $this;
    }

    public function getNotes(){
        return $this->notes;
    }

    public function setNotes($notes){
        $this->notes = $notes;

        return $this;
    }

    public function getTotal_frais(){
        return $this->total_frais;
    }

    public function setTotal_frais($total_frais){
        $this->total_frais = $total_frais;

        return $this;
    }
}