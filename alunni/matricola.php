<?php

require_once '../lib/req_apertura_sessione.php';

/*
  Copyright (C) 2023 Vittorio Lo Mele
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

//  istruzioni per tornare alla pagina di login se non c'è una sessione valida

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Matricola";
$script = "";
stampa_head_new($titolo, "", $script, "L");
stampa_testata_new("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);

$idalunno = $_SESSION['idstudente'];

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));

$query = "select * from tbl_alunni where idalunno=$idalunno";
$ris = eseguiQuery($con, $query)->fetch_assoc();

$idc = $ris['idclasse'];

$queryclasse = "select * from tbl_classi where idclasse=$idc";
$risc = eseguiQuery($con, $queryclasse)->fetch_assoc();

$mat = strtoupper($_SESSION["suffisso"]).$idalunno;

?>

<center>
    <div style="max-width: 350px;">
        <div>
            <img alt='barcode' class="barcode" src="../lib/genbarcode.php?data=<?php echo $mat ?>"/>
        </div>
        <div style="margin-top: 10px;"><span style="font-size: 23px;"><?php echo $mat ?></span></div>
        <div style="margin-top: 10px;">
            <table>
                <tr>
                    <td>
                        <i class="bi bi-person-lines-fill"></i> 
                        <span style="padding-right: 10px;">Cognome e Nome:</span>
                    </td>
                    <td><b><?php echo $ris["cognome"] . " " . $ris["nome"] ?></b></td>
                </tr>
                <tr>
                    <td>
                        <i class="bi bi-calendar-event"></i> 
                        <span>Data di nascita: </span>
                    </td>
                    <td><b> <?php echo data_italiana($ris["datanascita"]) ?> </b></td>
                </tr>
                <tr>
                    <td>
                        <i class="bi bi-backpack"></i> 
                        <span>Classe: </span>
                    </td>
                    <td><b><?php echo $risc["anno"] . " " . $risc["sezione"] . " " . $risc["specializzazione"] ?></b></td>
                </tr>
            </table>
        </div>
    </div>
</center>

<?php

mysqli_close($con);
stampa_piede_new("");
