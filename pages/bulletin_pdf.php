<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . "/../traitement/fpdf/fpdf.php";
require_once __DIR__ . "/../traitement/requette.php";

$matricule = $_GET['matricule'] ?? null;

if(!$matricule){
    die("Erreur : aucun matricule fourni !");
}

$etudiant = getEtudiantByMatricule($matricule);

if(!$etudiant){
    die("Erreur : étudiant introuvable !");
}

$notes = getNotesByEtudiant($etudiant['idetudiant']);

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);

$pdf->Cell(0,10,"BULLETIN DE NOTES",0,1,'C');
$pdf->Ln(5);

$pdf->SetFont('Arial','',12);
$pdf->Cell(0,8,"Nom : ".$etudiant['nom']." ".$etudiant['prenom'],0,1);
$pdf->Cell(0,8,"Matricule : ".$etudiant['matricule'],0,1);
$pdf->Cell(0,8,"Classe : ".$etudiant['classe'],0,1);

$pdf->Ln(10);

$pdf->SetFont('Arial','B',12);
$pdf->Cell(70,8,"Module",1);
$pdf->Cell(30,8,"Coef",1);
$pdf->Cell(30,8,"Note",1);
$pdf->Cell(30,8,"Total",1);
$pdf->Ln();

$pdf->SetFont('Arial','',12);

$totalGeneral = 0;
$totalCoef = 0;

if($notes && count($notes) > 0){
    foreach($notes as $n){

        $note = (float)$n['note'];
        $coef = (float)$n['coefficient'];
        $total = $note * $coef;

        $totalGeneral += $total;
        $totalCoef += $coef;

        $pdf->Cell(70,8,$n['nommodule'],1);
        $pdf->Cell(30,8,$coef,1);
        $pdf->Cell(30,8,$note,1);
        $pdf->Cell(30,8,$total,1);
        $pdf->Ln();
    }

    $moyenne = $totalCoef > 0 ? $totalGeneral / $totalCoef : 0;

    $pdf->Ln(5);
    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(0,10,"Moyenne Generale : ".number_format($moyenne,2),0,1);
}else{
    $pdf->Cell(0,10,"Aucune note disponible.",0,1);
}

$pdf->Output();
exit;