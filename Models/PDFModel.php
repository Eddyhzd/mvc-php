<?php
namespace App\Models;
use FPDF;
require_once '..\lib\fpdf\fpdf.php';

class PDFModel extends FPDF{

    protected $id;
    protected UsersModel $user;
    protected CompteRenduModel $compteRendu;
    protected array $jours;

    public function __construct(UsersModel $user, CompteRenduModel $compteRendu, array $jours){
        parent::__construct('L','mm','A4');
        $this->user = $user;
        $this->compteRendu = $compteRendu;
        $this->jours = $jours;
    }

    // Header
    function Header() {
        // Logo
        $this->Image('../public/img/EILYPS.jpg',8,2,40);
        // Saut de ligne
        $this->Ln(20);
    }
    // Footer
    function Footer() {
        // Positionnement à 1,5 cm du bas
        $this->SetY(-15);
        // Adresse
        $this->Cell(196,5,'',0,0,'C');
    }

    // Génere un pdf et le stocke dans le dossier tmp
    function generer() {
        // PDF
        $this->AddPage();
        $this->SetFont('Arial','B',12);
        $this->SetTextColor(0);
        // Texte
        $this->Text(103,10,utf8_decode("COMPTE-RENDU D'ACTIVITE MENSUEL"));
        $this->SetFont('Arial','B',10);
        if ($this->user->getForfait() == 505) {
            $this->Text(120,15,utf8_decode("Personnel au Forfait-Jours"));
        } else {
            $this->Text(120,15,utf8_decode("Personnel Horaire"));
        }
        $this->SetFont('Arial','',9);
        $this->Text(103,20,utf8_decode("NOM - PRENOM : ".strtoupper($this->user->getNom().' '.$this->user->getPrenom())));
        $this->Text(122,25,utf8_decode("MOIS de : " . $this->compteRendu->getMois()));
        $this->Text(75,30,utf8_decode("Ce compte-rendu est "));
        $this->SetTextColor(255, 0, 0);
        $this->Text(117,30,utf8_decode("A RETOURNER SIGNE avant le 5 du mois suivant"));
        // Largeur des cellules
        $width_cell = [10, 20, 20, 10, 95, 10, 55, 55];
        // Longueur des cellules
        $height_cell = 3.5;
        // Couleur des bordures
        $this->SetDrawColor(189, 195, 199);
        // Tableau avec définition des tailles colonnes
        $header = [
            utf8_decode('Jours') => $width_cell[0], 
            utf8_decode('Matin') => $width_cell[1], 
            utf8_decode('Après-midi') => $width_cell[2],
            // 06/06/2023 utf8_decode("Type d'absence") => $width_cell[3], 
            utf8_decode('TR') => $width_cell[3], 
            utf8_decode('Activité') => $width_cell[4], 
            utf8_decode("Frais") => $width_cell[5], 
            utf8_decode("Kms Perso avec véhicule de service") => $width_cell[6], 
            utf8_decode("Kms Pro avec véhicule personnel") => $width_cell[7]
        ];
        // En Tête
        $this->SetTextColor(0,0,0);
        $this->SetY(35);
        $this->SetFillColor(236, 240, 241);
        foreach ($header as $name => $size) {
            $this->Cell($size, 5, $name, 1, 0, 0, 1);
        }

        // DONNEES
        $this->SetY(40);
        foreach($this->jours as $jour){

            $fond = '#ffffff';

            $date_jour = new \Datetime($jour->getDate_jour());
            if ($date_jour->format('D') == "Sun" || $date_jour->format('D') == "Sat"){
                // Si samedi / dimanche, on grise la ligne sauf si c'est un jour travaillé
                $fond = '#dfe6e9';
                if($jour->getConges_matin() != 'JTR' && $jour->getConges_apresmidi() != 'JTR'){
                    if ($date_jour->format('D') == "Sun"){
                        $jour->setConges_matin('RH')->setConges_apresmidi('RH');
                    }else{
                        $jour->setConges_matin('D')->setConges_apresmidi('D');
                    }
                    $jour->setColor_matin('Week-end')->setColor_apresmidi('Week-end');
                }
            }
            
            list($r, $g, $b) = sscanf($fond, "#%02x%02x%02x");
            list($r_matin, $g_matin, $b_matin) = sscanf($jour->getColor_matin(), "#%02x%02x%02x");
            list($r_apresmidi, $g_apresmidi, $b_apresmidi) = sscanf($jour->getColor_apresmidi(), "#%02x%02x%02x");

            $this->setFillColor($r, $g, $b);
            $this->Cell($width_cell[0], $height_cell, $date_jour->format('d'),1,0,'C', 1);
            $this->setFillColor($r_matin, $g_matin, $b_matin);
            $this->Cell($width_cell[1], $height_cell, empty($jour->getConges_matin()) ? '1' : $jour->getConges_matin(), 1, 0, 'C', 1);
            $this->setFillColor($r_apresmidi, $g_apresmidi, $b_apresmidi);
            $this->Cell($width_cell[2], $height_cell, empty($jour->getConges_apresmidi()) ? '1' : $jour->getConges_apresmidi(), 1, 0, 'C', 1);
            $this->setFillColor($r, $g, $b);
            $this->Cell($width_cell[3], $height_cell, $jour->getTicket(), 1, 0, 'C', 1);
            $this->Cell($width_cell[4], $height_cell, $jour->getNotes_jour(), 1, 0, 'C', 1);
            $this->Cell($width_cell[5], $height_cell, empty($jour->getFrais_jour()) ? '' : round($jour->getFrais_jour(), 2), 1, 0, 'C', 1);
            $this->Cell($width_cell[6], $height_cell, empty($jour->getKm_vehicule_perso()) ? '' : $jour->getKm_vehicule_perso(), 1, 0, 'C', 1);
            $this->Cell($width_cell[7], $height_cell, empty($jour->getKm_vehicule_pro()) ? '' : $jour->getKm_vehicule_pro(), 1, 1, 'C', 1);
        }

        // Total des frais du mois
        $this->setXY(165, 148.5);
        $this->Cell($width_cell[5], $height_cell, round($this->compteRendu->getTotal_frais(), 2), 1);

        $this->SetFont('Arial','',12);

        $this->Text(140, 160, 'Nombre de TR :');
        $this->Text(150, 165, $this->compteRendu->getNb_ticket());
        $this->Rect(138, 155, 35, 12);

        $this->SetFont('Arial','',10);

        $this->SetFillColor(236, 240, 241);

        $this->setXY(190, 150);
        $this->Cell(30, 4, '', 1, 0, 'C', true);
        $this->Cell(30, 4, '', 1, 1, 'C', true);
        $this->setXY(190, 154);
        $this->Cell(30, 4, utf8_decode('Vehicule N°'), 1);
        $this->Cell(30, 4, is_null($this->compteRendu->getNum_vehicule()) ? '' : $this->compteRendu->getNum_vehicule(), 1);
        $this->setXY(190, 158);
        $this->Cell(30, 4, utf8_decode('Kms début'), 1);
        $this->Cell(30, 4, is_null($this->compteRendu->getKm_debut()) ? '' : $this->compteRendu->getKm_debut(), 1);
        $this->setXY(190, 162);
        $this->Cell(30, 4, utf8_decode('Kms fin'), 1);
        $this->Cell(30, 4, is_null($this->compteRendu->getKm_fin()) ? '' : $this->compteRendu->getKm_fin(), 1);
        $this->setXY(190, 166);
        $this->Cell(30, 4, utf8_decode('Qté carburant'), 1);
        $this->Cell(30, 4, is_null($this->compteRendu->getQte_carburant()) ? '' : $this->compteRendu->getQte_carburant(), 1);

        $this->SetTextColor(0,0,0);

        $this->Text(20,175,utf8_decode("Je déclare avoir respecté au cours du mois de " . $this->compteRendu->getMois()));
        $this->Text(20,180,utf8_decode("l'amplitude maximale de travail (13 heures), les temps minimaux de repos quotidien (11 heures) et hebdomadaire (repos dominical de 35h) prévus par la loi et"));
        $this->Text(20,185,utf8_decode("l'accord d'entreprise. Je m'engage, pour les mois à venir, à répartir ma charge de travail de manière équilibrée dans le temps"));

        $this->Text(20,195,utf8_decode("Validé par " . ucfirst($this->user->getNom()) . ' ' . strtoupper($this->user->getPrenom()) . " le " . date('d/m/Y')));
        $this->Text(150,195,utf8_decode("Signature N+1 :"));

        // Nom du fichier
        $dir = '../tmp/' . strtoupper($this->user->getNom()) . '_' . strtoupper($this->user->getPrenom());
        $nom = $dir . '/CR_' . strtoupper($this->user->getNom()) . '_' . strtoupper($this->user->getPrenom()) . $this->compteRendu->getDate_cr() . '.pdf';

        // On verifie si le dossier existe
        if (is_dir($dir) == false) {
            // Creation du dossier utilisateur
			chmod($dir, 0777);
            mkdir($dir);
        }

        // Création du PDF dans le dossier temporaire
        $this->Output($nom,'F');
    }
}