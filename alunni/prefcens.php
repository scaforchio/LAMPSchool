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

// istruzioni per tornare alla pagina di login

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "") {
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Modifica preferenze censimento";
stampa_head_new($titolo, "", $style, "MSA");
stampa_testata_new($titolo, "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);
if (!$con) {
    print("<h1> Connessione al server fallita </h1>");
    exit;
}

if($_SERVER['REQUEST_METHOD'] == "POST"){
    $conferma = trim($con->real_escape_string($_POST['com'].$_POST['acc'].$_POST['ass'].$_POST['col']));
    if(strlen($conferma) == 4){
        $aaa = $_POST['idalu'];
        eseguiQuery($con, "UPDATE tbl_alunni SET censito = '$conferma' WHERE idalunno = '$aaa';");
    }
}

$idalunno = mysqli_real_escape_string($con, $_GET["idalu"]);

$query = eseguiQuery($con, "SELECT * FROM tbl_alunni WHERE idalunno = $idalunno;");
$alunno = mysqli_fetch_assoc($query);

$opzioni = array();

for ($i=0; $i < 17; $i++) { 
    $opzioni[$i] = "";
}

if($alunno['censito'] != '0' && $alunno['censito'] != '1'){
    $prima_lettera = $alunno['censito'][0];
    $seconda_lettera = $alunno['censito'][1];
    $terza_lettera = $alunno['censito'][2];
    $quarta_lettera = $alunno['censito'][3];

    switch ($prima_lettera) {
        case 'N':
            $opzioni[1] = "checked";
            break;
        
        case 'P':
            $opzioni[2] = "checked";
            break;

        case 'M':
            $opzioni[3] = "checked";
            break;
            
        case 'E':
            $opzioni[4] = "checked";
            break;
    }

    switch ($seconda_lettera) {
        case 'N':
            $opzioni[5] = "checked";
            break;
        
        case 'P':
            $opzioni[6] = "checked";
            break;

        case 'M':
            $opzioni[7] = "checked";
            break;
            
        case 'E':
            $opzioni[8] = "checked";
            break;
    }

    switch ($terza_lettera) {
        case 'N':
            $opzioni[9] = "checked";
            break;
        
        case 'P':
            $opzioni[10] = "checked";
            break;

        case 'M':
            $opzioni[11] = "checked";
            break;
            
        case 'E':
            $opzioni[12] = "checked";
            break;
    }

    switch ($quarta_lettera) {
        case 'N':
            $opzioni[13] = "checked";
            break;
        
        case 'P':
            $opzioni[14] = "checked";
            break;

        case 'M':
            $opzioni[15] = "checked";
            break;
            
        case 'E':
            $opzioni[16] = "checked";
            break;
    }
}

print('<center>');

print('<b>Alunno: </b> ' . $alunno['nome'] . " " . $alunno['cognome']);
print("<br><br>");

?>

<form method="post">
    <table class="table table-bordered">
        <tr>
            <td>
                Comunicazione dei dati sull'andamento scolastido e disciplinare, sulle assenze, <br>
                sulla partecipazione ad attività e progetti e su altri dati inerenti le attività <br>
                scolastiche ai sogtgetti esercenti la potestà genitoriale mediante comunicazioni <br>
                verbali, scritte e/o elettroniche.
            </td>
            <td>
                <div style="display: flex;flex-direction: column;align-items: center;justify-content: center; height:100px;">
                    <center>
                        <input type="radio" class="btn-check" name="com" value='N' id='option1' <?php echo $opzioni[1] ?> autocomplete="off">
                        <label class="btn" for="option1">Nessuno</label>

                        <input type="radio" class="btn-check" name="com" value='P' id='option2' <?php echo $opzioni[2] ?> autocomplete="off">
                        <label class="btn" for="option2">Padre</label>

                        <input type="radio" class="btn-check" name="com" value='M' id='option3' <?php echo $opzioni[3] ?> autocomplete="off">
                        <label class="btn" for="option3">Madre</label>

                        <input type="radio" class="btn-check" name="com" value='E' id='option4' <?php echo $opzioni[4] ?> autocomplete="off">
                        <label class="btn" for="option4">Entrambi</label>
                    </center>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                Accesso dei soggetti esercenti la potestà genitoriale al Registro elettronico, <br>
                mediante consegna di credenziali o anche mediante mantenimento delle credenziali attive. <br>
            </td>
            <td>
                <div style="display: flex;flex-direction: column;align-items: center;justify-content: center; height:100px;">
                    <center>
                        <input type="radio" class="btn-check" name="acc" value='N' id='option5' <?php echo $opzioni[5] ?> autocomplete="off">
                        <label class="btn" for="option5">Nessuno</label>

                        <input type="radio" class="btn-check" name="acc" value='P' id='option6' <?php echo $opzioni[6] ?> autocomplete="off">
                        <label class="btn" for="option6">Padre</label>

                        <input type="radio" class="btn-check" name="acc" value='M' id='option7' <?php echo $opzioni[7] ?> autocomplete="off">
                        <label class="btn" for="option7">Madre</label>

                        <input type="radio" class="btn-check" name="acc" value='E' id='option8' <?php echo $opzioni[8] ?> autocomplete="off">
                        <label class="btn" for="option8">Entrambi</label>
                    </center>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                Comunicazione, anche mediante invio di SMS, di assenze, accessi ritardati o <br>
                ingressi posticipati e di ogni altra informazione relativa alla vita scolastica. <br>
            </td>
            <td>
                <div style="display: flex;flex-direction: column;align-items: center;justify-content: center; height:100px;">
                    <center>
                        <input type="radio" class="btn-check" name="ass" value='N' id='option9' <?php echo $opzioni[9] ?>  autocomplete="off">
                        <label class="btn" for="option9">Nessuno</label>

                        <input type="radio" class="btn-check" name="ass" value='P' id='option10' <?php echo $opzioni[10] ?> autocomplete="off">
                        <label class="btn" for="option10">Padre</label>

                        <input type="radio" class="btn-check" name="ass" value='M' id='option11' <?php echo $opzioni[11] ?> autocomplete="off">
                        <label class="btn" for="option11">Madre</label>

                        <input type="radio" class="btn-check" name="ass" value='E' id='option12' <?php echo $opzioni[12] ?> autocomplete="off">
                        <label class="btn" for="option12">Entrambi</label>
                    </center>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                Partecipazione ai colloqui finalizzati a comunicare l'andamento didattico-disciplinare <br>
                dell'alunno, sia in presenza che a distanza, dei genitori esercenti la responsabilità <br>
                genitoriale, anche in assenza dell'alunno maggiorenne.
            </td>
            <td>
                <div style="display: flex;flex-direction: column;align-items: center;justify-content: center; height:100px;">
                    <center>
                        <input type="radio" class="btn-check" name="col" value='N' id='option13' <?php echo $opzioni[13] ?> autocomplete="off">
                        <label class="btn" for="option13">Nessuno</label>

                        <input type="radio" class="btn-check" name="col" value='P' id='option14' <?php echo $opzioni[14] ?> autocomplete="off">
                        <label class="btn" for="option14">Padre</label>

                        <input type="radio" class="btn-check" name="col" value='M' id='option15' <?php echo $opzioni[15] ?> autocomplete="off">
                        <label class="btn" for="option15">Madre</label>

                        <input type="radio" class="btn-check" name="col" value='E' id='option16' <?php echo $opzioni[16] ?> autocomplete="off">
                        <label class="btn" for="option16">Entrambi</label>
                    </center>
                </div>
            </td>
        </tr>
    </table>
    <input class="btn btn-secondary" type="submit" value="Salva">
    <button class="btn btn-secondary" onclick="window.close()">Chiudi</button>
<?php

if(isset($_POST['idalu'])){
    print('<input type="hidden" name="idalu" value="' . $_POST['idalu'] . '">');
}else{
    print('<input type="hidden" name="idalu" value="' . $_GET['idalu'] . '">');
}

print('</form></center>');

stampa_piede_new('');
