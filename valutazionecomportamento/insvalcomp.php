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


@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

// istruzioni per tornare alla pagina di login se non c'� una sessione valida
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Inserimento voto per obiettivi";
$script = "";
stampa_head($titolo,"",$script,"SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");


$gio = stringa_html('gio');
$mese = stringa_html('mese');
$anno = stringa_html('anno');

$idmateria = stringa_html('materia');
$iddocente = stringa_html('iddocente');
$data = $anno . "/" . $mese . "/" . $gio;
$idclasse = stringa_html('cl');
$idgruppo = stringa_html('idgruppo');
$cattedra = stringa_html('cattedra');

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));

if ($idgruppo == "")
{
    $query = "SELECT idalunno,cognome,nome FROM tbl_alunni WHERE idclasse=" . $idclasse;
    $ris = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
}
else
{
    $query = "select tbl_gruppialunni.idalunno as idalunno,cognome,nome from tbl_gruppialunni,tbl_alunni
            where tbl_gruppialunni.idalunno=tbl_alunni.idalunno
            and idgruppo=$idgruppo";
}

$ris = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
while ($id = mysqli_fetch_array($ris))            //    <-----------  ttttttt
{

    $esistenti = false;
    $presentevoto = false;
    $idal = $id['idalunno'];
    $query = "select idvalcomp,voto from tbl_valutazionicomp where idalunno=" . $idal . " and data='$data' and idmateria='$idmateria' and iddocente='$iddocente'";
    $ris2 = mysqli_query($con, inspref($query)) or die (mysqli_error);
    if (mysqli_num_rows($ris2) > 0)
    {

        // Si preleva l'idvalutazione e si cancellano le valutazioni singole
        $val = mysqli_fetch_array($ris2);
        $presentevoto = true;

        $idvalcomp = $val['idvalcomp'];
        $querycanc = "delete from tbl_valutazioniobcomp where idvalcomp=$idvalcomp";
        $ris3 = mysqli_query($con, inspref($querycanc)) or die (mysqli_error);
        $numcancellate = mysqli_affected_rows($con);
        if ($numcancellate > 0)
        {
            $esistenti = true;
        }


    }
    else
    {
        // Si inserisce il nuovo voto nella tabella tbl_valutazionicomp con valore di voto=0
        // tale valore verrà valorizzato dopo aver inserito i voti delle singole abilità e conoscenze
        // con il voto medio risultante
        $query = "insert into tbl_valutazionicomp(idalunno,idmateria,iddocente,idclasse,data,giudizio)
          values('$idal','$idmateria','$iddocente','" . estrai_classe_alunno_data($idal, $data, $con) . "','$data','')";
        $ris2 = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
        $idvalcomp = mysqli_insert_id($con);
    }

    // Si procede con l'inserimento di tutti i voti inseriti

    // $idmateria = estrai_id_materia($cattedra, $con);
    // $idclasse = estrai_id_classe($cattedra, $con);
    $numvoti=0;
    $totvoti=0;
    $query = "SELECT idsubob FROM tbl_compsubob";
    $risab = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
    while ($nomab = mysqli_fetch_array($risab))
    {
        $idsubob = $nomab['idsubob'];
        $va = "voto" . $idal . "_" . $idsubob;
        $votocomp = is_stringa_html($va) ? stringa_html($va) : 99;
        if ($votocomp != 99)
        {
            $numvoti++;
            $totvoti = $totvoti + $votocomp;
            $query = "insert into tbl_valutazioniobcomp(voto,idvalcomp,idsubob)
                           values('$votocomp','$idvalcomp','$idsubob')";

            $risins = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
        }
    }


    if ($numvoti != 0)
    {
        /*  if ($presentevoto & !$esistenti)
          {
              print "<br><center><font size=4 color='red'>Valutazione non legata alle competenze già presente per " . $id['cognome'] . " " . $id['nome'] . "</font></center>";
              mysqli_query($con, inspref("delete from tbl_valutazioniabilcono where idvalint=$idvalcomp"));
          }
          else
          {*/
        $votomedio = round($totvoti * 4 / $numvoti) / 4;
        $query = "update tbl_valutazionicomp set voto=$votomedio where idvalcomp=$idvalcomp";
        $risupd = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
        echo "
					  <center>
					  <font size=4><br>
					  Il voto medio risultante per l'alunno " . $id['cognome'] . " " . $id['nome'] . " è: <b>" . dec_to_mod($votomedio) . "</b></font>
					  </center>";
        //  }
    }
    else
    {
        mysqli_query($con, inspref("delete from tbl_valutazionicomp where idvalcomp=$idvalcomp"));
        if ($esistenti)
        {
            echo "
					  <p align='center'>
					  <font size=4><br>Valutazioni cancellate per l'alunno " . $id['cognome'] . " " . $id['nome'] . "!<br>
					  </font>
					  ";
        }
    }


}

//  codice per richiamare il form delle tbl_assenze;

print ("
   <form method='post' action='valcomp.php'>
   <p align='center'>

 
    <p align='center'><input type=hidden value='$gio' name=gio>
    <p align='center'><input type=hidden value='$mese - $anno' name=mese>
  
    <p align='center'><input type=hidden value='$idal' name=alunno>
    <p align='center'><input type=hidden value='$cattedra' name=cattedra>
    ");

print("<input type='submit' value='OK' name='b'></p>
     </form>");


mysqli_close($con);
stampa_piede("");


