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
//    VISUALIZZAZIONE E AUTORIZZAZIONE
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


$titolo = "Autorizzazione assemblee di classe";
$script = "<script type='text/javascript'>
         <!--
               var stile = 'top=10, left=10, width=800, height=400, status=no, menubar=no, toolbar=no, scrollbars=yes';
               function Popup(apri) 
               {
                  window.open(apri, '', stile);
               }
         //-->
         </script>";
stampa_head($titolo, "", $script, "SP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));

$iddocente = $_SESSION['idutente'];
$assaut = "SELECT * FROM tbl_assemblee WHERE docenteautorizzante=0 OR autorizzato=0";
$ris1 = mysqli_query($con, inspref($assaut)) or die("Errore durante la connessione: " . mysqli_error($con) . "<br/>" . $assaut);
print "<CENTER><table border ='1' cellpadding='5'>";

//ELENCO RICHIESTE ASSEMBLEE DA AUTORIZZARE
print "<tr class='prima'>
		<td colspan='9'><font size='2'>ASSEMBLEE DA AUTORIZZARE</font></td>
	   </tr>";

print "<tr class='prima'>
                        <td>Richiesta</td>
			
			
                        <td>Docenti concedenti</td>
			<td>Rappresentanti di classe</td>
			
			<td>O.d.G.</td>
                        <td>Autorizza</td>
			<td>Note</td>
		   </tr>";
$i = 0;
if (mysqli_num_rows($ris1) == 0)
{
    print "<td colspan='9' align='center'><b><i>Nessuna assemblea da autorizzare</i></b></td>";
}
else
{
    while ($dataass = mysqli_fetch_array($ris1))
    {
        $controllo = 0;
        if ($dataass['docenteconcedente2'] == 0)
        {
            if ($dataass['concesso1'] == 1)
            {
                $controllo = 1;
            }
        }
        else
        {
            if ($dataass['concesso1'] == 1 and $dataass['concesso2'] == 1)
            {
                $controllo = 1;
            }
        }
        if ($controllo == 1)
        {
            print "<form action='registra_autorizzazione.php' method='GET'>";
            print "<tr>";
            
            //AUTORIZZAZIONE ASSEMBLEA
            print "<td>Classe: ";
            print decodifica_classe($dataass['idclasse'], $con);
            
            
            //DATA RICHIESTA
            print "<br>Data rich.: " . data_italiana($dataass['datarichiesta']);
            //DATA ASSEMBLEA
            print "<br>Data ass.: " . data_italiana($dataass['dataassemblea']);
            // ORA ASSEMBLEA
            print "<br>Ora: ".$dataass['orainizio']."-".$dataass['orafine']."</td>";
            //DOCENTI
            print "<td>";
            if ($dataass['docenteconcedente1'] !=0)
            {
                print estrai_dati_docente($dataass['docenteconcedente1'], $con)."<br>";
            }
            if ($dataass['docenteconcedente2'] !=0)
            {
                print estrai_dati_docente($dataass['docenteconcedente2'], $con)."<br>";
            }
            
            print "</td>";
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
            print "<td>".nl2br($dataass['odg'])."</td>";
            // CONCESSIONE
            print "<td align='center'>";
            $idclasse=$dataass['idclasse'];
            $queryverifica="select * from tbl_assemblee where idclasse=$idclasse and autorizzato=1 and consegna_verbale=0";
            $risverifica=mysqli_query($con,inspref($queryverifica));
            if (mysqli_num_rows($risverifica)>0)
                print "<a href=javascript:Popup('visionaverbali.php?idclasse=$idclasse')><img src='../immagini/alert.png'></a><br>";
            print "                
            
					<select name='autorizza'>
						<option value='1'>si</option>
						<option value='2'>no</option>
					</select></td>";
            //NOTE
            print "<td><p align='center'><textarea cols=20 rows=5 name='note'></textarea></p></td>";

            //BOTTONE AUTORIZZAZIONE
            print "<td><input type='submit' value='Registra'></td>";
            print "<input type='hidden' name='idassemblea' value='" . $dataass['idassemblea'] . "'>";
            print "<input type='hidden' name='iddocente' value='" . $iddocente . "'>";
            print "<input type='hidden' name='idclasse' value='" . $dataass['idclasse'] . "'>";
            print "</tr></form>";
            $i = $i + 1;
        }
    }
    if ($i == 0)
    {
        print "<td colspan='9' align='center'><b><i>Nessuna assemblea richiesta</i></b></td>";
    }
}
print "</table>";
stampa_piede("");
mysqli_close($con);
