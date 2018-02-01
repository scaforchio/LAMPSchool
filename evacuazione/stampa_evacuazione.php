<?php session_start();

/*
Copyright (C) 2015 Pietro Tamburrano
Questo programma è un software libero; potete redistribuirlo e/o modificarlo secondo i termini della
GNU Affero General Public License come pubblicata
dalla Free Software Foundation; sia la versione 3,
sia (a vostra scelta) ogni versione successiva.

Questo programma é distribuito nella speranza che sia utile
ma SENZA ALCUNA GARANZIA; senza anche l'implicita garanzia di
POTER ESSERE VENDUTO o di IDONEITA' A UN PROPOSITO PARTICOLARE.
Vedere la GNU Affero General Public License per ulteriori dettagli.

Dovreste aver ricevuto una copia della GNU Affero General Public License
in questo programma; se non l'avete ricevuta, vedete http://www.gnu.org/licenses/
*/

@require_once("../php-ini".$_SESSION['suffisso'].".php");
@require_once("../lib/funzioni.php");
require_once("../lib/fpdf/fpdf.php");

// istruzioni per tornare alla pagina di login se non c'� una sessione valida
////session_start();


$tipoutente=$_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente=="")
{
   header("location: ../login/login.php?suffisso=".$_SESSION['suffisso']);
   die;
}

$nome_file="Evacuazione (" . $_POST["gio"] . " - " . $_POST["meseanno"] . ") " . $_POST["denominazioneclasse"] . ".pdf";

//  Richiamare funzione di stampa
if($_GET["preview"] == 1) {
  stampa_evacuazione($nome_file, "I");
}
else {
  stampa_evacuazione($nome_file, "D");
}

function stampa_evacuazione($nomefile, $mode)
{
	@require("../php-ini".$_SESSION['suffisso'].".php");
	require_once("../lib/fpdf/fpdf.php");
  $schede=new FPDF();

  $schede->AddPage();
  if ($_SESSION['suffisso']!="") $suff=$_SESSION['suffisso']."/"; else $suff="";
  $schede->Image('../abc/'.$suff.'testata.jpg',NULL,NULL,190,43);

  $schede->SetFont('Times', 'B', 14);
  $posY = 60;
  $schede->setXY(10, $posY);
  $schede->MultiCell(190, 8, converti_utf8("MODULO DI EVACUAZIONE"), NULL, "C");
  $schede->SetFont('Times', '', 12);
  $posY += 10;
  $schede->setXY(10, $posY);
  $schede->Cell(30, 8, "CLASSE: ", 0, NULL, "C");
  $schede->Cell(160, 8, converti_utf8($_POST["denominazioneclasse"]), 0, NULL, "L");
  $posY += 10;
  $schede->setXY(10, $posY);
  $schede->Cell(190, 8, "Indicare aula o laboratorio occupati nel momento dell'evacuazione: ", 1, 1, "C");
  $schede->Cell(190, 8, converti_utf8($_POST["aula"]), 1, 1, "C");
  $posY += 20;
  $schede->setXY(10, $posY);
  $schede->Cell(90, 8, converti_utf8("N° studenti iscritti alla classe:"), 1, NULL, "C");
  $schede->Cell(25, 8, converti_utf8($_POST["numeroalunni"]), 1, NULL, "C");
  $posY += 8;
  $schede->setXY(10, $posY);
  $schede->Cell(90, 8, converti_utf8("N° studenti assenti:"), 1, NULL, "C");
  $schede->Cell(25, 8, converti_utf8($_POST["alunniassenti"]), 1, NULL, "C");
  $posY += 8;
  $schede->setXY(10, $posY);
  $schede->Cell(90, 8, converti_utf8("N° studenti presenti ma non in aula:"), 1, NULL, "C");
  $schede->Cell(25, 8, converti_utf8($_POST["alunninoninaula"]), 1, NULL, "C");
  $schede->Cell(25,8, "Luogo:", 1, NULL, "C");
  if($_POST["alunninoninaula"]>0) {
    $schede->Cell(50,8, converti_utf8($_POST["luogononinaula"]), 1, NULL, "C");
  }
  else {
    $schede->Cell(50,8, converti_utf8("-"), 1, NULL, "C");
  }
  $posY += 12;
  $schede->setXY(10, $posY);
  $schede->Cell(90, 8, converti_utf8("N° studenti evacuati:"), 1, NULL, "C");
  $schede->Cell(25, 8, converti_utf8($_POST["alunnievacuati"]), 1, NULL, "C");
  $posY += 12;
  $schede->setXY(10, $posY);
  $schede->Cell(90, 8, converti_utf8("N° altre persone presenti:"), 1, NULL, "C");
  $schede->Cell(25, 8, converti_utf8($_POST["numaltrepersone"]), 1, NULL, "C");
  if($_POST["numaltrepersone"]>0) {
    $posY += 12;
    $schede->setXY(10, $posY);
    $schede->Cell(190, 8, converti_utf8("Indicare altre persone presenti:"), 1, 1, "C");
    $schede->Cell(190, 8, converti_utf8($_POST["altrepersone"]), 1, NULL, "C");
    $posY+= 8;
  }
  $posY += 12;
  $schede->setXY(10, $posY);
  $schede->Cell(90, 8, converti_utf8("N° studenti dispersi:"), 1, NULL, "C");
  $schede->Cell(25, 8, converti_utf8($_POST["alunnidispersi"]), 1, NULL, "C");
  if($_POST["alunnidispersi"]>0){
    $posY += 12;
    $schede->setXY(10, $posY);
    $schede->Cell(190, 8, converti_utf8("Indicare nominativi degli alunni dispersi:"), 1, 1, "C");
    $alu_disp_tmp = "";
    for($n=1; $n<=$_POST["alunnidispersi"]; $n++){
      $alu_disp_tmp .= $_POST["disperso" . $n];
      if($n != $_POST["alunnidispersi"]){
        $alu_disp_tmp .= ", ";
      }
    }
    $schede->MultiCell(190, 8, converti_utf8($alu_disp_tmp), 1, "C");
    $posY+= 8;
  }
  $posY += 12;
  $schede->setXY(10, $posY);
  $schede->Cell(90, 8, converti_utf8("Studente apri fila:"), 1, NULL, "C");
  $schede->Cell(100, 8, converti_utf8($_POST["aprifila"]), 1, NULL, "C");
  $posY += 8;
  $schede->setXY(10, $posY);
  $schede->Cell(90, 8, converti_utf8("Studente chiudi fila:"), 1, NULL, "C");
  $schede->Cell(100, 8, converti_utf8($_POST["chiudifila"]), 1, NULL, "C");
  $posY += 8;
  $schede->setXY(10, $posY);
  $schede->Cell(90, 8, converti_utf8("Zona di raccolta:"), 1, NULL, "C");
  $schede->Cell(100, 8, converti_utf8($_POST["zonaraccolta"]), 1, NULL, "C");
  $posY += 8;
  $schede->setXY(10, $posY);
  $schede->Cell(90, 8, converti_utf8("Tipo di emergenza:"), 1, NULL, "C");
  $schede->Cell(100, 8, converti_utf8($_POST["tipoemergenza"]), 1, NULL, "C");

  $schede->setXY(10, 260);
  $schede->Cell(100, 16, converti_utf8($comune_scuola) . ", " . converti_utf8($_POST["dataformattata"]), NULL, NULL, "L");
  $schede->Cell(90, 8, "Il docente", NULL, 2, "C");
  $schede->Cell(90, 8, converti_utf8($_POST["insegnante"]), NULL, NULL, "C");

  $schede->Output($nomefile,$mode);

  }
