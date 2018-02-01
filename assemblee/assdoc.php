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
//    VISUALIZZAZIONE E CONCESSIONE
//	  DELLE ASSEMBLEE DI CLASSE PER I DOCENTI
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


$titolo = "Assemblee proprie classi";
$script = "<script type='text/javascript'>
         <!--
               var stile = 'top=10, left=10, width=800, height=400, status=no, menubar=no, toolbar=no, scrollbars=yes';
               function Popup(apri) 
               {
                  window.open(apri, '', stile);
               }
         //-->
         </script>";
stampa_head($titolo, "", $script, "SD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));

$iddocente = stringa_html('iddocente');
$iddocente = $_SESSION['idutente'];
//query per selezionare assemblee da concedere riferite al docente collegato
$asses = "SELECT * FROM tbl_assemblee 
		  WHERE ((docenteconcedente1=$iddocente AND concesso1=0)
                        OR (docenteconcedente2=$iddocente AND concesso2=0))
                        AND (rappresentante1<>0 and rappresentante2<>0)";
$ris1 = mysqli_query($con, inspref($asses)) or die("Errore durante la connessione: " . mysqli_error($con) . "<br/>" . $asses);

//ELENCO RICHIESTE ASSEMBLEE DA VALIDARE
print "<CENTER><table border ='1' cellpadding='5'>";

print "<tr class='prima'>
		<td colspan='4'><font size='3'>ASSEMBLEE RICHIESTE</font></td>
	   </tr>";

print "<tr class='prima'>
			
			<td>Dati assemblea</td> 
			
			<td>Richiedenti</td>
			
			<td>Ordine del giorno</td>
                        <td>Concessione</td>
		   </tr>";
$i = 0;
if (mysqli_num_rows($ris1) == 0)
{
    print "<td colspan='4' align='center'><b><i>Nessuna assemblea richiesta</i></b></td>";
}
else
{
    while ($dataass = mysqli_fetch_array($ris1))
    {
        $idassemblea=$dataass['idassemblea'];
        if ($dataass['docenteconcedente1'] == $iddocente)
        {
            $controllo = $dataass['concesso1'];
        }
        if ($dataass['docenteconcedente2'] == $iddocente)
        {
            $controllo = $dataass['concesso2'];
        }
        if ($controllo == 0)
        {
            print "<tr>";
            
            //DATA RICHIESTA
            //DATA RICHIESTA
            print "<td>Classe: " . decodifica_classe($dataass['idclasse'],$con,1). "<br>";
            print "Rich.: " . data_italiana($dataass['datarichiesta']) . "<br>";
            //DATA ASSEMBLEA
            print "Svolg.: " . data_italiana($dataass['dataassemblea']) . "<br>";
            //INIZIO - FINE
            print "Ora: " . $dataass['orainizio'] . " - " . $dataass['orafine'] . "</td>";


            //RAPPRESENTANTI
            $alu = "SELECT cognome,nome FROM tbl_alunni 
					WHERE idalunno=" . $dataass['rappresentante1'] . "
					OR idalunno=" . $dataass['rappresentante2'] . "
					ORDER BY cognome";

            $risalu = mysqli_query($con, inspref($alu));
            print "<td>";
            while ($dataalu = mysqli_fetch_array($risalu))
            {
                print ($dataalu['cognome'] . "&nbsp;" . $dataalu['nome'] . "<br/>");
            }
            print "</td>";

           
            //ORDINE DEL GIORNO
           // print "<td><a href=javascript:Popup('visdatiass.php?dato=odg&idass=" . $dataass['idassemblea'] . "')>Visualizza Ordine del Giorno</a></td>";
           print "<td>".nl2br($dataass['odg'])."</td>";
            //BOTTONE INVIO
            print "<td><a href='registra_concessione.php?idassemblea=$idassemblea&concesso=1'>CONCEDI</a>"
                    . "&nbsp;&nbsp;<a href='registra_concessione.php?idassemblea=$idassemblea&concesso=0'>NEGA</a></td>";
            print "</tr>";
            $i = $i + 1;
        }
    }
    if ($i == 0)
    {
        print "<td colspan='8' align='center'><b><i>Nessuna assemblea richiesta</i></b></td>";
    }
}
print "</table>";
//spazio tabella
print "<tr><td height='20' colspan='8' style='border-left-style:hidden;border-right-style:hidden'></td></tr>";
//ELENCO ASSEMBLEE CONCESSE
print "<CENTER><br><br><br><table border ='1' cellpadding='5'>";

$assco = "SELECT * FROM tbl_assemblee 
		  WHERE ((docenteconcedente1=$iddocente AND concesso1=1)
                        OR (docenteconcedente2=$iddocente AND concesso2=1))";

$ris2 = mysqli_query($con, inspref($assco)) or die("Errore durante la connessione: " . mysqli_error($con) . "<br/>" . $assco);
print "<tr class='prima'>
		<td colspan='8'><font size='2'>ASSEMBLEE CONCESSE</font></td>
	   </tr>";
print "<tr class='prima'>
			
			<td>Data</td> 
			
			<td>Rappresentanti di classe</td>
			
			<td>O.d.G.</td>
		   </tr>";
if (mysqli_num_rows($ris2) == 0)
{
    print "<td colspan='8' align='center'><b><i>Nessuna assemblea concessa</i></b></td>";
}
else
{
    while ($dataass = mysqli_fetch_array($ris2))
    {
        if ($dataass['docenteconcedente1'] == $iddocente)
        {
            $controllo = $dataass['concesso1'];
        }
        if ($dataass['docenteconcedente2'] == $iddocente)
        {
            $controllo = $dataass['concesso2'];
        }
        if ($controllo == 1)
        {
            print "<tr>";
            
            
            //DATA RICHIESTA
            //DATA RICHIESTA
            print "<td>Classe: " . decodifica_classe($dataass['idclasse'],$con,1). "<br>";
            print "Rich.: " . data_italiana($dataass['datarichiesta']) . "<br>";
            //DATA ASSEMBLEA
            print "Svolg.: " . data_italiana($dataass['dataassemblea']) . "<br>";
            //INIZIO - FINE
            print "Ora: " . $dataass['orainizio'] . " - " . $dataass['orafine'] . "</td>";


            //RAPPRESENTANTI
            $alu = "SELECT cognome,nome FROM tbl_alunni 
					WHERE idalunno=" . $dataass['rappresentante1'] . "
					OR idalunno=" . $dataass['rappresentante2'] . "
					ORDER BY cognome";

            $risalu = mysqli_query($con, inspref($alu));
            print "<td>";
            while ($dataalu = mysqli_fetch_array($risalu))
            {
                print ($dataalu['cognome'] . "&nbsp;" . $dataalu['nome'] . "<br/>");
            }
            print "</td>";

            //ORDINE DEL GIORNO
            //print "<td><a href=javascript:Popup('visdatiass.php?dato=odg&idass=" . $dataass['idassemblea'] . "')>Visualizza OdG</a></td>";
            print "<td>" . nl2br($dataass['odg']) . "</td>";
            print "</tr>";
        }
        else
        {
            print "<td colspan='8' align='center'><b><i>Nessuna assemblea concessa</i></b></td>";
        }
    }
}


print "</table>";
//ELENCO ASSEMBLEE EFFETTUATE
print "<CENTER><br><br><br><table border ='1' cellpadding='5'>";

$assef = "SELECT * FROM tbl_assemblee 
		  WHERE (docenteconcedente1=$iddocente OR docenteconcedente2=$iddocente)
		  AND (verbale != '')";

$ris3 = mysqli_query($con, inspref($assef)) or die("Errore durante la connessione: " . mysqli_error($con) . "<br/>" . $assef);
print "<tr class='prima'>
		<td colspan='8'><font size='2'>ASSEMBLEE EFFETTUATE</font></td>
	   </tr>";

print "<tr class='prima'>
			<td colspan='2'>Verbale</td> 
			<td>Data richiesta</td>
			<td>Data assemblea</td> 
			<td>Rappresentanti di classe</td>
			<td>Presidente</td>
			<td>Segretario</td>
			<td>Ordine del giorno</td>
		   </tr>";
if (mysqli_num_rows($ris3) == 0)
{
    print "<td colspan='8' align='center'><b><i>Nessuna assemblea effettuata</i></b></td>";
}
else
{
    while ($dataass = mysqli_fetch_array($ris3))
    {
        print "<tr>";
        //VERBALE
        if ($dataass['consegna_verbale'] == 0)
        {
            print "<td colspan='2' align='center'><img src='../immagini/red_cross.gif'></td>";
        }
        else
        {
            print "<td colspan='2' align='center'><img src='../immagini/green_tick.gif'><br/><a href=javascript:Popup('visdatiass.php?dato=ver&idass=" . $dataass['idassemblea'] . "')>Visualizza verbale</a></td>";
        }
        //DATA RICHIESTA
        print "<td align='center'>" . data_italiana($dataass['datarichiesta']) . "</td>";
        //DATA ASSEMBLEA
        print "<td align='center'>" . data_italiana($dataass['dataassemblea']) . "</td>";

        //RAPPRESENTANTI
        $alu = "SELECT cognome,nome FROM tbl_alunni 
				WHERE idalunno=" . $dataass['rappresentante1'] . "
				OR idalunno=" . $dataass['rappresentante2'] . "
				ORDER BY cognome";

        $risalu = mysqli_query($con, inspref($alu));
        print "<td>";
        while ($dataalu = mysqli_fetch_array($risalu))
        {
            print ($dataalu['cognome'] . "&nbsp;" . $dataalu['nome'] . "<br/>");
        }
        print "</td>";

        //PRESIDENTE
        $alu = "SELECT cognome,nome FROM tbl_alunni
				WHERE idalunno=" . $dataass['alunnopresidente'];

        $risalu = mysqli_query($con, inspref($alu));
        $dataalu = mysqli_fetch_array($risalu);
        print "<td>" . $dataalu['cognome'] . "&nbsp;" . $dataalu['nome'] . "</td>";

        //SEGRETARIO
        $alu = "SELECT cognome,nome FROM tbl_alunni
				WHERE idalunno=" . $dataass['alunnosegretario'];

        $risalu = mysqli_query($con, inspref($alu));
        $dataalu = mysqli_fetch_array($risalu);
        print "<td>" . $dataalu['cognome'] . "&nbsp;" . $dataalu['nome'] . "</td>";

        //ORDINE DEL GIORNO
        print "<td><a href=javascript:Popup('visdatiass.php?dato=odg&idass=" . $dataass['idassemblea'] . "')>Visualizza Ordine del Giorno</a></td>";

        print "</tr>";
    }
}

print "</table>";

stampa_piede("");
mysqli_close($con);
