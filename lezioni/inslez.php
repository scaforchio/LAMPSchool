<?php

session_start();

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


@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

// istruzioni per tornare alla pagina di login se non c'è una sessione valida
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$tempoinizio = millitime();

$titolo = "Inserimento lezione";
$script = "";
stampa_head($titolo, "", $script, "SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");

$gio = stringa_html('gio');
$mese = stringa_html('mese');

$anno = stringa_html('anno');
$idlezione = stringa_html('codlezione');
$idmateria = stringa_html('materia');
$iddocente = stringa_html('iddocente');
$idgruppo = stringa_html('idgruppo');
$data = $anno . "-" . $mese . "-" . $gio;
$idclasse = stringa_html('cl');
//$argomenti = elimina_apici(stringa_html('argomenti'));
$argomenti = stringa_html('argomenti');
$ultimamodifica = stringa_html('ultimamodifica');

//$attivita = elimina_apici(stringa_html('attivita'));
$attivita = stringa_html('attivita');
$numeroore = stringa_html('orelezione');
$orainizio = stringa_html('orainizio');
$provenienza = stringa_html('provenienza');

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));


// INSERIMENTO, CANCELLAZIONE O UPDATE DATI LEZIONE   DA RIVEDERE PER INSERIMENTO PRESENZA
$ope = '';

if ($idlezione != '')
{

    if ((($argomenti != "") | ($attivita != "")) | ($numeroore != ""))
    {
        $ope = 'U';
        $query = "update tbl_lezioni set numeroore='$numeroore',orainizio='$orainizio',argomenti='$argomenti',attivita='$attivita' where idlezione=$idlezione";
    } else
    {
        $ope = 'D';
        $query = "delete from tbl_lezioni where idlezione=$idlezione";
    }
} else
{
    $ope = 'I';
    $query = "insert into tbl_lezioni(idclasse,datalezione,iddocente,idmateria,numeroore,orainizio,argomenti,attivita) values ('$idclasse','$data','$iddocente','$idmateria','$numeroore','$orainizio','" . elimina_apici($argomenti) . "','" . elimina_apici($attivita) . "')";
}

//
//  INSERIMENTO, AGGIORNAMENTO O CANCELLAZIONE FIRMA
//

$flagsovrapposizione = false;

if ($ope == 'I')
{

    if ($ris3 = eseguiQuery($con, $query))
    {
        $idlezione = mysqli_insert_id($con);
    } else
    {
        //
        //  Risolve problema di inserimento in contemporanea
        //  da parte di due docenti.
        //

        print "<b><br><div style=\"text-align: center;\">Attenzione! Lezione già inserita da altro docente: inserita solo la firma!</div></b>";
        $flagsovrapposizione = true;
        $query = "select idlezione from tbl_lezioni where
			          idclasse=$idclasse and
			          idmateria=$idmateria and 
			          datalezione='$data' and
			          orainizio=$orainizio and
			          numeroore=$numeroore";
        $rislez = eseguiQuery($con, $query);
        $rec = mysqli_fetch_array($rislez);
        $idlezione = $rec['idlezione'];
    }


    // Inserimento firma con preventiva cancellazione di firma eventualmente già esistente

    $queryinsfirma = "delete from tbl_firme where iddocente=$iddocente and idlezione=$idlezione";
    $ris4 = eseguiQuery($con, $queryinsfirma);
    $queryinsfirma = "insert into tbl_firme(idlezione,iddocente) values ('$idlezione','$iddocente')";
    $ris4 = eseguiQuery($con, $queryinsfirma);
    print "<div style=\"text-align: center;\"><b>Inserimento effettuato!</b></div>";
}
if ($ope == 'U')
{
    /*
     * VERIFICO CHE NON SIANO GIA' STATE APPORTATE MODIFICHE DA ALTRA POSTAZIONE
     */

    $queryselmod = "select oraultmod from tbl_lezioni where
              idlezione=$idlezione";
    $rislezmod = eseguiQuery($con, $queryselmod);
    $recmod = mysqli_fetch_array($rislezmod);
    $ultimamodificaprecedente = $recmod['oraultmod'];

    //  print "Ultima $ultimamodificaprecedente penultima $ultimamodifica";

    if ($ultimamodifica != $ultimamodificaprecedente)
    {
        print "<br><br><center><b><big>Lezione già modificata da altro docente! Ricaricarla nuovamente!</big></center>";

        $flagsovrapposizione = true;
    } else
    {

        $ris3 = eseguiQuery($con, $query);

        // Aggiorno il timestamp della firma
        $queryinsfirma = "delete from tbl_firme where iddocente=$iddocente and idlezione=$idlezione";
        $ris4 = eseguiQuery($con, $queryinsfirma);
        $queryinsfirma = "insert into tbl_firme(idlezione,iddocente) values ('$idlezione','$iddocente')";
        $ris4 = eseguiQuery($con, $queryinsfirma);

        print "<div style=\"text-align: center;\"><b>Aggiornamento effettuato!</b></div>";
    }
}
if ($ope == 'D')
{
    $ris3 = eseguiQuery($con, $query);
    print "<div style=\"text-align: center;\"><b>Cancellazione effettuata!</b></div>";
}


//   ttttt
//   Se si viene dal riepilogo lezioni ricavare l'idlezione
//
//
if (!$flagsovrapposizione)
{


    /*
     * INSERIMENTO ORE ASSENZA LEZIONE
     */


    ricalcola_assenze_lezioni_classe($con, $idclasse, $data);


    /*
     * INSERIMENTO VALUTAZIONI
     */

    if ($idgruppo == "")
    {
        if (!cattedra_sost($iddocente, $idmateria, $idclasse, $con))
        {
            $query = "SELECT idalunno AS al FROM tbl_alunni WHERE idalunno IN (" . estrai_alunni_classe_data($idclasse, $data, $con) . ")";
        } else
        {
            $query = "select idalunno as al from tbl_cattnosupp where idclasse=$idclasse and iddocente=$iddocente and idmateria=$idmateria";
        }
    } else
    {

        $query = "select tbl_gruppialunni.idalunno as al from tbl_gruppialunni,tbl_alunni
                 where tbl_gruppialunni.idalunno=tbl_alunni.idalunno
                 and idgruppo=$idgruppo
                 and idclasse=$idclasse";
    }
    $ris = eseguiQuery($con, $query);


    while ($id = mysqli_fetch_array($ris))            //    <-----------  ttttttt
    {
        @require '../lib/req_salva_voti.php';
    }
}

echo "<p align='center'>";
/*
  if ($provenienza != "")  //ritorno a riepilogo
  {
  if ($ope != 'D')
  {
  if ($provenienza == 'argo')
  {
  print ("<br/><font size=1><a href='riepargom.php?idlezione=" . $idlezione . "'>Ritorna a riepilogo</a><br/>");
  }
  else
  {
  print ("<br/><font size=1><a href='sitleztota.php?idlezione=" . $idlezione . "'>Ritorna a riepilogo</a><br/>");
  }
  }
  else
  {
  if ($provenienza == 'argo')
  {
  print ("<br/><font size=1><a href='riepargom.php'>Ritorna a riepilogo</a><br/>");
  }
  else
  {
  print ("<br/><font size=1><a href='sitleztota.php'>Ritorna a riepilogo</a><br/>");
  }
  }
  }
 */
$tempofine = millitime();
$durataoperazione = $tempofine - $tempoinizio;
inserisci_log("DURATA INSERIMENTO LEZIONE: $durataoperazione");
if ($_SESSION['regcl'] != "")
{
    $pr = $_SESSION['prove'];
    $cl = $_SESSION['regcl'];
    $ma = $_SESSION['regma'];
    $gi = $_SESSION['reggi'];
    $_SESSION['regcl'] = "";
    $_SESSION['regma'] = "";
    $_SESSION['reggi'] = "";
    if (!$flagsovrapposizione)
    {
        print "
			  <form method='post' id='formlez' action='../regclasse/$pr'>
			  <input type='hidden' name='gio' value='$gi'>
			  <input type='hidden' name='meseanno' value='$ma'>
			  <input type='hidden' name='idclasse' value='$cl'>
			  </form>
			  <SCRIPT language='JavaScript'>
			  {
				  document.getElementById('formlez').submit();
			  }
			  </SCRIPT>";
    } else
    {
        print "
			  <form method='post' id='formlez' action='../regclasse/$pr'>
			  <input type='hidden' name='gio' value='$gi'>
			  <input type='hidden' name='meseanno' value='$ma'>
			  <input type='hidden' name='idclasse' value='$cl'>
			  <br><div style=\"text-align: center;\"><input type='submit' value='OK'></div>
			  </form>
			  ";
    }
} else
{

    //  codice per richiamare il form delle tbl_lezioni;
    //  tttt se si viene dal riepilogo ritornare al riepilogo passando l'idlezione
    print ('
			<form method="post" action="lez.php">
			<p align="center">');

    // Se la lezione non è stata cancellata si passa il codice
    if ($ope != 'D')
    {
        print ('<p align="center"><input type=hidden value=' . $idlezione . ' name=idlezione>');
    }

    print('<input type="submit" value="OK" name="b"></p></form>');
}

mysqli_close($con);
stampa_piede("");

