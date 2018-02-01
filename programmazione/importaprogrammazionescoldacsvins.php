<?php session_start();

/*
Copyright (C) 2015 Pietro Tamburrano
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
@include '../php-ini' . $_SESSION['suffisso'] . '.php';
@include '../lib/funzioni.php';
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));
// istruzioni per tornare alla pagina di login
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione

$idmateria = stringa_html('idmateria');
$anno = stringa_html('anno');
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}


$script = "<script type='text/javascript'>
         <!--
               var stile = 'top=10, left=10, width=1024, height=400, status=no, menubar=no, toolbar=no, scrollbars=yes';
               function Popup(apri) 
               {
                  window.open(apri, '', stile);
               }
         //-->
         </script>";
stampa_head("Carica programmazione da CSV", "", $script);

stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - Carica programmazione scolastica da CSV", "", "$nome_scuola", "$comune_scuola");

$arrpar = array();


// COSTRUISCO LA STRINGA DEI PARAMETRI DI IMPORTAZIONE
$sep = stringa_html('separatore'); // separatore dei dati per un file csv

switch ($sep)
{
    case ';':
        $arrpar[] = '1';
        break;
    case ',':
        $arrpar[] = '2';
        break;
    case '|':
        $arrpar[] = '3';
        break;
    case '/':
        $arrpar[] = '4';
        break;
    case 't':
        $arrpar[] = '5';
        break;
}
$deli = stringa_html('deli');

switch ($deli)
{
    case '':
        $arrpar[] = '0';
        break;
    case 'v':
        $arrpar[] = '1';
        break;
    case 'a':
        $arrpar[] = '2';
        break;
}
//  print $arrpar[1];

if ($sep == 't') $sep = "\t";
$del = $deli == '' ? '"' : ($deli == 'a' ? "'" : '"');  // delimitatore di testo
//  print $del;

if ($anno != "" && $idmateria != "")
{

    if (is_stringa_html('sovrascrittura'))
    {
        $sovrascrittura = true;
    }
    else
    {
        $sovrascrittura = false;
    }
    $ok = true;
    if ($sovrascrittura)
    {

        $dir = "$cartellabuffer"; // la directory nella quale verrà salvato il file

        $nomefile = $_FILES['filenomi']['name'];
        if (is_uploaded_file($_FILES['filenomi']['tmp_name']))
        {
            move_uploaded_file($_FILES['filenomi']['tmp_name'], "$dir/$nomefile") or die("Impossibile spostare il file");
        }
        else
        {
            die("Errore nell'upload del file." . $_FILES['filenomi']['error']);
        }

        // COPIA DATI NEL DB
        $filecsv = "$dir/$nomefile";

        $result = array();
        $handle = fopen($filecsv, "r") or die("Impossibile aprire il file in lettura!");

        if ($handle === false)
        {
            return $result;
        }
        // APERTURA FILE CSV PER MEMORIZZAZIONE PROPOSTE


        $numero_competenze = 0;
        $numero_abilita = 0;
        $numero_conoscenze = 0;

        $riga_tmp = fgetcsv($handle, 1000, $sep, $del);

        if ($riga_tmp[0] == 'tipo' && $riga_tmp[1] == 'sintesi' && $riga_tmp[2] == 'descrizione' && $riga_tmp[3] == 'obmin')
        {

            $numordcomp = 1;
            $numordabil = 1;
            $numordcono = 1;
            $idcompetenza = 0;
            $query = "delete from tbl_competscol where anno=$anno and idmateria=$idmateria";
            mysqli_query($con, inspref($query)) or die("Errore:" . inspref($query, false));
            while (($riga_tmp = fgetcsv($handle, 1000, $sep, $del)) !== FALSE)
            {
                if ($riga_tmp[0] == 'COMP')
                {

                    $numordabil = 1;
                    $numordcono = 1;
                    $query = "insert into tbl_competscol(anno,idmateria,numeroordine,sintcomp,competenza) VALUES
                            ($anno,$idmateria,$numordcomp,'$riga_tmp[1]','$riga_tmp[2]')";
                    mysqli_query($con, inspref($query)) or die("Errore:" . inspref($query, false));
                    $idcompetenza = mysqli_insert_id($con);

                    $numordcomp++;

                }
                else
                {
                    if ($riga_tmp[0] == 'ABIL')
                    {
                        if ($riga_tmp[3] == 1)
                        {
                            $obmin = 1;
                        }
                        else
                        {
                            $obmin = 0;
                        }

                        // INSERISCO ABILITA' CON ID DELLA COMPETENZA SE C'E' IMPOSTO LO STATO AD "A"
                        $query = "insert into tbl_abilscol(idcompetenza,numeroordine,sintabilcono,abilcono,obminimi,abil_cono) VALUES
                            ($idcompetenza,$numordabil,'$riga_tmp[1]','$riga_tmp[2]',$obmin,'A')";
                        mysqli_query($con, inspref($query)) or die("Errore:" . inspref($query, false));
                        $numordabil++;
                    }
                    else
                    {
                        if ($riga_tmp[0] == 'CONO')
                        {
                            if ($riga_tmp[3] == 1)
                            {
                                $obmin = 1;
                            }
                            else
                            {
                                $obmin = 0;
                            }
                            // INSERISCO ABILITA' CON ID DELLA COMPETENZA SE C'E' IMPOSTO LO STATO AD "A"
                            $query = "insert into tbl_abilscol(idcompetenza,numeroordine,sintabilcono,abilcono,obminimi,abil_cono) VALUES
                            ($idcompetenza,$numordcono,'$riga_tmp[1]','$riga_tmp[2]',$obmin,'C')";
                            mysqli_query($con, inspref($query)) or die("Errore:" . inspref($query, false));
                            $numordcono++;

                        }
                    }
                }


            }
        }
        else
        {
            print "<center><br><b>Struttura del file errata!</b>";
        }

    }
    else
    {
        // TTTT SE C'E' GIA' UNA PROGRAMMAZIONE MESSAGGIO
        $query = "select * from tbl_competscol where anno=$anno and idmateria=$idmateria";
        $ris = mysqli_query($con, inspref($query)) or die ("Errore: " . inspref($query));
        if (mysqli_num_rows($ris) > 0)
        {
            print "<center><br><b>C'è già una programmazione per l'anno e la materia. Confermare sovrascrittura in fase di selezione!</b>";
        }
    }

}

mysqli_close($con);
stampa_piede("");


