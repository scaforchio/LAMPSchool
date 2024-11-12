<?php

require_once '../lib/req_apertura_sessione.php';

/*
  Copyright (C) 2024 Vittorio Lo Mele
  Copyright (C) 2024 Michele Sacco
  Questo programma è un software libero; potete redistribuirlo e/o modificarlo secondo i termini della
  GNU Affero General Public License come pubblicata
  dalla Free Software Foundation; sia la versione 3,
  sia (a vostra scelta) ogni versione successiva.

  Questo programma è distribuito nella speranza che sia utile
  ma SENZA ALCUNA GARANZIA; senza anche l'implicita garanzia di
  POTER ESSERE VENDUTO o di IDONEITA' A UN PROPOSITO PARTICOLARE.
  Vedere la GNU Affero General Public License per ulteriori dettagli.

  Dovreste aver ricevuto una copia della GNU Affero General Public License
  in questo programma; se non l'avete ricevuta, vedete http://www.gnu.org/licenses/
 */

@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

// istruzioni per tornare alla pagina di login

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "") {
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Stampa Report";

stampa_head_new($titolo, "", "", "S");
stampa_testata_new("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='./sondaggi.php'>Gestione Sondaggi Alunni</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);
if (!$con) {
    print("<h1> Connessione al server fallita </h1>");
    exit;
}
// ID sondaggio - GET
$idsond = stringa_get_html('id');
// Estrazione risposte consentite
$query = "SELECT * FROM tbl_sondaggi WHERE idsondaggio = '$idsond'";
$tempris = mysqli_fetch_array(eseguiQuery($con, $query));
$risposte = json_decode($tempris['opzioni']);

// Pickup di tutti i dati relativi al sondaggio
$query = "SELECT * FROM tbl_rispostesondaggi WHERE idsondaggio = '$idsond'";
$ris = eseguiQuery($con, $query);

// Esegui il count di tutte le risposte non registrate (-1)
$query = "SELECT COUNT(idopzione) as risposte FROM tbl_rispostesondaggi WHERE idsondaggio = $idsond and idopzione = -1;";
$norispcount = mysqli_fetch_assoc(eseguiQuery($con, $query));
// Check di quante risposte ci sono e relativo count
$lun = (count($risposte))-1;
while($lun != -1){
    $rispcount[$lun] = mysqli_fetch_assoc(eseguiQuery($con, "SELECT COUNT(idopzione) as risposte FROM tbl_rispostesondaggi WHERE idsondaggio = $idsond and idopzione = $lun;"));
    $lun = $lun - 1;
}
?>
<center>
    <b class="text-center">Sondaggio: <?= strtoupper($tempris['oggetto']) ?></b>
</center> <br>
<p class="text-center">Descrizione: <br> <?= $tempris['descrizione'] ?></p>
<p class="text-center"> <b>Risposte Totali</b> <br>
<?php
for($i=0;$i<count($rispcount);$i++){
    print("<b>" .$risposte[$i][1] ."</b>: " .$rispcount[$i]["risposte"] ." ");
}
?>
</p>
<div style='margin-left: 10px; margin-right: 10px;'>
    <table border=1 align=center class='table table-bordered'> 
        <thead style='font-weight: bold;'> 
            <tr class='prima'> 
                <td data-priority='1' align=center>Nome e Cognome</td>  
                <td data-priority='2' align=center>Risultato</td>
            </tr>
        </thead>
    <tbody>
<?php

// Stampa risultati del sondaggio - divisione per classe
$classe = "";
while($sond = mysqli_fetch_array($ris)){
    // If classe varia dal precedente ciclo, crea una nuova tabella e scrive classe in alto
    $cl = decodifica_classe(estrai_classe_alunno($sond['idutente'], $con), $con);
    if($cl != $classe){
        $classe = $cl;
        ?> <tr class="prima"><td style='background-color: var(--bs-secondary-bg-subtle)' colspan=3 align=center><b><?= $classe ?></b></td></tr> <?php
        $datial = estrai_dati_alunno_rid($sond['idutente'], $con);
        if($sond['idopzione'] == -1){
            $risp = "NESSUNA RISPOSTA";
            $col = "#ff0000";
        }elseif($sond['idopzione']> -1){
            $risp = strtoupper($risposte[$sond['idopzione']][1]) . " (" . $sond['ts'] . ")";
            $col = "000";
            
        }
    }else{
        // Stampa nome, cognome, risultato dell'alunno
        $datial = estrai_dati_alunno_rid($sond['idutente'], $con);
        if($sond['idopzione'] == -1){
            $risp = "NESSUNA RISPOSTA";
            $col = "#ff0000";
        }elseif($sond['idopzione']> -1){
            $risp = strtoupper($risposte[$sond['idopzione']][1]) . " (" . $sond['ts'] . ")";
            $col = "000";
        }
    }
    ?>
        <tr>
            <td align=center><?= $datial ?></td> 
            <td align=center><p style="color:<?= $col ?>;"><?= $risp ?></p></td>
        </tr>
    <?php
}
?>
    </tbody>
    </table>
    </div>  
    <p class="text-center">
        <button class="btn btn-outline-secondary" onclick="window.print()"><i class="bi bi-printer"></i> Stampa Report</button>
    </p>
<?php
stampa_piede_new("");
mysqli_close($con);
