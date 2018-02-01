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
$idclasse = 0;
$idclasse = stringa_html('idclasse');

$idalu = stringa_html('idalu');

$titolo = "Esportazione programmazione";
$script = "";


stampa_head($titolo,"",$script,"SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> -  $titolo", "", "$nome_scuola", "$comune_scuola");

$annoscolastico = $annoscol . "/" . ($annoscol + 1);

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));

print "<center><b>Esportazione programmazione</b></center><br/><br/>";

$cattedra = stringa_html('cattedra');
$idmateria="" ;
$idclasse="" ;
$iddocente=$_SESSION["idutente"] ;
print ("
   <form method='post' action='esportaprogrammazioneincsv.php' name='comp'>

   <p align='center'>
   <table align='center'>
   <tr>
      <td width='50%'><p align='center'><b>Cattedra</b></p></td>
      <td width='50%'>
      <SELECT ID='cattedra' NAME='cattedra' ONCHANGE='comp.submit()'> <option value=''>&nbsp ");


$con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));

$query="select idcattedra,tbl_classi.idclasse, anno, sezione, specializzazione, denominazione,tbl_materie.idmateria from tbl_cattnosupp, tbl_classi, tbl_materie where iddocente=$iddocente and tbl_cattnosupp.idclasse=tbl_classi.idclasse and tbl_cattnosupp.idmateria = tbl_materie.idmateria and idalunno=0 order by anno, sezione, specializzazione, denominazione";

$ris=mysqli_query($con,inspref($query));
while($nom=mysqli_fetch_array($ris))
{
    print "<option value='";
    print ($nom["idcattedra"]);
    print "'";
    if ($cattedra==$nom["idcattedra"])
    {
        print " selected";
        $idmateria=$nom["idmateria"];
        $idclasse=$nom["idclasse"];
    }
    print ">";

    print ($nom["anno"]);

    print "&nbsp;";
    print($nom["sezione"]);
    print "&nbsp;";
    print($nom["specializzazione"]);
    print "&nbsp;-&nbsp;";
    print($nom["denominazione"]);

}

print("
      </SELECT>
      </td></tr></table></form>");



if ($idmateria!="" && $idclasse!="")
{
    // Programmazione docente


    $esisteprogrammazione=false;
    $nf = "prog_".$idmateria."_".$idclasse."_" . $_SESSION['suffisso'] . ".csv";
    $nomefile = "$cartellabuffer/" . $nf;
    $fp = fopen($nomefile, 'w');

    fputcsv($fp, array("tipo", "sintesi", "descrizione", "obmin"), ";");


    $querycomp = "select * from tbl_competdoc where idclasse=$idclasse and idmateria=$idmateria order by numeroordine";
    $riscomp = mysqli_query($con,inspref($querycomp)) or die ("Errore: ".inspref($querycomp,false));

    while($reccomp=mysqli_fetch_array($riscomp))
    {
        $esisteprogrammazione=true;
        fputcsv($fp,array("COMP",$reccomp['sintcomp'],$reccomp['competenza'],""), ";");
        $idcompetenza=$reccomp['idcompetenza'];
        $queryabil = "select * from tbl_abildoc where idcompetenza=$idcompetenza order by abil_cono,numeroordine";
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
        print ("<br/><center>Apri programmazione cattedra:<a href='$cartellabuffer/$nf'><img src='../immagini/csv.png'></a></center>");
    else
        print ("<br/><center>Non c'è programmazione per la cattedra!</center>");


    // Programmazione istituto

    $anno=decodifica_anno_classe($idclasse,$con);
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



