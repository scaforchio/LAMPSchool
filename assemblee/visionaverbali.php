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

//  istruzioni per tornare alla pagina di login se non c'è una sessione valida
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}


$titolo = "Visione verbali assemblee di classe";
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

$iddocente = stringa_html('idutente');
$idclasse = stringa_html('idclasse');
$mese = stringa_html('mese');



//
//   Classi
//
print "<form action='visionaverbali.php' name='classi' method='POST'>";
print " <center><b>Classe </b><SELECT NAME='idclasse' ONCHANGE='classi.submit()'><option value=''>&nbsp;";


$query = "select idclasse, anno, sezione, specializzazione from tbl_classi
          order by anno, specializzazione, sezione";

$ris = mysqli_query($con, inspref($query));
while ($nom = mysqli_fetch_array($ris))
{
    print "<option value='";
    print ($nom["idclasse"]);
    print "'";
    if ($idclasse == $nom["idclasse"])
    {
        print " selected";
    }
    print ">";
    print ($nom["anno"]);
    print "&nbsp;";
    print($nom["sezione"]);
    print "&nbsp;";
    print($nom["specializzazione"]);
    print "</option>";
}

print "</select>";



//
//   Mese
//

print " <center><b>Mese </b><SELECT NAME='mese' ONCHANGE='classi.submit()'><option value=''>&nbsp;";



for ($m = 9; $m <= 12; $m++)
{
    if ($m < 10)
    {
        $ms = "0" . $m;
    }
    else
    {
        $ms = '' . $m;
    }
    if ("$ms - $annoscol" == $mese)
    {
        echo("<option selected>$ms - $annoscol</option>");
    }
    else
    {
        echo("<option>$ms - $annoscol</option>");
    }
}
$annoscolsucc = $annoscol + 1;
for ($m = 1; $m <= 8; $m++)
{
    if ($m < 10)
    {
        $ms = '0' . $m;
    }
    else
    {
        $ms = '' . $m;
    }
    if ("$ms - $annoscolsucc"== $mese)
    {
        echo("<option selected>$ms - $annoscolsucc</option>");
    }
    else
    {
        echo("<option>$ms - $annoscolsucc</option>");
    }
}
echo("</select>");

print ("</form>");

//STAMPO TABELLA IN BASE ALLA CLASSE

$query = "SELECT * FROM tbl_assemblee where (autorizzato=1 OR autorizzato=2)";
if ($idclasse != "")
{
    $query .= " AND idclasse=$idclasse";
}
if ($mese != "")
{
    $mm = substr($mese, 0, 2);
    $query .= " AND month(dataassemblea)=$mm";
}
$query.=" order by dataassemblea DESC";

$ris = mysqli_query($con, inspref($query)) or die("Errore durante la connessione: " . mysqli_error($con) . "<br/>" . $query);
print "<br/><br/><center><table border ='1' cellpadding='5' class='smallchar'>";

print "<tr class='prima'>
				<td>Assemblea</td> 
                                <td>O.d.G.</td>
				<td>Autorizzazione</td>
				<td>Verbale</td>
                                <td>Commenti verbale</td>
		   </tr>";
if (mysqli_num_rows($ris) == 0)
{
    print "<td colspan='8' align='center'><b><i>Nessuna assemblea da visualizzare</i></b></td>";
}
else
{
    while ($data = mysqli_fetch_array($ris))
    {
        
        //CLASSE
        print "<td>" . decodifica_classe($data['idclasse'], $con) . "";
        //DATA RICHIESTA
        print "<br>Rich.:" . data_italiana($data['datarichiesta']) . "<br>Eff.:" . data_italiana($data['dataassemblea']) . " ore:" . $data['orainizio'] . "-" . $data['orafine'];
        //ODG
        
         
        print "<br>Doc.:<br><i>".estrai_dati_docente($data['docenteconcedente1'], $con);
        if ($data['docenteconcedente2'] != 0)
        {
            print "<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".estrai_dati_docente($data['docenteconcedente2'],$con);
        }
        print "</i></td>";
        
        // ODG
       // print "<td align='center'><textarea>" . $data['odg'] . "\n" . estrai_dati_alunno_rid($data['rappresentante1'], $con) . "\n" . estrai_dati_alunno_rid($data['rappresentante2'], $con). "</textarea></td>";
        print "<td>" . nl2br($data['odg']) . "<br><i>" . estrai_dati_alunno_rid($data['rappresentante1'], $con) . "<br>" . estrai_dati_alunno_rid($data['rappresentante2'], $con). "</i></td>";
        
        
        //AUTORIZZAZIONE
        print "<td>";
        if ($data['autorizzato']==1)
            print "<img src='../immagini/green_tick.gif'> <i>".estrai_dati_docente ($data['docenteautorizzante'], $con)."</i>";
        if ($data['autorizzato']==2)
            
            print "<img src='../immagini/red_cross.gif'> <i>".estrai_dati_docente ($data['docenteautorizzante'], $con)."</i>";
        print "<br>".$data['note'];

        

        //VERBALE
        if ($data['consegna_verbale'] != 0)
        {
            print "<td>" . nl2br($data['verbale']) .
                    "<br>SEGRETARIO: <i>" . estrai_dati_alunno_rid($data['alunnosegretario'], $con) ."</i>" .
                    "<br>PRESIDENTE: <i> " . estrai_dati_alunno_rid($data['alunnopresidente'], $con) . "</i></td>";
        }
        else
        {
            print "<td>&nbsp;</td>";
        }

        //COMMENTI
        print "<td>" . nl2br($data['commenti_verbale']) . "<br><i>" . estrai_dati_docente($data['docente_visione'], $con) . "</i></td>";


        print "</tr>";
    }
}


print "</table>";

mysqli_close($con);
stampa_piede("");

