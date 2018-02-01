<?php


session_start();

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

// @require_once("../php-ini".$_SESSION['suffisso'].".php");
@require_once("../lib/funzioni.php");

@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");

$_SESSION["annoscol"] = $annoscol; //prende la variabile presente nella sessione
$_SESSION['versione'] = $versioneprecedente;
// istruzioni per tornare alla pagina di login se non c'� una sessione valida
$goback = goBackRiepilogoRegistro();

$idclasse = stringa_html('idclasse');
$tipo = stringa_html('tipo');
$gio = stringa_html('gio');
$meseanno = stringa_html('mese'); // In effetti contiene sia il mese che l'anno
//$mese = substr($meseanno, 0, 2);
//$anno = substr($meseanno, 5, 4);
$visualizzabili = array("image/jpeg", "application/pdf", "image/pjpeg", "image/gif", "image/png");

$titolo = "Visualizzazione programmi svolti";
$script = "";
stampa_head($titolo, "", $script, "SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a>$goback[1] - $titolo", "", "$nome_scuola", "$comune_scuola");

// scelta classe
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));

//$gotoPage = $_SERVER['PHP_SELF'];
print ("
         <form method='post' action='visdocumenticlasse.php' name='docclassi'>
         <input type='hidden' name='idclasse' value='$idclasse'>
         <input type='hidden' name='goback' value='" . $goback[0] . "'>
         <input type='hidden' name='gio' value='$gio'>
         <input type='hidden' name='meseanno' value='$meseanno'>

          <p align='center'>
         <table align='center'>");
print("
        <tr>
        <td width='50%'><b>Tipo</b></p></td>
        <td width='50%'>
        <SELECT NAME='tipo' ONCHANGE='docclassi.submit()'><option value=''>&nbsp;</option>  ");

//
//  Riempimento combobox delle tbl_classi
//
$query = "SELECT * FROM tbl_tipidocumenti ORDER BY descrizione";
$ris = mysqli_query($con, inspref($query));
while ($nom = mysqli_fetch_array($ris))
{
    print "<option value='";
    print ($nom["idtipodocumento"]);
    print "'";
    if ($tipo == $nom["idtipodocumento"])
    {
        print " selected";
    }
    print ">";
    print ($nom["descrizione"]);

}

print ("</select></td></tr></table></form>");


// visualizzazione programmi
//$maxcomp=20;


if ($tipo == "")
{
    $query = "select doctype,tbl_documenti.iddocumento, tbl_documenti.descrizione,tbl_documenti.datadocumento, tbl_tipidocumenti.descrizione as tipo
			  from tbl_documenti,tbl_tipidocumenti
			  where tbl_documenti.idtipodocumento=tbl_tipidocumenti.idtipodocumento
			  and tbl_documenti.idtipodocumento<1000000000
			  and idclasse = $idclasse
			  order by datadocumento desc";
}
else
{
    $query = "select doctype, tbl_documenti.iddocumento, tbl_documenti.descrizione,tbl_documenti.datadocumento,tbl_tipidocumenti.descrizione as tipo
			  from tbl_documenti,tbl_tipidocumenti
			  where tbl_documenti.idtipodocumento=tbl_tipidocumenti.idtipodocumento
			  and tbl_documenti.idtipodocumento=$tipo
			  and idclasse = $idclasse
			  order by datadocumento desc";

}
//print inspref($query);
$ris = mysqli_query($con, inspref($query)) or die ("Errore: " . inspref($query));
if (mysqli_num_rows($ris)>0)
{
    print ("
		<p align='center'>
		<table align='center' border='1'>
		<tr class='prima'>
			<td><b>Documento</b></td>
			<td><b>Tipo</b></td>
			<td><b>Data</b></td>
			<td><b>Azione</b></td>");
    while ($nom = mysqli_fetch_array($ris))
    {


        print "<tr><td>" . $nom['descrizione'] . "</td>";
        print "<td>" . $nom['tipo'] . "</td>";
        print "<td>" . data_italiana($nom['datadocumento']) . "</td>";
        print ("<td> ");
        echo "<a href='actionsdocum.php?action=download&Id=" . $nom["iddocumento"] . "' target='_blank'><img src='../immagini/download.jpg' alt='scarica'></a> ";

        if (in_array($nom["doctype"], $visualizzabili))
        {
            echo "  <a href='actionsdocum.php?action=view&Id=" . $nom["iddocumento"] . "' ";
            echo "target='_blank'><img src='../immagini/view.jpg' alt='visualizza'></a>  ";
        }

        print "</td>";
        print"</tr>";


    }

    print "</table>";
}
else
{
    print "<center><b><br>Nessun documento presente!</b>";
}

mysqli_close($con);
stampa_piede("", false);


