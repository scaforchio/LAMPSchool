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
$codlez = stringa_html('codlezione');
$materia = stringa_html('materia');
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

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));


// INSERIMENTO, CANCELLAZIONE O UPDATE DATI LEZIONE   DA RIVEDERE PER INSERIMENTO PRESENZA
$ope = '';

if ($codlez != '')
{

    if ((($argomenti != "") | ($attivita != "")) | ($numeroore != ""))
    {
        $ope = 'U';
        $query = "update tbl_lezioni set numeroore='$numeroore',orainizio='$orainizio',argomenti='$argomenti',attivita='$attivita' where idlezione=$codlez";
    }
    else
    {
        $ope = 'D';
        $query = "delete from tbl_lezioni where idlezione=$codlez";
    }
}
else
{
    $ope = 'I';
    $query = "insert into tbl_lezioni(idclasse,datalezione,iddocente,idmateria,numeroore,orainizio,argomenti,attivita) values ('$idclasse','$data','$iddocente','$materia','$numeroore','$orainizio','" . elimina_apici($argomenti) . "','" . elimina_apici($attivita) . "')";
}

//
//  INSERIMENTO, AGGIORNAMENTO O CANCELLAZIONE FIRMA
//

$flagsovrapposizione = false;

if ($ope == 'I')
{

    if ($ris3 = mysqli_query($con, inspref($query))) // or die ("Errore nella query di inserimento: ". mysqli_error($con));
    {
        $codlez = mysqli_insert_id($con);
    }
    else
    {
        //
        //  Risolve problema di inserimento in contemporanea
        //  da parte di due docenti.
        //

        print "<b><br><div style=\"text-align: center;\">Attenzione! Lezione già inserita da altro docente: inserita solo la firma!</div></b>";
        $flagsovrapposizione = true;
        $query = "select idlezione from tbl_lezioni where
			          idclasse=$idclasse and
			          idmateria=$materia and 
			          datalezione='$data' and
			          orainizio=$orainizio and
			          numeroore=$numeroore";
        $rislez = mysqli_query($con, inspref($query)) or die ("Errore: " . inspref($query));
        $rec = mysqli_fetch_array($rislez);
        $codlez = $rec['idlezione'];
    }


    // Inserimento firma con preventiva cancellazione di firma eventualmente già esistente

    $queryinsfirma = "delete from tbl_firme where iddocente=$iddocente and idlezione=$codlez";
    $ris4 = mysqli_query($con, inspref($queryinsfirma)) or die ("Errore nella query di cancellazione: " . mysqli_error($con));
    $queryinsfirma = "insert into tbl_firme(idlezione,iddocente) values ('$codlez','$iddocente')";
    $ris4 = mysqli_query($con, inspref($queryinsfirma)) or die ("Errore nella query di inserimento: " . mysqli_error($con));
    print "<div style=\"text-align: center;\"><b>Inserimento effettuato!</b></div>";
}
if ($ope == 'U')
{
    /*
     * VERIFICO CHE NON SIANO GIA' STATE APPORTATE MODIFICHE DA ALTRA POSTAZIONE
     */
    
    $queryselmod = "select oraultmod from tbl_lezioni where
              idlezione=$codlez";
    $rislezmod = mysqli_query($con, inspref($queryselmod)) or die ("Errore: " . inspref($queryselmod));
    $recmod = mysqli_fetch_array($rislezmod);
    $ultimamodificaprecedente = $recmod['oraultmod'];
    
  //  print "Ultima $ultimamodificaprecedente penultima $ultimamodifica";
    
    if ($ultimamodifica != $ultimamodificaprecedente)
    {
        print "<br><br><center><b><big>Lezione già modificata da altro docente! Ricaricarla nuovamente!</big></center>";
        
        $flagsovrapposizione=true;
        
    }
   
    
    else
    {
    
    $ris3 = mysqli_query($con, inspref($query)) or die ("Errore nella query di aggiornamento: " . mysqli_error($con));

    // Aggiorno il timestamp della firma
    $queryinsfirma = "delete from tbl_firme where iddocente=$iddocente and idlezione=$codlez";
    $ris4 = mysqli_query($con, inspref($queryinsfirma)) or die ("Errore nella query di inserimento: " . mysqli_error($con));
    $queryinsfirma = "insert into tbl_firme(idlezione,iddocente) values ('$codlez','$iddocente')";
    $ris4 = mysqli_query($con, inspref($queryinsfirma)) or die ("Errore nella query di inserimento: " . mysqli_error($con));
    
    print "<div style=\"text-align: center;\"><b>Aggiornamento effettuato!</b></div>";
    }
}
if ($ope == 'D')
{
    $ris3 = mysqli_query($con, inspref($query)) or die ("Errore nella query di cancellazione: " . mysqli_error($con));
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
     *
     * L'inserimentoe modifica delle ore di assenza è stata disabilitata in quanto le assenze delle lezioni
     * sono state sincronizzate con le assenze, ritardi e uscite anticipate.

    if ($idgruppo == "")
    {
        $query = "SELECT idalunno AS al FROM tbl_alunni WHERE idalunno in (".estrai_alunni_classe_data($idclasse,$data,$con).")";
    }
    else
    {
        $query = "select tbl_gruppialunni.idalunno as al from tbl_gruppialunni,tbl_alunni
           where tbl_gruppialunni.idalunno=tbl_alunni.idalunno
           and idgruppo=$idgruppo
           and idclasse=$idclasse";
    }
    $ris = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));


    while ($id = mysqli_fetch_array($ris))
    {

        $va = "oreass" . $id['al'];
        $assal = stringa_html($va);

        $query = "SELECT * FROM tbl_asslezione WHERE idalunno=" . $id['al'] . " AND idlezione=$codlez";
        $rissel = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
        if (mysqli_num_rows($rissel) > 0)
        {
            $query = "DELETE FROM tbl_asslezione WHERE idalunno='" . $id['al'] . "' AND idlezione='" . $codlez . "'";
            $ris2 = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));

        }
        if ($ope == 'U' | $ope == 'I')
        {
            if ($assal != "0")
            {
                if ($assal <= $numeroore)
                {

                    $query = "INSERT INTO tbl_asslezione(idalunno,idmateria,data,oreassenza,idlezione)
									VALUES(" . $id['al'] . "," . $materia . ",'" . $data . "','" . $assal . "','" . $codlez . "')";

                    $ris3 = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
                }
                else
                {


                    $query = "INSERT INTO tbl_asslezione(idalunno,idmateria,data,oreassenza,idlezione)
									VALUES(" . $id['al'] . "," . $materia . ",'" . $data . "','" . $numeroore . "','" . $codlez . "')";

                    $ris4 = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
                    print "Ore di assenza cambiate per alunno " . $id['al'];
                }
            }
        }
    }

    */


    /*
     * INSERIMENTO VALUTAZIONI
     */

    if ($idgruppo == "")
    {
        if (!cattedra_sost($iddocente, $materia, $idclasse, $con))
        {
            $query = "SELECT idalunno AS al FROM tbl_alunni WHERE idalunno IN (" . estrai_alunni_classe_data($idclasse, $data, $con) . ")";
        }
        else
        {
            $query = "select idalunno as al from tbl_cattnosupp where idclasse=$idclasse and iddocente=$iddocente and idmateria=$materia";
        }
    }
    else
    {

        $query = "select tbl_gruppialunni.idalunno as al from tbl_gruppialunni,tbl_alunni
                 where tbl_gruppialunni.idalunno=tbl_alunni.idalunno
                 and idgruppo=$idgruppo
                 and idclasse=$idclasse";
    }
    $ris = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));


    while ($id = mysqli_fetch_array($ris))            //    <-----------  ttttttt
    {
        //
        //   INSERIMENTO VOTI SCRITTI
        //

        $idal = $id['al'];
        $va = "votos" . $idal;

        $ga = "giudizios" . $idal;

        $votoal = is_stringa_html($va) ? stringa_html($va) : 999;  // Se 999 vuol dire che è un voto medio

        $giudal = stringa_html($ga);
        if ($votoal == 99 && $giudal == '')  // Il giudizio è da cancellare
        {
            $query = 'SELECT * FROM tbl_valutazioniintermedie WHERE idalunno=' . $idal . ' AND idlezione="' . $codlez . '" AND tipo="S"';

            $rissel = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
            if (mysqli_num_rows($rissel) > 0)
            {
                $query = "delete from tbl_valutazioniintermedie where idalunno=" . $idal . " and idlezione='$codlez' and tipo='S'";
                $risd = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
            }
        }
        else
        {

            // Verifico se il voto già c'è
            $query = "select idvalint from tbl_valutazioniintermedie where idalunno=" . $idal . " and idlezione='$codlez' and tipo='S'";

            $risric = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
            if ($rec = mysqli_fetch_array($risric))
            {
                $idvalint = $rec['idvalint'];
            }
            else
            {
                $idvalint = 0;
            }
            if ($idvalint != 0)
            {
                if ($votoal != 999)
                {
                    $query = "update tbl_valutazioniintermedie set voto=$votoal, giudizio='$giudal' where idalunno=" . $idal . " and idlezione='$codlez' and tipo='S'";
                    $risup = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
                }
                else
                {
                    $query = "update tbl_valutazioniintermedie set giudizio='$giudal' where idalunno=" . $idal . " and idlezione='$codlez' and tipo='S'";
                    $risup = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
                }
            }
            else
            {
                // Inserisco voti non già esistenti
                $query = "insert into tbl_valutazioniintermedie(idalunno,idmateria,iddocente,idclasse,idlezione,data,tipo,voto,giudizio)
							values(" . $idal . ",$materia,$iddocente,$idclasse,'$codlez','$data','S',$votoal,'$giudal')";
                $risins = mysqli_query($con, inspref($query));
            }
        }

        //
        //   INSERIMENTO VOTI ORALI
        //


        $va = "votoo" . $idal;

        $ga = "giudizioo" . $idal;

        $votoal = is_stringa_html($va) ? stringa_html($va) : 999;

        $giudal = stringa_html($ga);
        if ($votoal == 99 && $giudal == '')
        {
            $query = "SELECT * FROM tbl_valutazioniintermedie WHERE idalunno=" . $idal . " AND idlezione=" . $codlez . " AND tipo='O'";
            $rissel = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
            if (mysqli_num_rows($rissel) > 0)
            {
                $query = "delete from tbl_valutazioniintermedie where idalunno=" . $idal . " and idlezione='$codlez' and tipo='O'";
                $risd = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
            }
        }
        else
        {

            // Verifico se il voto già c'è
            $query = "select idvalint from tbl_valutazioniintermedie where idalunno=" . $idal . " and idlezione='$codlez' and tipo='O'";

            $risric = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
            if ($rec = mysqli_fetch_array($risric))
            {
                $idvalint = $rec['idvalint'];
            }
            else
            {
                $idvalint = 0;
            }
            if ($idvalint != 0)
            {
                if ($votoal != 999)
                {
                    $query = "update tbl_valutazioniintermedie set voto=$votoal, giudizio='$giudal' where idalunno=" . $idal . " and idlezione='$codlez' and tipo='O'";
                    $risup = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
                }
                else
                {
                    $query = "update tbl_valutazioniintermedie set giudizio='$giudal' where idalunno=" . $idal . " and idlezione='$codlez' and tipo='O'";
                    $risup = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
                }
            }
            else
            {
                // Inserisco voti non già esistenti
                $query = "insert into tbl_valutazioniintermedie(idalunno,idmateria,iddocente,idclasse,idlezione,data,tipo,voto,giudizio)
							values(" . $idal . ",$materia,$iddocente,$idclasse,'$codlez','$data','O',$votoal,'$giudal')";
                $risins = mysqli_query($con, inspref($query));
            }
        }


        //
        //   INSERIMENTO VOTI PRATICI
        //

        $va = "votop" . $idal;

        $ga = "giudiziop" . $idal;

        $votoal = is_stringa_html($va) ? stringa_html($va) : 999;

        $giudal = stringa_html($ga);
        if ($votoal == 99 && $giudal == '')
        {
            $query = 'SELECT * FROM tbl_valutazioniintermedie WHERE idalunno=' . $idal . ' AND idlezione="' . $codlez . '" AND tipo="P"';
            $rissel = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
            if (mysqli_num_rows($rissel) > 0)
            {
                $query = "delete from tbl_valutazioniintermedie where idalunno=" . $idal . " and idlezione='$codlez' and tipo='P'";
                $risd = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
            }
        }
        else
        {
            // Verifico se il voto già c'è
            $query = "select idvalint from tbl_valutazioniintermedie where idalunno=" . $idal . " and idlezione='$codlez' and tipo='P'";

            $risric = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
            if ($rec = mysqli_fetch_array($risric))
            {
                $idvalint = $rec['idvalint'];
            }
            else
            {
                $idvalint = 0;
            }
            if ($idvalint != 0)
            {
                if ($votoal != 999)
                {
                    $query = "update tbl_valutazioniintermedie set voto=$votoal, giudizio='$giudal' where idalunno=" . $idal . " and idlezione='$codlez' and tipo='P'";
                    $risup = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
                }
                else
                {
                    $query = "update tbl_valutazioniintermedie set giudizio='$giudal' where idalunno=" . $idal . " and idlezione='$codlez' and tipo='P'";
                    $risup = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
                }
            }
            else
            {
                // Inserisco voti non già esistenti
                $query = "insert into tbl_valutazioniintermedie(idalunno,idmateria,iddocente,idclasse,idlezione,data,tipo,voto,giudizio)
							values(" . $idal . ",$materia,$iddocente,$idclasse,'$codlez','$data','P',$votoal,'$giudal')";
                $risins = mysqli_query($con, inspref($query));
            }
        }

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
            print ("<br/><font size=1><a href='riepargom.php?idlezione=" . $codlez . "'>Ritorna a riepilogo</a><br/>");
        }
        else
        {
            print ("<br/><font size=1><a href='sitleztota.php?idlezione=" . $codlez . "'>Ritorna a riepilogo</a><br/>");
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
    }
    else
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
}
else
{

    //  codice per richiamare il form delle tbl_lezioni;
    //  tttt se si viene dal riepilogo ritornare al riepilogo passando l'idlezione
    print ('
			<form method="post" action="lez.php">
			<p align="center">');

    // Se la lezione non è stata cancellata si passa il codice
    if ($ope != 'D')
    {
        print ('<p align="center"><input type=hidden value=' . $codlez . ' name=idlezione>');
    }

    print('<input type="submit" value="OK" name="b"></p></form>');
}

mysqli_close($con);
stampa_piede("");

