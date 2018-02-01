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

$titolo = "Esportazione programmazione scolastica";
$script = "";


stampa_head($titolo,"",$script,"SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> -  $titolo", "", "$nome_scuola", "$comune_scuola");

$annoscolastico = $annoscol . "/" . ($annoscol + 1);

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));

print "<center><b>Esportazione programmazione scolastica</b></center><br/><br/>";

$cattedra = stringa_html('cattedra');
$idmateria=stringa_html('idmateria') ;
$anno=stringa_html('anno') ;

print ("
   <form method='post' action='esportaprogrammazionescolincsv.php' name='comp'>

   <p align='center'>
   <table align='center'>
   <tr>
      <td width='50%'><p align='center'><b>Materia</b></p></td>
      <td width='50%'>
      <SELECT NAME='idmateria' ONCHANGE='comp.submit()'> <option>&nbsp ");



$con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));

$query="select idmateria, denominazione from tbl_materie order by denominazione";
$ris=mysqli_query($con,inspref($query));
while($nom=mysqli_fetch_array($ris))
{
    print "<option value='";
    print ($nom["idmateria"]);
    print "'";
    if ($idmateria==$nom["idmateria"])
     print " selected";
    print ">";
    print ($nom["denominazione"]);

}

print("
</SELECT>
      </td></tr>

      <tr>
      <td width='50%'><p align='center'><b>Anno</b></p></td>");

//
//   Inizio visualizzazione Anno
//



print("<td>   <select name='anno' ONCHANGE='comp.submit()'><option value=''>&nbsp;");
for($a=1;$a<=($numeroanni);$a++)
{
    if ($a==$anno)
        print("<option selected>$a");
    else
     print("<option value='$a'>$a");
}
echo("</select>");




print("    </form></td></tr><tr></table></form>");



if ($idmateria!="" && $anno!="")
{

    // Programmazione istituto


    $esisteprogrammazione=false;
    $nf = "prog_".$idmateria."_anno_".$anno."_" . $_SESSION['suffisso'] . ".csv";
    $nomefile = "$cartellabuffer/" . $nf;
    $fp = fopen($nomefile, 'w');

    fputcsv($fp, array("tipo", "sintesi", "descrizione", "obmin"), ";");


    $querycomp = "select * from tbl_competscol where anno=$anno and idmateria=$idmateria order by numeroordine";
    $riscomp = mysqli_query($con,inspref($querycomp)) or die ("Errore: ".inspref($querycomp,false));

    while($reccomp=mysqli_fetch_array($riscomp))
    {
        $esisteprogrammazione=true;
        fputcsv($fp,array("COMP",$reccomp['sintcomp'],$reccomp['competenza'],""), ";");
        $idcompetenza=$reccomp['idcompetenza'];
        $queryabil = "select * from tbl_abilscol where idcompetenza=$idcompetenza order by abil_cono,numeroordine";
        $risabil = mysqli_query($con,inspref($queryabil)) or die ("Errore: ".inspref($queryabil,false));
        while($recabil=mysqli_fetch_array($risabil))
        {
            $tipo="";
            $obmin="";
            if ($recabil['abil_cono']=='A')
                $tipo="ABIL";
            else
                $tipo="CONO";
            if ($recabil['obminimi'])
                $obmin="1";

            fputcsv($fp,array($tipo,$recabil['sintabilcono'],$recabil['abilcono'],$obmin), ";");
        }


    }
    fclose($fp);
    if ($esisteprogrammazione)
        print ("<br/><center>Apri programmazione scolastica:<a href='$cartellabuffer/$nf'><img src='../immagini/csv.png'></a></center>");
    else
        print ("<br/><center>Non c'è programmazione scolastica!</center>");

}

stampa_piede("");
mysqli_close($con);



