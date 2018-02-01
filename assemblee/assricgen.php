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

//
//    VISUALIZZAZIONE DELLE ASSEMBLEE DI CLASSE PER I GENITORI
//	  E
//	  RICHIESTA DI ASSEMBLEE DI CLASSE PER GLI ALUNNI 
//


@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

//  istruzioni per tornare alla pagina di login se non c'è una sessione valida
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}


$titolo = "Assemblee di classe";
$script = "<script type='text/javascript'>
         <!--
               var stile = 'top=10, left=10, width=800, height=400, status=no, menubar=no, toolbar=no, scrollbars=yes';
               function Popup(apri) 
               {
                  window.open(apri, '', stile);
               }
         //-->
         </script>";
stampa_head($titolo, "", $script, "L");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));

$idalunno = $_SESSION['idstudente'];
$idclasse = estrai_classe_alunno($idalunno, $con);

$query = "select * from tbl_classi where rappresentante1=$idalunno or rappresentante2=$idalunno";
$riscontr = mysqli_query($con, inspref($query)) or die("Errore" . inspref($query));
if (mysqli_num_rows($riscontr) != 0)
{
    $alurapp = true;
}
else
{
    $alurapp = false;
}

$queryass = "SELECT * FROM tbl_assemblee 
			 WHERE idclasse = $idclasse
			 ORDER BY idassemblea DESC";
$risass = mysqli_query($con, inspref($queryass));
if (mysqli_num_rows($risass) == 0)
{
    print "<br/><CENTER><b>Non hai richiesto/effettuato ancora nessuna assemblea</b>";
}
else
{
    $classe = "SELECT anno,sezione,specializzazione FROM tbl_classi WHERE idclasse=$idclasse";
    $risclasse = mysqli_query($con, inspref($classe));
    $val = mysqli_fetch_array($risclasse);
//print "<center><b>PROVA</b></center><br/>";
    print "<center><b>Riepilogo assemblee " . $val['anno'] . $val['sezione'] . "&nbsp;" . $val['specializzazione'] . "</b></center><br/>";
    print "<CENTER><table border ='1' cellpadding='5'>";
    print "<tr class='prima'>
		<td colspan=5 align=center width=40%>RICHIESTA</td> 
		<td colspan=1 align=center width=40%>SVOLGIMENTO</td>
		<td colspan=2 align=center width=20%>ESITO</td>
	   </tr>";
    print "<tr class='prima'>
                <td>Data</td> 
                <td>O.d.G.</td>
                <td>Rappresentanti di classe</td>
                <td>Docenti ora</td>
                <td>Autorizzazione</td>
                <td>Verbale</td>
                <td>Esame verbale</td>
  	   </tr>";
    while ($dataass = mysqli_fetch_array($risass))
    {
        $idassemblea = $dataass['idassemblea'];
        print "<tr>";


//DATA RICHIESTA
        print "<td align='center'>Rich.: " . data_italiana($dataass['datarichiesta']) . "<br>";
//DATA ASSEMBLEA
        print "Svolg.: " . data_italiana($dataass['dataassemblea']) . "<br>";
//INIZIO - FINE
        print "Ora: " . $dataass['orainizio'] . " - " . $dataass['orafine'] . "</td>";

//ORDINE DEL GIORNO
        print "<td>" . nl2br($dataass['odg']) . "</td>";

//RAPPRESENTANTI
        $alu = "SELECT cognome,nome FROM tbl_alunni 
		        WHERE idalunno=" . $dataass['rappresentante1'] . "
		        OR idalunno=" . $dataass['rappresentante2'] . "
		        ORDER BY cognome";

        $risalu = mysqli_query($con, inspref($alu));
        print "<td>";
        $numerorappresentantirichiedenti = 0;
        while ($dataalu = mysqli_fetch_array($risalu))
        {
            print ($dataalu['cognome'] . "&nbsp;" . $dataalu['nome'] . "<br/>");
            $numerorappresentantirichiedenti++;
        }

        if ($numerorappresentantirichiedenti == 1 & $_SESSION['idstudente'] != $dataass['rappresentante1'])
        {
            if ($alurapp)
            {
                print "<a href='registra_conferma.php?idassemblea=" . $dataass['idassemblea'] . "'>CONFERMA RICHIESTA</a>";
            }
        }
        print "</td>";


//DOCENTI CONCEDENTI
        $doc = "SELECT cognome,nome FROM tbl_docenti WHERE iddocente=" . $dataass['docenteconcedente1'];
        if ($dataass['docenteconcedente2'] != 0)
        {
            $doc .= " OR iddocente=" . $dataass['docenteconcedente2'] . " ORDER BY cognome";
        }
        print "<td>";
        $risdoc = mysqli_query($con, inspref($doc));
        $cont = 1;
        while ($datadoc = mysqli_fetch_array($risdoc))
        {
            if ($cont == 1)
            {
                if ($dataass['concesso1'] == 1)
                {
                    $fontin = "<font color=green>";
                    $fontfi = "</font>";
                }
                else if ($dataass['concesso1'] == 2)
                {
                    $fontin = "<font color=red>";
                    $fontfi = "</font>";
                }
                else
                {
                    $fontin = "";
                    $fontfi = "";
                }
            }
            else
            {
                if ($dataass['concesso2'] == 1)
                {
                    $fontin = "<font color=green>";
                    $fontfi = "</font>";
                }
                else if ($dataass['concesso2'] == 2)
                {
                    $fontin = "<font color=red>";
                    $fontfi = "</font>";
                }
                else
                {
                    $fontin = "";
                    $fontfi = "";
                }
            }
            print ($fontin . $datadoc['cognome'] . "&nbsp;" . $datadoc['nome'] . "<br/>" . $fontfi);
            $cont++;
        }
        print "</td>";

//DOCENTE AUTORIZZANTE (se esiste)
//AUTORIZZAZIONE

        if ($dataass['autorizzato'] == 0)
        {
            print "<td align='center'>&nbsp; </td>";
        }
        else
        {
            if ($dataass['autorizzato'] == 2)
            {
                print "<td><center><img src='../immagini/red_cross.gif'></center><br>" . nl2br($dataass['note']) . "<br><i>" . estrai_dati_docente($dataass['docenteautorizzante'], $con) . "</i></td>";
            }
            else
            {
                print "<td><center><img src='../immagini/green_tick.gif'></center><br>" . nl2br($dataass['note']) . "<br><i>" . estrai_dati_docente($dataass['docenteautorizzante'], $con) . "</i></td>";
            }
        }

//VERBALE
        if ($alurapp)
        {
            if ($dataass['verbale'] == '' and $dataass['autorizzato'] == 1 and date('Y-m-d') > $dataass['dataassemblea'])
            {
                print "<td align='center'><img src='../immagini/red_cross.gif'>";
                print "<br/><a href='insver.php?idassemblea=" . $dataass['idassemblea'] . "&idclasse=$idclasse'>Inserisci verbale!</a>";
            }
            else
            {
                if ($dataass['verbale'] == '' and $dataass['autorizzato'] == 1 and date('Y-m-d') == $dataass['dataassemblea'])
                {
                    print "<td><br/><a href='insver.php?idassemblea=" . $dataass['idassemblea'] . "&idclasse=$idclasse'>INSERISCI</a>";
                }
                else
                {
                    if (date('Y-m-d') < $dataass['dataassemblea'])
                        print "<td align='center'>&nbsp;";
                    else if ($dataass['verbale'] != "")
                        print "<td>" . nl2br($dataass['verbale']);
                    else
                        print "<td>&nbsp;";

                    if ($dataass['verbale'] != "")
                    {
                        if ($dataass['oratermine'] != "00:00:00")
                        {
                            print "<br>Ora termine: " . substr($dataass['oratermine'], 0, 5) . "<br>";
                        }
                        else
                        {
                            print "<br>";
                        }
//SEGRETARIO
                        $alu = "SELECT cognome,nome FROM tbl_alunni
				WHERE idalunno=" . $dataass['alunnosegretario'];

                        $risalu = mysqli_query($con, inspref($alu));
                        $dataalu = mysqli_fetch_array($risalu);
                        print "<center>SEGRETARIO<br>" . $dataalu['cognome'] . "&nbsp;" . $dataalu['nome'] . "</center><br>";

//PRESIDENTE
                        if ($dataass['alunnopresidente'] != 0)
                        {
                            $alu = "SELECT cognome,nome FROM tbl_alunni
				WHERE idalunno=" . $dataass['alunnopresidente'];

                            $risalu = mysqli_query($con, inspref($alu)) or die("Erore" . inspref($alu));
                            $dataalu = mysqli_fetch_array($risalu);
                            print "<center>PRESIDENTE<br>" . $dataalu['cognome'] . "&nbsp;" . $dataalu['nome'] . "</center>";
                        }
                        else
                        {
                            if ($idalunno != $dataass['alunnosegretario'])
                            {
                                print "<center><a href='registra_firmapresidente.php?idassemblea=$idassemblea'>CONFERMA E TRASMETTI VERBALE</a></center>";
                            }
                            else
                            {
                                print "<center><a href='insver.php?idassemblea=$idassemblea'>CORREGGI VERBALE</a></center><br><br><center>PRESIDENTE<br>Firma non presente!<br>Verbale non ancora trasmesso!";
                            }
                        }
                    }
                }
            }
        }
        else  // Alunno non rappresentante
        {
            if ($dataass['alunnopresidente'] != 0 & $dataass['alunnosegretario'] != 0)
                print "<td align='center'>" . $dataass['verbale'] . "";
            else
                print "<td align='center'>";
        }
        print "</td>";


        print "<td>" . nl2br($dataass['commenti_verbale']) . "<br><i><b>" . estrai_dati_docente($dataass['docente_visione'], $con) . "</b></i></td>";





        print "</tr>";
    }


    print "</table>";
}



if ($alurapp)
{
    print "<form action='ricgen.php' method='POST'>";
    print " <p align='center'><input type=hidden value='" . $idclasse . "' name='idclasse'></p>";
    print "	<p align='center'><input type=submit value='Richiedi nuova assemblea'>";
    print "</form>";
}


stampa_piede("");
mysqli_close($con);



