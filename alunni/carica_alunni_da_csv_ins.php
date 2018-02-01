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

// istruzioni per tornare alla pagina di login
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione

if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

//TODO: Verificare funzionamento dopo eliminazione del 30/05/2015 della gestione degli errori

$script = "<script type='text/javascript'>
         <!--
               var stile = 'top=10, left=10, width=1024, height=400, status=no, menubar=no, toolbar=no, scrollbars=yes';
               function Popup(apri) 
               {
                  window.open(apri, '', stile);
               }
         //-->
         </script>";
stampa_head("Carica Archivio Alunni da CSV", "", $script, "MASP");

stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - Carica Archivio Alunni da CSV", "", "$nome_scuola", "$comune_scuola");

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


if (is_stringa_html('sovrascrittura'))
{
    $sovrascrittura = true;
}
else
{
    $sovrascrittura = false;
}


if (is_stringa_html('intestazione'))
{
    $arrpar[] = '1';
}
else
{
    $arrpar[] = '0';
}
$arrpar[] = stringa_html('poscogn');
$arrpar[] = stringa_html('posnome');
$arrpar[] = stringa_html('posdata');
$arrpar[] = stringa_html('possesso');
$arrpar[] = stringa_html('poscodf');
$arrpar[] = stringa_html('poscodsidi');
$arrpar[] = stringa_html('poscomnasc');
$arrpar[] = stringa_html('posindi');
$arrpar[] = stringa_html('poscomres');
$arrpar[] = stringa_html('postele');
$arrpar[] = stringa_html('poscell');
$arrpar[] = stringa_html('posemail');

$stringaparametri = implode("!", $arrpar);
// print "Stringa ".$stringaparametri;
// FINE COSTRUZIONE STRINGA DI IMPORTAZIONE


// se i dati in ingresso sono stati inseriti correttamente:
if (stringa_html('upload') == "CARICA" && isset($_FILES['filenomi']['tmp_name']))
//    && (substr($_FILES['filenomi']['name'],-4) == ".txt"
//        || substr($_FILES['filenomi']['name'],-4) == ".csv"))

{

    $posizioni = array();

    $posizioni[] = stringa_html('poscogn');
    $posizioni[] = stringa_html('posnome');
    $posizioni[] = stringa_html('posdata');
    $posizioni[] = stringa_html('poscodf');
    if (stringa_html('possesso') != 99) $posizioni[] = stringa_html('possesso');
    if (stringa_html('poscodsidi') != 99) $posizioni[] = stringa_html('poscodsidi');
    if (stringa_html('poscomnasc') != 99) $posizioni[] = stringa_html('poscomnasc');
    if (stringa_html('posindi') != 99) $posizioni[] = stringa_html('posindi');
    if (stringa_html('poscomres') != 99) $posizioni[] = stringa_html('poscomres');
    if (stringa_html('postele') != 99) $posizioni[] = stringa_html('postele');
    if (stringa_html('poscell') != 99) $posizioni[] = stringa_html('poscell');
    if (stringa_html('posemail') != 99) $posizioni[] = stringa_html('posemail');

    $max = 0;
    for ($i = 0; $i < count($posizioni); $i++)
        if ($posizioni[$i] > $max)
        {
            $max = $posizioni[$i];
        }

    if (duplicati($posizioni))
    {
        print"<center>
			Ci sono posizioni duplicate nella sequenza! Verificare le posizioni dei campi.<br/><br/>
			<form action='carica_alunni_da_csv.php' method='POST'>
                 <input type='hidden' name='par' value='$stringaparametri'> 
                 <input type='submit' value=' << Indietro '>
            </form>";
        exit;
    }

    $poscogn = stringa_html('poscogn') - 1;
    $posnome = stringa_html('posnome') - 1;
    $posdata = stringa_html('posdata') - 1;
    $poscodf = stringa_html('poscodf') - 1;
    $possesso = stringa_html('possesso') - 1;
    $poscodsidi = stringa_html('poscodsidi') - 1;
    $poscomnasc = stringa_html('poscomnasc') - 1;
    $posindi = stringa_html('posindi') - 1;
    $poscomres = stringa_html('poscomres') - 1;
    $postele = stringa_html('postele') - 1;
    $poscell = stringa_html('poscell') - 1;
    $posemail = stringa_html('posemail') - 1;


    //connessione al server
    $con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);

    if (!$con)
    {
        print("<h1> Connessione al server fallita </h1>");
        exit;
    }
    else
    {
        $DB = true;

        if (!$DB)
        {
            print "NOME DATABASE:" . $db_nome;
            print "<br/><h1> Connessione al database fallita </h1>";
            exit;
        };

        $dir = "$cartellabuffer"; // la directory nella quale verrà salvato il file

        $nomefile = $_FILES['filenomi']['name'];
        if (is_uploaded_file($_FILES['filenomi']['tmp_name']))
        {
            move_uploaded_file($_FILES['filenomi']['tmp_name'], "$dir/$nomefile")
            or die("Impossibile spostare il file");
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


        $nf = session_id() . ".csv";
        $nomefile = "$cartellabuffer/" . $nf;
        $fp = fopen($nomefile, 'w');

        print "
              <table border='1' align='center' cellspacing='0'>
              <tr>
              <td align='center'><b>Num.</b></td>
              <td align='center'><b>Codice Fiscale</b></td>
              <td align='center'><b>Cognome</b></td>
              <td align='center'><b>Nome</b></td>
              <td align='center'><b>Data di Nascita</b></td>
              <td align='center'><b>Utente</b></td>
              <td align='center'><b>Password</b></td>
              </tr>
               ";
        $numero_di_alunni = 0;
        $numero_alunni_inseriti = 0;
        if (is_stringa_html('intestazione'))
        {
            $riga_tmp = fgetcsv($handle, 1000, $sep, $del);
        }
        while (($riga_tmp = fgetcsv($handle, 1000, $sep, $del)) !== FALSE)
        {
            print "<tr>";
            $numero_colonne = count($riga_tmp);
            if ($max > $numero_colonne)
            {
                print"<center><p>Numero di colonne nel file minore della massima posizione indicata!</p></center>";
                break;
            }
            $err = 0;
            $numero_di_alunni++;
            print "<td align=center>$numero_di_alunni</td>";
            if (Verifica_CodiceFiscale($riga_tmp[$poscodf]))
            {
                print "<td align=center>" . $riga_tmp[$poscodf] . "</td>";
            }
            else
            {
                print "<td align=center><font color='red'>" . $riga_tmp[$poscodf] . "</font></td>";
                $err = 1;
            }
            print "<td align=center>" . $riga_tmp[$poscogn] . "</td>";
            print "<td align=center>" . $riga_tmp[$posnome] . "</td>";
            if (Controllodata($riga_tmp[$posdata]))
            {
                print "<td align=center>" . $riga_tmp[$posdata] . "</td>";
            }
            else
            {
                print "<td align=center><font color='red'>" . $riga_tmp[$posdata] . "</font></td>";
                $err = 1;
            }


            $cognome = elimina_apici($riga_tmp[$poscogn]);
            $nome = elimina_apici($riga_tmp[$posnome]);
            // print ($riga_tmp[$posdata]);
            $dataNascita = data_to_db($riga_tmp[$posdata]);
            $codfiscale = $riga_tmp[$poscodf];
            $sesso = $possesso != 98 ? $riga_tmp[$possesso] : '';
            $codmeccanografico = $poscodsidi != 98 ? $riga_tmp[$poscodsidi] : '';
            $comuneNascita = $poscomnasc != 98 ? estraicodcomune($riga_tmp[$poscomnasc], $con) : estraicodcomune("0000", $con);
            $indirizzo = $posindi != 98 ? elimina_apici($riga_tmp[$posindi]) : '';
            $comuneResidenza = $poscomres != 98 ? estraicodcomune($riga_tmp[$poscomres], $con) : estraicodcomune("0000", $con);
            $telefono = $postele != 98 ? $riga_tmp[$postele] : '';
            $cellulare = $poscell != 98 ? $riga_tmp[$poscell] : '';
            $email = $posemail != 98 ? $riga_tmp[$posemail] : '';

            if ($err != 1)
            {
                // ALUNNO
                // PRIMA SI CANCELLA L'ALUNNO, IL TUTORE E L'UTENTE CON LO STESSO IDTUTORE O IDUTENTE
                $sqlt = "select idutente,idtutore from tbl_alunni where codfiscale='$codfiscale'";
                $res = mysqli_query($con, inspref($sqlt));

                if ($res)
                {

                    if (mysqli_num_rows($res) > 0)
                    { // Se esiste si cancella nelle 3 tabelle
                        $colonna = mysqli_fetch_array($res);
                        $idutente = $colonna['idtutore']; // prima si prova con idtutore

                        if ($idutente > 0)
                        { // IMPORTANTE per non eliminare l'utente adminlamp
                            $where = "where idtutore=$idutente";
                        }
                        else
                        { // poi con idutente
                            $idutente = $colonna['idutente'];
                            $where = "where idutente=$idutente";
                        }

                        if ($idutente > 0)
                        { // IMPORTANTE per non eliminare l'utente adminlamp
                            $sqlt = "delete from tbl_alunni $where";
                            mysqli_query($con, inspref($sqlt));
                          //  $sqlt = "delete from tbl_tutori $where";
                          //  mysqli_query($con, inspref($sqlt));
                           // $sqlt = "delete from tbl_utenti where idutente=$idutente";
                            $sqlt = "delete from tbl_utenti $where";
                            mysqli_query($con, inspref($sqlt));
                        }
                        mysqli_free_result($res);
                    }
                }

                // POI SI INSERISCE IL RECORD NELLA TABELLA tbl_alunni;
                $res = "insert into tbl_alunni(
                            cognome,nome,datanascita,codfiscale,idcomnasc,idcomres,sesso,codmeccanografico,indirizzo,telefono,telcel,email)
                            VALUES ('$cognome','$nome','$dataNascita','$codfiscale','$comuneNascita','$comuneResidenza','$sesso','$codmeccanografico','$indirizzo','$telefono','$cellulare','$email')";

                //print "<br/>" . inspref($res);
                mysqli_query($con, inspref($res)) or die("Errore in iserimento alunno" . inspref($query, false));

                $idalunnoinserito = mysqli_insert_id($con);


                // TUTORE
                // PRIMA SI CANCELLA IL TUTORE ESISTENTE
               // $sqlt = "delete from tbl_tutori where idtutore=$idalunnoinserito";
               // mysqli_query($con, inspref($sqlt)) or die("Errore in iserimento alunno" . inspref($sqlt, false));


                // POI SI INSERISCE IL RECORD NELLA TABELLA tbl_tutori;
               // $sqlt = "insert into tbl_tutori(idtutore,cognome,nome,idalunno,idutente) values ('$idalunnoinserito','$cognome','$nome','$idalunnoinserito','$idalunnoinserito')";
               // mysqli_query($con, inspref($sqlt)) or die("Errore in iserimento alunno" . inspref($sqlt, false));
                // UTENTE
                // PRIMA SI CANCELLA L'UTENTE ESISTENTE
                $sqlt = "delete from tbl_utenti where idutente=$idalunnoinserito";
                mysqli_query($con, inspref($sqlt)) or die("Errore in iserimento alunno" . inspref($sqlt, false));

                // POI SI INSERISCE IL RECORD NELLA TABELLA tbl_utenti;
                $utente = "gen" . $idalunnoinserito;
                $password = creapassword();
                print "<td align=center>$utente</td>";
                print "<td align=center>$password</td>";
                $sqlt = "insert into tbl_utenti(idutente,userid,password,tipo) values ('$idalunnoinserito','$utente',md5('" . md5($password) . "'),'T')";
                mysqli_query($con, inspref($sqlt)) or die("Errore in iserimento alunno" . inspref($sqlt, false));

                // AGGIORNAMENTO DELL'ALUNNO CON L'ID DEL TUTORE E CON L'ID DELL'UTENTE
                $sqlt = "update tbl_alunni set idtutore=$idalunnoinserito,idutente=$idalunnoinserito where idalunno=$idalunnoinserito";
                mysqli_query($con, inspref($sqlt)) or die("Errore in iserimento alunno" . inspref($sqlt, false));

                // Inseriamo nel file csv

                fputcsv($fp, array($riga_tmp[$poscodf], $riga_tmp[$poscogn], $riga_tmp[$posnome], $riga_tmp[$posdata], $utente, $password), ";");
                print "</tr>";

                $numero_alunni_inseriti++;
            }
        }


        fclose($handle);
        fclose($fp);

        print ("</table>");
        print ("<br/><center><a href='$cartellabuffer/$nf'><img src='../immagini/csv.png'></a></center>");
    }
    printf("<p align='center'> Numero di alunni inseriti: $numero_alunni_inseriti su $numero_di_alunni");
}
else
{
    print '<h1> File non valido </h1>';
}
print ("
<div style='text-align: center'>
    <br/>
    <form action='carica_alunni_da_csv.php' method='POST'>
        <input type='hidden' name='par' value='$stringaparametri'>
        <input type='submit' value=' << Indietro '>
    </form>
</div>
");

mysqli_close($con);
stampa_piede("");


