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
$iddocente = $_SESSION["idutente"];
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}
$iddocprog = 0;

$titolo = "Visualizzazione programmazione";
$script = "<script type='text/javascript'>
         <!--
               var stile = 'top=10, left=10, width=1024, height=400, status=no, menubar=no, toolbar=no, scrollbars=yes';
               function Popup(apri) 
               {
                  window.open(apri, '', stile);
               }
         //-->
         </script>";

stampa_head($titolo,"",$script,"SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");


$cattedra = stringa_html('cattedra');

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));

if ($tipoutente != 'P')
{
    if ($_SESSION['sostegno'])
    {
        $query = "select idcattedra,tbl_cattnosupp.idalunno,tbl_classi.idclasse, anno, sezione, specializzazione, denominazione
					from tbl_cattnosupp, tbl_classi, tbl_materie, tbl_tipoprog 
					where iddocente=$iddocente 
					and tbl_cattnosupp.idclasse=tbl_classi.idclasse 
					and tbl_cattnosupp.idmateria = tbl_materie.idmateria 
					and tbl_cattnosupp.idmateria = tbl_tipoprog.idmateria
					and tbl_cattnosupp.idalunno = tbl_tipoprog.idalunno
					and tbl_tipoprog.tipoprogr='P'
					order by tbl_cattnosupp.idalunno, denominazione";
    }
    else
    {
        $query = "select idcattedra,tbl_cattnosupp.idalunno,tbl_classi.idclasse, anno, sezione, specializzazione, denominazione
					from tbl_cattnosupp, tbl_classi, tbl_materie, tbl_tipoprog 
					where tbl_cattnosupp.idclasse=tbl_classi.idclasse 
					and tbl_cattnosupp.idmateria = tbl_materie.idmateria 
					and tbl_cattnosupp.idalunno<>0
					and tbl_cattnosupp.idmateria = tbl_tipoprog.idmateria
					and tbl_cattnosupp.idalunno = tbl_tipoprog.idalunno
					and tbl_tipoprog.tipoprogr='P'
					and tbl_classi.idclasse in (select idclasse from tbl_cattnosupp where iddocente=$iddocente)
					and tbl_materie.idmateria in (select idmateria from tbl_cattnosupp where iddocente=$iddocente)
					order by tbl_cattnosupp.idalunno, denominazione";
    }
}
else
{
    $query = "SELECT idcattedra,tbl_cattnosupp.idalunno,tbl_classi.idclasse, anno, sezione, specializzazione, denominazione
					FROM tbl_cattnosupp, tbl_classi, tbl_materie, tbl_tipoprog
					WHERE tbl_cattnosupp.idclasse=tbl_classi.idclasse
					AND tbl_cattnosupp.idmateria = tbl_materie.idmateria
					AND tbl_cattnosupp.idalunno<>0
					AND tbl_cattnosupp.idmateria = tbl_tipoprog.idmateria
					AND tbl_cattnosupp.idalunno = tbl_tipoprog.idalunno
					AND tbl_tipoprog.tipoprogr='P'
					ORDER BY tbl_cattnosupp.idalunno, denominazione";
}


$ris = mysqli_query($con, inspref($query)) or die ("Errore:" . inspref($query));

if (mysqli_num_rows($ris) > 0)
{
    print ("
						<form method='post' action='visprogralu.php' name='comp'>
   
							<p align='center'>
						<table align='center'>
						<tr>
							<td width='50%'><p align='center'><b>Cattedra</b></p></td>
								<td width='50%'>
								<SELECT ID='cattedra' NAME='cattedra' ONCHANGE='comp.submit()'> <option value=''>&nbsp ");


    while ($nom = mysqli_fetch_array($ris))
    {
        print "<option value='";
        print ($nom["idcattedra"]);
        print "'";
        if ($cattedra == $nom["idcattedra"])
        {
            print " selected";
        }
        print ">";
        print (estrai_dati_alunno($nom['idalunno'], $con));
        print "&nbsp;-&nbsp;";
        print($nom["denominazione"]);

    }

    print("
      </SELECT>
      </td></tr></table></form>");
}
else

{
    print "<br><br><center><b>Nessuna cattedra per alunni con programma personalizzato!</b></center><br>";
}


if ($cattedra != "")
{

    if ($tipoutente == 'P')
    {
        $query = "select iddocente from tbl_cattnosupp where idcattedra=$cattedra";

        $risdoc = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
        $val = mysqli_fetch_array($risdoc);
        $iddocprog = $val['iddocente'];

    }


    $con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));


    if ($tipoutente == 'P')
    {
        $query = "select * from tbl_docenti where iddocente=$iddocprog";
    }
    else
    {
        $query = "select * from tbl_docenti where iddocente=$iddocente";
    }
    $ris = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
    if ($val = mysqli_fetch_array($ris))
    {
        $cognome = $val["cognome"];
        $nome = $val["nome"];
    }
    $query = "select idcattedra,idalunno,tbl_classi.idclasse, anno, sezione, specializzazione, denominazione from tbl_cattnosupp, tbl_classi, tbl_materie where idcattedra=$cattedra and tbl_cattnosupp.idclasse=tbl_classi.idclasse and tbl_cattnosupp.idmateria = tbl_materie.idmateria order by anno, sezione, specializzazione, denominazione";
    $ris = mysqli_query($con, inspref($query));
    if ($val = mysqli_fetch_array($ris))
    {
        $materia = ($val["denominazione"]);
        $classe = $val["anno"] . " " . $val["sezione"] . " " . $val["specializzazione"];
        $alunno = estrai_dati_alunno($val["idalunno"], $con);
        $idalunno = $val["idalunno"];
    }
    print "<center>Programmazione personalizzata: <br/>";
    print "Alunno: $alunno<br/>";
    print "Materia: $materia<br/>";
    print "Docente: $nome $cognome";

    print "</center>";


    $idmateria = estrai_id_materia($cattedra, $con);
    $idclasse = estrai_id_classe($cattedra, $con);


    $query = "select * from tbl_competalu where idmateria=$idmateria and idalunno=$idalunno order by numeroordine";
    $ris = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));

    if (mysqli_num_rows($ris) > 0)
    {
        print "<font size=2>";
        while ($val = mysqli_fetch_array($ris))
        {


            $numord = $val["numeroordine"];
            $sintcomp = $val["sintcomp"];
            $competenza = $val["competenza"];
            $idcompetenza = $val["idcompetenza"];
            print "<br/><br/><b>$numord. $sintcomp</b><br>  $competenza";

            $query = "select * from tbl_abilalu where idcompetenza=$idcompetenza and abil_cono='C' order by numeroordine";
            $risabil = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
            print "<font size=1>";
            while ($valabil = mysqli_fetch_array($risabil))
            {
                $sintabil = $valabil["sintabilcono"];
                $numordabil = $valabil["numeroordine"];
                $abilita = $valabil["abilcono"];
                $obminimi = $valabil["obminimi"];
                // if ($numordabil==1) print "<br/><b><big><center>CONOSCENZE</center></big></b>";
                if (!$obminimi)
                {
                    print "<br/><b>C $numord.$numordabil $sintabil</b><br> $abilita";
                }
                else
                {
                    print "<br/><i><b>C $numord.$numordabil $sintabil</b><br> $abilita</i>";
                }
            }

            $query = "select * from tbl_abilalu where idcompetenza=$idcompetenza and abil_cono='A' order by numeroordine";
            $risabil = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));

            while ($valabil = mysqli_fetch_array($risabil))
            {
                $sintabil = $valabil["sintabilcono"];
                $numordabil = $valabil["numeroordine"];
                $abilita = $valabil["abilcono"];
                $obminimi = $valabil["obminimi"];
                // if ($numordabil==1) print "<br/><b><big><center>ABILITA'</center></big></b>";
                if (!$obminimi)
                {
                    print "<br/><b>A $numord.$numordabil $sintabil</b><br> $abilita";
                }
                else
                {
                    print "<br/><i><b>A $numord.$numordabil $sintabil</b><br> $abilita</i>";
                }
            }
            print "</font>";
        }
        print "<br/><br/>(Le voci in <i>corsivo</i> fanno parte degli obiettivi minimi)";

        print "</font>";

        print"<br/><center><a href=javascript:Popup('staprogralu.php?cattedra=$cattedra')><img src='../immagini/stampa.png'></a><br/><br/>";
    }
    else
    {
        print"<br/><center><b>Non c'è ancora una programmazione personalizzata per l'alunno.<br>
		                      Importarla da programmazione esistente o definirla.</b></center><br/>";
    }
}

mysqli_close($con);
stampa_piede(""); 

