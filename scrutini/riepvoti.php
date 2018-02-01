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
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));

// istruzioni per tornare alla pagina di login se non c'è una sessione valida
////session_start();


// DEFINIZIONE ARRAY PER MEMORIZZAZZIONE IN CSV
$listamaterie = array();
$listamaterie[] = "Alunno";


$periodo = stringa_html('periodo');
$idclasse = stringa_html('cl');

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}


//
//    Parte iniziale della pagina
//

$titolo = "Tabellone scrutini";
$script = "<script type='text/javascript'>
         <!--
               var stile = 'top=10, left=10, width=1024, height=400, status=no, menubar=no, toolbar=no, scrollbars=yes';
               
               window.onload=function(){
                  nascondi();
               };
               
               function mostra() {
                    document.getElementById('sostituzioni').style.display='block';
                    document.getElementById('mv').disabled=true;
                    document.getElementById('nv').disabled=false;
				}

				function nascondi() {
				    document.getElementById('sostituzioni').style.display='none';
				    document.getElementById('nv').disabled=true;
                    document.getElementById('mv').disabled=false;
				}
               function Popup(apri) 
               {
                  window.open(apri, '', stile);
               }
               
               function stampaA4(alunno)
               {
                  datast=document.getElementById('datastampa').value;
                  firmadir=document.getElementById('firmadirigente').value;
                  gioass=document.getElementById('gioass').value;
                  link='stampaschedealu.php?classe=$idclasse&periodo=$periodo&firma='+firmadir+'&data='+datast+'&gioass='+gioass;
                  if (alunno!='')
                     link='stampaschedealu.php?periodo=$periodo&firma='+firmadir+'&data='+datast+'&gioass='+gioass+'&idalunno='+alunno;
                  // document.location.href=link;
                  window.open(link);
				}
               
               function stampaSEP()
               {
                  datast=document.getElementById('datastampa').value;
                  firmadir=document.getElementById('firmadirigente').value;
                  link='stampaschedeseparatefin.php?classe=$idclasse&periodo=1&firma='+firmadir+'&data='+datast;
                  // document.location.href=link;
                  window.open(link);
			   }
               
               function stampaTAB()
               {
                  datast=document.getElementById('datastampa').value;
                  firmadir=document.getElementById('firmadirigente').value;
                  
                  link='stampatabvoti.php?classe=$idclasse&periodo=$periodo&firma='+firmadir+'&data='+datast;
                  // document.location.href=link;
                  window.open(link);
					}
               
               function stampaVERB()
               {
                  datast=document.getElementById('datastampa').value;
                  firmadir=document.getElementById('firmadirigente').value;
                  
                  link='stampaverbaleint.php?classe=$idclasse&periodo=$periodo';
                  // document.location.href=link;
                  window.open(link);
			   }
               
               jQuery(function($){
				$.datepicker.regional['it'] = {
					clearText: 'Svuota', clearStatus: 'Annulla',
					closeText: 'Chiudi', closeStatus: 'Chiudere senza modificare',
					prevText: '&#x3c;Prec', prevStatus: 'Mese precedente',
					prevBigText: '&#x3c;&#x3c;', prevBigStatus: 'Mostra l\'anno precedente',
					nextText: 'Succ&#x3e;', nextStatus: 'Mese successivo',
					nextBigText: '&#x3e;&#x3e;', nextBigStatus: 'Mostra l\'anno successivo',
					currentText: 'Oggi', currentStatus: 'Mese corrente',
					monthNames: ['Gennaio','Febbraio','Marzo','Aprile','Maggio','Giugno',
					'Luglio','Agosto','Settembre','Ottobre','Novembre','Dicembre'],
					monthNamesShort: ['Gen','Feb','Mar','Apr','Mag','Giu',
					'Lug','Ago','Set','Ott','Nov','Dic'],
					monthStatus: 'Seleziona un altro mese', yearStatus: 'Seleziona un altro anno',
					weekHeader: 'Sm', weekStatus: 'Settimana dell\'anno',
					dayNames: ['Domenica','Luned&#236','Marted&#236','Mercoled&#236','Gioved&#236','Venerd&#236','Sabato'],
					dayNamesShort: ['Dom','Lun','Mar','Mer','Gio','Ven','Sab'],
					dayNamesMin: ['Do','Lu','Ma','Me','Gio','Ve','Sa'],
					dayStatus: 'Usa DD come primo giorno della settimana', dateStatus: '\'Seleziona\' D, M d',
					dateFormat: 'dd/mm/yy', firstDay: 1,
					initStatus: 'Scegliere una data', isRTL: false};
				$.datepicker.setDefaults($.datepicker.regional['it']);
			});

						   
			$(document).ready(function(){
				 $('#dataverbale').datepicker({ dateFormat: 'dd/mm/yy' });
			 });
			 
			 
			 $(document).ready(function(){
					$('#orainizio').datetimepicker({
						formatTime: 'H:i',
						format: 'H:i',
						step: 5,
						datepicker:false
					});
			});
			
			$(document).ready(function(){
					$('#orafine').datetimepicker({
						formatTime: 'H:i',
						format: 'H:i',
						step: 5,
						datepicker:false
 
					});
			});
			 
         //-->
         </script>";

stampa_head($titolo, "", $script, "SDMAP");

stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");


// $nome = stringa_html('cl');  

// $periodo = stringa_html('periodo');
//$anno = stringa_html('anno');
$idscrutinio = 0;

// $idclasse = stringa_html('cl');
$id_ut_doc = $_SESSION["idutente"];

//if ($giorno=='')
//   $giorno=date('d');
//if ($mese=='')
//   $mese=date('m');
//if ($anno=='')
//   $anno=date('Y');


print ('
         <form method="post" action="riepvoti.php" name="voti">
   
         <p align="center">
         <table align="center">');


//
//   Inizio visualizzazione del combo box del periodo
//
if ($numeroperiodi == 2)
{
    print('<tr><td width="50%"><b>Quadrimestre</b></td>');
}
else
{
    print('<tr><td width="50%"><b>Trimestre</b></td>');
}

echo('   <td width="50%">');
echo('   <select name="periodo" ONCHANGE="voti.submit()">');

if ($periodo == '1')
{
    echo("<option selected value='1'>Primo</option>");
}
else
{
    echo("<option value='1'>Primo</option>");
}
if ($periodo == '2')
{
    echo("<option selected value='2'>Secondo</option>");
}
else
{
    echo("<option value='2'>Secondo</option>");
}

if ($numeroperiodi == 3)
{
    if ($periodo == '3')
    {
        echo("<option selected value='3'>Terzo</option>");
    }
    else
    {
        echo("<option value='3'>Terzo</option>");
    }
}


echo("</select>");
echo("</td></tr>");


//
//  Fine visualizzazione del quadrimestre
//


//
//   Classi
//

print('
        <tr>
        <td width="50%"><b>Classe</b></td>
        <td width="50%">
        <SELECT ID="cl" NAME="cl" ONCHANGE="voti.submit()"><option value=""></option>  ');

//
//  Riempimento combobox delle tbl_classi
//
$coordinatore = false;
if ($tipoutente == "S" | $tipoutente == "P" | $tipoutente == "A")
{
    $query = "SELECT DISTINCT tbl_classi.idclasse,anno,sezione,specializzazione FROM tbl_classi ORDER BY anno,sezione,specializzazione";
}
else
{
    $query = "SELECT DISTINCT tbl_classi.idclasse,anno,sezione,specializzazione FROM tbl_classi
           WHERE idcoordinatore=" . $_SESSION['idutente'] . " ORDER BY anno,sezione,specializzazione";
    $coordinatore = true;
}

// $query="select distinct tbl_classi.idclasse,anno,sezione,specializzazione from tbl_classi order by anno,sezione,specializzazione";
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
}

print ("</select></td></tr></table></form>");


if ($idclasse != "")
{

    if ($tipoutente != "A")
    {


        // VERIFICO SE E' POSSIBILE IMPORTARE PROPOSTE

        $query = "SELECT * FROM tbl_proposte,tbl_alunni
			where tbl_proposte.idalunno=tbl_alunni.idalunno
			and idclasse=$idclasse
			and periodo=$periodo";
        //print inspref($query);
        $ris = mysqli_query($con, inspref($query));
        $numproposte = mysqli_num_rows($ris);

        $query = "SELECT * FROM tbl_valutazionifinali,tbl_alunni
			where tbl_valutazionifinali.idalunno=tbl_alunni.idalunno
			and idclasse=$idclasse
			and periodo=$periodo";
        $ris = mysqli_query($con, inspref($query));
        $numvalutazioni = mysqli_num_rows($ris);
        //print inspref($query);


        // INSERISCO LO SCRUTINIO SE NON E' PRESENTE


        $queryver = "SELECT * FROM tbl_scrutini WHERE idclasse=$idclasse AND periodo='$periodo'";
        $risver = mysqli_query($con, inspref($queryver)) or die ("Errore nella query: " . mysqli_error($con));
        if (!$valver = mysqli_fetch_array($risver))
        {
            $testo1 = estrai_testo('verbscrutint01', $con);
            $testo2 = estrai_testo('verbscrutint02', $con);
            $testo3 = estrai_testo('verbscrutint03', $con);
            $testo4 = estrai_testo('verbscrutint04', $con);
            $queryscr = "INSERT into tbl_scrutini(idclasse,periodo,datascrutinio,stato,testo1,testo2,testo3,testo4)
						VALUES ($idclasse,$periodo,'" . date("Y-m-d") . "','A','$testo1','$testo2','$testo3','$testo4')";
            $risscr = mysqli_query($con, inspref($queryscr)) or print ("<br><div style=\"text-align: center;\"><b>Errore" . inspref($queryscr));
            $idscrutinio = mysqli_insert_id($con);
            $datascrutinio = date("Y-m-d");
        }
        else
        {
            $idscrutinio = $valver['idscrutinio'];
            $orainizioscrutinio = $valver['orainizioscrutinio'];
            $orafinescrutinio = $valver['orafinescrutinio'];
            $dataverbale = $valver['dataverbale'];
            $luogoscrutinio = $valver['luogoscrutinio'];
            $sostituzioni = $valver['sostituzioni'];
            $segretario = $valver['segretario'];
            $datascrutinio = $valver['datascrutinio'];
            $testo1 = $valver['testo1'];
            $testo2 = $valver['testo2'];
            $testo3 = $valver['testo3'];
            $testo4 = $valver['testo4'];
            if ($testo1 == "") $testo1 = estrai_testo('verbscrutint01', $con);
            if ($testo2 == "") $testo2 = estrai_testo('verbscrutint02', $con);
            if ($testo3 == "") $testo3 = estrai_testo('verbscrutint03', $con);
            if ($testo4 == "") $testo4 = estrai_testo('verbscrutint04', $con);

        }

        // Riempio l'elenco degli alunni presenti nella classe al momento dello scrutinio
        // TTTT Decidere se usare datascrutinio o datafinequadrimestre
        $elencoalunni = estrai_alunni_classe_data($idclasse, $fineprimo, $con);
        // print $elencoalunni;

        if ($numproposte > 0 and $numvalutazioni == 0)
        {
            // IMPORTO LE PROPOSTE PER LA CLASSE

            $queryins = "INSERT into tbl_valutazionifinali(idalunno,idmateria,votounico,votoscritto,votoorale,votopratico,assenze,periodo)
								  SELECT tbl_proposte.idalunno,idmateria,unico,scritto,orale,pratico,assenze,periodo from tbl_proposte,tbl_alunni 
								  where tbl_proposte.idalunno=tbl_alunni.idalunno
								  and idclasse=$idclasse and periodo=$periodo";

            $risins = mysqli_query($con, inspref($queryins)) or die(mysqli_error($con));
            print "<br><div style=\"text-align: center;\"><b>Voti importati dalle proposte di voto!</b></div><br>";
            // CALCOLO IL VOTO DI CONDOTTA PER TUTTI GLI ALUNNI
            //$query = "SELECT idalunno FROM tbl_alunni WHERE idclasse=$idclasse";
            $query = "SELECT idalunno FROM tbl_alunni WHERE idalunno in ($elencoalunni)";
            $ris = mysqli_query($con, inspref($query)) or die(mysqli_error($con));
            while ($nom = mysqli_fetch_array($ris))
            {
                $idal = $nom['idalunno'];
                $queryins = "INSERT into tbl_valutazionifinali(idalunno,idmateria,votounico,periodo)
							 VALUES ($idal,-1," . calcola_media_condotta($idal, $periodo, $con) . ",$periodo)";
                $risins = mysqli_query($con, inspref($queryins)) or die(mysqli_error($con));
            }

        }

        $alunni = array();
        $mattipo = array();
        $valutazioni = array();
        $annotazioni = array();
        // RICHIAMO LA FUNZIONE PER LA CREAZIONE DEL FILE CSV
        $numerovalutazioni = creaFileCSV($idclasse, $periodo, $elencoalunni, $alunni, $mattipo, $valutazioni, $con,$annotazioni);

        $query = "SELECT distinct tbl_materie.idmateria,sigla,tipovalutazione FROM tbl_cattnosupp,tbl_materie
		          WHERE tbl_cattnosupp.idmateria=tbl_materie.idmateria
		          and tbl_cattnosupp.idclasse=$idclasse
                  and tbl_cattnosupp.iddocente <> 1000000000
		          order by tbl_materie.progrpag,tbl_materie.sigla";
        $ris = mysqli_query($con, inspref($query));
        if (mysqli_num_rows($ris) > 0)
        {
            print ("<table align='center' border='1'><tr class='prima' align='center'><td>Alunno</td><td>Scrut.</td>");
            $tottipoval = "";
            while ($nom = mysqli_fetch_array($ris))
            {
                print ("<td>");
                print ($nom["sigla"]);
                $codmat[] = $nom["idmateria"];
                $sigmat[] = $nom["sigla"];
                $listamaterie[] = $nom["sigla"];
                $tipoval[] = $nom["tipovalutazione"];
                $tottipoval = $tottipoval . $nom["tipovalutazione"];
                print ("</td>");

            }

            // INSERISCO LA CONDOTTA
            print ("<td>");
            print "COMPO";
            $codmat[] = -1;
            $sigmat[] = "COMPO";
            $listamaterie[] = "COMPO";
            $tipoval[] = "TU";
            $tottipoval = $tottipoval . "TU";
            print ("</td></tr>");


            //if (strpos($tottipoval,"S")!=false | strpos($tottipoval,"P")!=false | strpos($tottipoval,"P")!=false) {
            print "<tr class='prima'><td></td><td></td>";
            for ($nummat = 0; $nummat < count($codmat); $nummat++)
            {
                print ("<td align='center'><table><tr>");
                if (strpos($tipoval[$nummat], "S") != false)
                {
                    print "<td>S</td>";
                }
                if (strpos($tipoval[$nummat], "O") != false)
                {
                    print "<td>O</td>";
                }
                if (strpos($tipoval[$nummat], "P") != false)
                {
                    print "<td>P</td>";
                }
                if (strpos($tipoval[$nummat], "U") != false)
                {
                    print "<td>U</td>";
                }
                print ("</tr></table></td>");
            }


            $numeroalunno = 0;


            //	$query='select * from tbl_alunni where idclasse="'.$idclasse.'" order by cognome,nome,datanascita';

            $query = "select * from tbl_alunni where idalunno in ($elencoalunni) order by cognome,nome,datanascita";
            $ris = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
            while ($val = mysqli_fetch_array($ris))
            {
                $listavoti = array();
                // $esiste_voto=false;
                $idalunno = $val["idalunno"];
                $numeroalunno++;
                if ($numeroalunno % 2 == 0)
                {
                    $colore = '#FFFFFF';
                }
                else
                {
                    $colore = '#CCCCCC';
                }
                echo "<tr bgcolor=$colore>";

                if (!$val['certificato'])
                {
                    $cert = "";
                }
                else
                {
                    $cert = "<img src='../immagini/apply_small.png'>";
                }

                echo "      <td><b>" . $val['cognome'] . " " . $val['nome'] . " " . data_italiana($val['datanascita']) . "$cert</b></td>";
           //     if ($tipoutente == "P" | $tipoutente == "S")
           //     {
                    echo "<td align='center'><a href='schedaalu.php?cl=$idclasse&idalunno=$idalunno&periodo=$periodo&prov=tab'><img src='../immagini/scrutinio.gif'></a>";
                    if (!scrutinio_aperto($idclasse, $periodo, $con))
                    {
                        print "<img src='../immagini/stampaA4.png' height='24' width='24' onclick='stampaA4($idalunno)'  onmouseover=$(this).css('cursor','pointer')>";
                    }
                    print "</td>";
           //     }
           //     else
           //     {
           //         echo "<td align='center'>&nbsp;</td>";
           //     }
                $listavoti[] = $val["cognome"] . " " . $val["nome"] . " " . data_italiana($val["datanascita"]);
                $contavoti = 0;
                $sommavoti = 0;
                for ($nummat = 0; $nummat < count($codmat); $nummat++)
                {
                    $cm = $codmat[$nummat];

                    $votounico = ricerca_voto($idalunno, $cm, "U", $alunni, $mattipo, $valutazioni);
                    $votoscritto = ricerca_voto($idalunno, $cm, "S", $alunni, $mattipo, $valutazioni);
                    $votoorale = ricerca_voto($idalunno, $cm, "O", $alunni, $mattipo, $valutazioni);
                    $votopratico = ricerca_voto($idalunno, $cm, "P", $alunni, $mattipo, $valutazioni);
                    $annotazione = ricerca_note($idalunno, $cm,$alunni, $mattipo, $annotazioni);
                    print "<td align='center'><table><tr>";
                    if (trim($annotazione)!="")
                    {
                        $sottolineatura = "";
                        $finesottolineatura = "<sup>*</sup>";
                    }
                    else
                    {
                        $sottolineatura = "";
                        $finesottolineatura = "";
                    }

                    if (strpos($tipoval[$nummat], "U") != false)
                    {
                        if (insufficiente($votounico))
                        {
                            $colore = "'red'";
                        }
                        else
                        {
                            $colore = "''";
                        }
                        print "<td align='center' bgcolor=$colore>$sottolineatura" . dec_to_vot($votounico) . "$finesottolineatura</td>";

                    }
                    if (strpos($tipoval[$nummat], "S") != false)
                    {
                        if (insufficiente($votoscritto))
                        {
                            $colore = "'red'";
                        }
                        else
                        {
                            $colore = "''";
                        }
                        print "<td align='center' bgcolor=$colore>$sottolineatura" . dec_to_vot($votoscritto) . "$finesottolineatura</td>";
                    }
                    if (strpos($tipoval[$nummat], "O") != false)
                    {
                        if (insufficiente($votoorale))
                        {
                            $colore = "'red'";
                        }
                        else
                        {
                            $colore = "''";
                        }
                        print "<td align='center' bgcolor=$colore>$sottolineatura" . dec_to_vot($votoorale) . "$finesottolineatura</td>";
                    }
                    if (strpos($tipoval[$nummat], "P") != false)
                    {
                        if (insufficiente($votopratico))
                        {
                            $colore = "'red'";
                        }
                        else
                        {
                            $colore = "''";
                        }
                        print "<td align='center' bgcolor=$colore>$sottolineatura" . dec_to_vot($votopratico) . "$finesottolineatura</td>";
                    }

                    print "</tr></table></td>";


                }
                print"</tr>";

            }
            print "</table>";
            if (scrutinio_aperto($idclasse, $periodo, $con))
            {
                $abilscr = '';
            }
            else
            {
                $abilscr = ' disabled';
            }
            print "<br><center><input type='button' id='mv' value='Mostra dati verbale' onclick='mostra()' />
				   <input type='button' id='nv' value='Nascondi dati verbale' onclick='nascondi()' /></center></div>
				   <style type='text/css'>
					   div.grigia { background-color: #bbbbbb;}
				   </style>
				   <div id='sostituzioni' class='grigia' style.display='none'>";
            print "<form name='registradativerbale' action='registradativerbale.php' method='post'>";
            print "<fieldset><legend>DATI VERBALE</legend>";
            print "<center>
						<input type='hidden' name='idscrutinio' value='$idscrutinio'>
						Data: <input type='text' name='dataverbale' value='" . data_italiana($dataverbale) . "' id='dataverbale' $abilscr>
						Ora inizio: <input type='text' name='orainizioscrutinio' value='" . substr($orainizioscrutinio, 0, 5) . "' id='orainizio' maxlength='5' size='5' $abilscr>
						Ora fine: <input type='text' name='orafinescrutinio' value='" . substr($orafinescrutinio, 0, 5) . "' id='orafine' maxlength='5' size='5' $abilscr>
						<br>Luogo (aula, ecc.): <input type='text' name='luogoscrutinio' value='$luogoscrutinio' maxlength='100' size='100' $abilscr>
						<br>Testo 1 scrutinio<br><textarea name='testo1' rows='5' cols='100'>" . $testo1 . "</textarea>
						<br>[ELENCO DOCENTI PRESENTI]
						<br>Testo 2 scrutinio<br><textarea name='testo2' rows='5' cols='100'>" . $testo2 . "</textarea>
						<br>[EVENTUALI ANNOTAZIONI SUI SINGOLI ALUNNI]
						<br>Testo 3 scrutinio<br><textarea name='testo3' rows='5' cols='100'>" . $testo3 . "</textarea>
						<br>Testo 4 scrutinio<br><textarea name='testo4' rows='5' cols='100'>" . $testo4 . "</textarea>
						<br>[ELENCO DOCENTI PER FIRMA]<br>";

            print "<table align='center' border='1'>";
            print "<tr class='prima'><td>Docente</td><td>Sostituito da</td><td>Segretario</td><td>Sost. da suppl.</td></tr>";
            $querydoc = "SELECT DISTINCT cognome,nome,iddocente FROM tbl_docenti
				WHERE iddocente=1000000000";
            $risdoc = mysqli_query($con, inspref($querydoc)) or die ("Errore nella query: " . inspref($querydoc, false));
            while ($recdoc = mysqli_fetch_array($risdoc))
            {
                print "<tr><td>" . $recdoc['cognome'] . " " . $recdoc['nome'] . "</td><td>";
                $querysost = "SELECT DISTINCT cognome,nome,iddocente FROM tbl_docenti
							WHERE iddocente<>" . $recdoc['iddocente'] . " ORDER BY cognome,nome";
                $rissost = mysqli_query($con, inspref($querysost)) or die ("Errore nella query: " . inspref($querysost, false));
                print "<select name='docsost" . $recdoc['iddocente'] . "' $abilscr><option value=''>&nbsp;</option>";
                while ($recsost = mysqli_fetch_array($rissost))
                {
                    if (strpos($sostituzioni, $recdoc['iddocente'] . "<" . $recsost['iddocente']) > 0)
                    {
                        print "<option value='" . $recsost['iddocente'] . "' selected>" . $recsost['cognome'] . " " . $recsost['nome'] . "</option>";
                    }
                    else
                    {
                        print "<option value='" . $recsost['iddocente'] . "'>" . $recsost['cognome'] . " " . $recsost['nome'] . "</option>";
                    }
                }
                print "</select>";
                print "</td>";
                if ($segretario != $recdoc['iddocente'])
                {
                    print "<td><input type='radio' name='segretario' value='" . $recdoc['iddocente'] . "'></td>";
                }
                else
                {
                    print "<td><input type='radio' name='segretario' value='" . $recdoc['iddocente'] . "' checked='checked'></td>";
                }
                print "</tr>";
            }
            $querydoc = "SELECT DISTINCT cognome,nome,tbl_docenti.iddocente FROM tbl_cattnosupp,tbl_docenti
				WHERE tbl_cattnosupp.iddocente=tbl_docenti.iddocente
				AND tbl_cattnosupp.idclasse=" . $idclasse . "
				AND tbl_cattnosupp.iddocente<>1000000000
				ORDER BY cognome,nome
				";
            $risdoc = mysqli_query($con, inspref($querydoc)) or die ("Errore nella query: " . inspref($querydoc, false));
            while ($recdoc = mysqli_fetch_array($risdoc))
            {
                print "<tr><td>" . $recdoc['cognome'] . " " . $recdoc['nome'] . "</td><td>";
                $querysost = "SELECT DISTINCT cognome,nome,iddocente FROM tbl_docenti
							WHERE iddocente<>" . $recdoc['iddocente'] . " ORDER BY cognome,nome";
                $rissost = mysqli_query($con, inspref($querysost)) or die ("Errore nella query: " . inspref($querysost, false));
                print "<select name='docsost" . $recdoc['iddocente'] . "' $abilscr><option value=''>&nbsp;</option>";
                while ($recsost = mysqli_fetch_array($rissost))
                {
                    if (strpos($sostituzioni, $recdoc['iddocente'] . "<" . $recsost['iddocente']) > 0)
                    {
                        print "<option value='" . $recsost['iddocente'] . "' selected>" . $recsost['cognome'] . " " . $recsost['nome'] . "</option>";
                    }
                    else
                    {
                        print "<option value='" . $recsost['iddocente'] . "'>" . $recsost['cognome'] . " " . $recsost['nome'] . "</option>";
                    }
                }
                print "</select>";
                print "</td>";
                if ($segretario != $recdoc['iddocente'])
                {
                    print "<td><input type='radio' name='segretario' value='" . $recdoc['iddocente'] . "'></td>";
                }
                else
                {
                    print "<td><input type='radio' name='segretario' value='" . $recdoc['iddocente'] . "' checked='checked'></td>";
                }
                if (strpos($sostituzioni, "§" . $recdoc['iddocente']) > -1)
                {
                    print "<td><input type='checkbox' name='suppl" . $recdoc['iddocente'] . "' value='" . $recdoc['iddocente'] . "' checked='checked'></td>";
                }
                else
                {
                    print "<td><input type='checkbox' name='suppl" . $recdoc['iddocente'] . "' value='" . $recdoc['iddocente'] . "'></td>";
                }

                print "</tr>";
            }
            print "</table>";
            print "<br><center><input type='submit' value='Registra dati per verbale' $abilscr>
				   </center>";

            print "</fieldset>";
            print "</form>";
            print "</div>";
            /*
            print "<br><center><a href='stampaschedealu.php?classe=$idclasse&periodo=$periodo'><img src='../immagini/stampa.png'></a>";
            $nf="scrut_".decodifica_classe($idclasse, $con)."_".$periodo.".csv";
            $nf=str_replace(" ","_",$nf);
            $nomefile="$cartellabuffer/".$nf;
            print ("&nbsp;&nbsp;&nbsp;<a href='$nomefile'><img src='../immagini/csv.png'></a></center>");
            */
            //
            //   STAMPE ED ESPORTAZIONE
            //
            print "<fieldset><legend>STAMPE</legend>";
            print "<div style=\"text-align: center;\"><br>Data stampa<input type='text' id='datastampa' size='10' maxlenght='10' name='datastampa' value='" . date('d/m/Y') . "'>";
            print "&nbsp;Firma dirigente<input type='text' id='firmadirigente' value='" . estrai_dirigente($con) . "'>&nbsp;
			       &nbsp;Giorni assenza nella scheda<select name='gioass' id='gioass'><option value='yes'>Sì</option><option value='no'>No</option></select>
				   <input type='hidden' name='votitab' value='yes'>
				   ";
            print "</div>";
            print "<br><div style=\"text-align: center;\"><img src='../immagini/stampaA4.png' onclick='stampaA4(\"\")'  onmouseover=$(this).css('cursor','pointer')>";
            print "&nbsp;&nbsp;&nbsp;<img src='../immagini/stampaVERB.png' onclick='stampaVERB()'  onmouseover=$(this).css('cursor','pointer')>";
            print "&nbsp;&nbsp;&nbsp;<img src='../immagini/stampaTAB.png' onclick='stampaTAB()'  onmouseover=$(this).css('cursor','pointer')>";
            print "&nbsp;&nbsp;&nbsp;<img src='../immagini/stampaSEP.png' onclick='stampaSEP()'  onmouseover=$(this).css('cursor','pointer')>";

            $nf = "scrut_" . decodifica_classe($idclasse, $con) . "_" . $periodo . ".csv";
            $nf = str_replace(" ", "_", $nf);
            $nomefile = "$cartellabuffer/" . $nf;
            print ("&nbsp;&nbsp;&nbsp;<a href='$nomefile' target='_blank'><img src='../immagini/csv.png'></a></div>");
            print "</fieldset>";
            // VERIFICO SE LO SCRUTINIO E' APERTO E IN TAL CASO PROPONGO LA CHIUSURA
            // ALTRIMENTI VISUALIZZO AVVISO DI VOTI MANCANTI
            if (scrutinio_aperto($idclasse, $periodo, $con))
            {
                //	if (scrutinio_completo($idclasse,$periodo,$numerovalutazioni,$con))
                //	{

                if ($tipoutente == "P" | $tipoutente == "S")
                {
                    print "<div style=\"text-align: center;\">";
                    print "<form name='chiudiscrutinio' action='chiudiscrutinio.php' method='post'>
							 <input type='hidden' name='idscrutinio' value='$idscrutinio'>
							 
							 <input type='submit' value='Chiudi scrutinio'></form>";

                    print "</div>";
                }
                //	}
                //	else
                //	{
                if (!scrutinio_completo($idclasse, $periodo, $numerovalutazioni, $con))
                {
                    print ("<p align=center><font color='red'>L'attribuzione dei voti è incompleta!</font></p>");
                }
                print "<div style=\"text-align: center;\">";
                print "<form name='ricaricaproposte' action='ricaricaproposte.php' method='post'>
							 <input type='hidden' name='idscrutinio' value='$idscrutinio'>
							 <br>ATTENZIONE! La reimportazione delle proposte annullerà eventuali modifiche apportate.<br>
							 <input type='submit' value='Ricarica proposte'></form>";

                print "</div>";
                //	}
            }
            else
            {
               /* $data_scrutinio = data_italiana(estrai_datascrutinio($idclasse, $periodo, $con));
                print ("<p align=center><b>Scrutinio chiuso in data: $data_scrutinio!</b></p>"); */
                print ("<p align=center><b>Scrutinio chiuso!</b></p>");
            }
        }
        else
        {
            print("<div style=\"text-align: center;\"><b><br>Nessun alunno presente!</b></div>");
        }
    }
    else  // TIPO UTENTE = "A"
    {
        if (scrutinio_aperto($idclasse, $periodo, $con))
        {
            print "<div style=\"text-align: center;\"><b><br>Scrutinio non ancora chiuso!</b></div>";
        }
        else
            
        {
            
            
            $elencoalunni = estrai_alunni_classe_data($idclasse, $fineprimo, $con);
            
        
            print ("<table align='center' border='1'><tr class='prima' align='center'><td>Alunno</td><td>Stampa</td></tr>");


           


            $numeroalunno = 0;


            //	$query='select * from tbl_alunni where idclasse="'.$idclasse.'" order by cognome,nome,datanascita';

            $query = "select * from tbl_alunni where idalunno in ($elencoalunni) order by cognome,nome,datanascita";
            $ris = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
            while ($val = mysqli_fetch_array($ris))
            {
                $listavoti = array();
                // $esiste_voto=false;
                $idalunno = $val["idalunno"];
                $numeroalunno++;
                if ($numeroalunno % 2 == 0)
                {
                    $colore = '#FFFFFF';
                }
                else
                {
                    $colore = '#CCCCCC';
                }
                echo "<tr bgcolor=$colore>";

                if (!$val['certificato'])
                {
                    $cert = "";
                }
                else
                {
                    $cert = "<img src='../immagini/apply_small.png'>";
                }

                echo "      <td><b>" . $val['cognome'] . " " . $val['nome'] . " " . data_italiana($val['datanascita']) . "$cert</b></td>";
           //     if ($tipoutente == "P" | $tipoutente == "S")
           //     {
                    echo "<td align='center'><img src='../immagini/stampaA4.png' height='24' width='24' onclick='stampaA4($idalunno)'  onmouseover=$(this).css('cursor','pointer')></td></tr>";
           //     }
           //     else
           //     {
           //         echo "<td align='center'>&nbsp;</td>";
           //     }
                


            }
                print "</table>";

        }
            
            
            
            
            
            
            
        print "<fieldset><legend>STAMPE</legend>";
        print "<div style=\"text-align: center;\"><br>Data stampa<input type='text' id='datastampa' size='10' maxlenght='10' name='datastampa' value='" . date('d/m/Y') . "'>";
        print "&nbsp;Firma dirigente<input type='text' id='firmadirigente' value='" . estrai_dirigente($con) . "'>&nbsp;
                               &nbsp;Giorni assenza nella scheda<select name='gioass' id='gioass'><option value='yes'>Sì</option><option value='no'>No</option></select>
                               <input type='hidden' name='votitab' value='yes'>
                               ";
        print "<br><center><img src='../immagini/stampaA4.png' onclick='stampaA4(\"\")'  onmouseover=$(this).css('cursor','pointer')>";
        print "&nbsp;&nbsp;&nbsp;<img src='../immagini/stampaVERB.png' onclick='stampaVERB()'  onmouseover=$(this).css('cursor','pointer')>";
        print "&nbsp;&nbsp;&nbsp;<img src='../immagini/stampaTAB.png' onclick='stampaTAB()'  onmouseover=$(this).css('cursor','pointer')>";
        print "&nbsp;&nbsp;&nbsp;<img src='../immagini/stampaSEP.png' onclick='stampaSEP()'  onmouseover=$(this).css('cursor','pointer')>";
        /*
        $nf="scrut_".decodifica_classe($idclasse, $con)."_".$numeroperiodi.".csv";
        $nf=str_replace(" ","_",$nf);
        $nomefile="$cartellabuffer/".$nf;
        print ("&nbsp;&nbsp;&nbsp;<a href='$nomefile' target='_blank'><img src='../immagini/csv.png'></a></center>");
         */
        print "</div>";
        print "</fieldset>";
        
    }
}

mysqli_close($con);
stampa_piede("");

function ricerca_voto($idalunno, $idmateria, $tipo, $alu, $mattipi, $valutaz)
{
    for ($i = 0; $i < count($valutaz); $i++)
    {

        if ($idalunno == $alu[$i] & ($idmateria . $tipo) == $mattipi[$i])
        {
            return $valutaz[$i];
        }
    }
    return 0;
}

function ricerca_note($idalunno, $idmateria, $alu, $mattipi, $note)
{
    for ($i = 0; $i < count($note); $i++)
    {

        if ($idalunno == $alu[$i] & ($idmateria == substr($mattipi[$i], 0, strlen($idmateria))))
        {
            return $note[$i];
        }
    }
    return "";
}

function ricerca_assenze($idalunno, $idmateria, $alu, $mattipi, $assenze)
{
    for ($i = 0; $i < count($assenze); $i++)
    {

        if ($idalunno == $alu[$i] & ($idmateria == substr($mattipi[$i], 0, strlen($idmateria))))
        {
            return $assenze[$i];
        }
    }
    return 0;
}


function creaFileCSV($idclasse, $periodo, $elencoalunni, &$alu, &$mattipo, &$valu, $conn,&$annot)
{
    //@require("../php-ini".$_SESSION['suffisso'].".php");
    global $cartellabuffer;
    global $plesso_specializzazione;
    global $fineprimo;
    global $finesecondo;
    global $numeroperiodi;

    $assenze = array();
    // $elencoalunni=alunni_classe_data($idclasse,$datascrutinio,$conn);
    // print $elencoalunni;
    $query = "SELECT tbl_valutazionifinali.*,tbl_materie.tipovalutazione FROM tbl_valutazionifinali,tbl_alunni,tbl_materie
	          WHERE tbl_valutazionifinali.idalunno=tbl_alunni.idalunno
	          AND tbl_valutazionifinali.idmateria=tbl_materie.idmateria
	          AND tbl_valutazionifinali.idalunno in ($elencoalunni)
	          AND periodo='$periodo'
	          ORDER BY idmateria";
    // print inspref($query);
    $risvalu = mysqli_query($conn, inspref($query)) or die(mysqli_error($conn));
    // print "Numero record:".mysqli_num_rows($risvalu);
    while ($recval = mysqli_fetch_array($risvalu))
    {


        if (strpos($recval['tipovalutazione'], "S") != false)
        {
            $alu[] = $recval['idalunno'];
            $mattipo[] = $recval['idmateria'] . "S";
            $valu[] = $recval['votoscritto'];
            $annot[] = $recval['note'];
            $assenze[] = $recval['assenze'];
        }
        if (strpos($recval['tipovalutazione'], "P") != false)
        {
            $alu[] = $recval['idalunno'];
            $mattipo[] = $recval['idmateria'] . "P";
            $valu[] = $recval['votopratico'];
            $annot[] = $recval['note'];
            $assenze[] = $recval['assenze'];
        }
        if (strpos($recval['tipovalutazione'], "O") != false)
        {
            $alu[] = $recval['idalunno'];
            $mattipo[] = $recval['idmateria'] . "O";
            $valu[] = $recval['votoorale'];
            $annot[] = $recval['note'];
            $assenze[] = $recval['assenze'];
        }
        if (strpos($recval['tipovalutazione'], "U") != false)
        {
            $alu[] = $recval['idalunno'];
            $mattipo[] = $recval['idmateria'] . "U";
            $valu[] = $recval['votounico'];
            $annot[] = $recval['note'];
            $assenze[] = $recval['assenze'];
        }

    }

    // for ($i=0;$i<count($mattipo);$i++)
    //     print "$i  $mattipo[$i]"."<br>";


    $nf = "scrut_" . decodifica_classe($idclasse, $conn) . "_" . $periodo . ".csv";
    $nf = str_replace(" ", "_", $nf);
    $nomefile = "$cartellabuffer/" . $nf;
    $fp = fopen($nomefile, 'w');
    $query = "SELECT distinct tbl_materie.idmateria,sigla,tipovalutazione FROM tbl_cattnosupp,tbl_materie
           WHERE tbl_cattnosupp.idmateria=tbl_materie.idmateria
           and tbl_cattnosupp.idclasse=$idclasse
           and tbl_cattnosupp.iddocente <> 1000000000
           order by tbl_materie.progrpag,tbl_materie.sigla";
    $ris = mysqli_query($conn, inspref($query));
    $intestazione = array();
    $codmat = array();
    $tipoval = array();



    $queryscr = "SELECT * FROM tbl_scrutini WHERE idclasse=$idclasse AND periodo='$periodo'";
    $risscr = mysqli_query($conn, inspref($queryscr)) or die ("Errore nella query: " . mysqli_error($conn));
    $recscr=mysqli_fetch_array($risscr);
    $datascrutinioita=data_italiana($recscr['datascrutinio']);

    // VALORI PER CALCOLO VALUTAZIONI
    $numeroalunni = 0;
    $numeromaterie = 0;

    if (mysqli_num_rows($ris) > 0)
    {
        $intestazione[] = "Cognome";
        $intestazione[] = "Nome";
        $intestazione[] = "Data Nascita";
        $intestazione[] = "Comune nascita";
        $intestazione[] = "Codice fiscale";
        $intestazione[] = "Genere";
        $intestazione[] = "Provincia";
        $intestazione[] = "Classe";
        $intestazione[] = "Sezione";
        $intestazione[] = $plesso_specializzazione;
        while ($nom = mysqli_fetch_array($ris))
        {
            $numeromaterie++;
            $codmat[] = $nom["idmateria"];
            $tipoval[] = $nom["tipovalutazione"];
            if (strpos($nom['tipovalutazione'], "S") != false)
            {
                $intestazione[] = "Scritto " . $nom['sigla'];
            }
            if (strpos($nom['tipovalutazione'], "O") != false)
            {
                $intestazione[] = "Orale " . $nom['sigla'];
            }
            if (strpos($nom['tipovalutazione'], "P") != false)
            {
                $intestazione[] = "Pratico " . $nom['sigla'];
            }
            if (strpos($nom['tipovalutazione'], "U") != false)
            {
                $intestazione[] = "Unico " . $nom['sigla'];
            }

            $intestazione[] = "Assenze " . $nom['sigla'];
            $intestazione[] = "Annotazioni " . $nom['sigla'];
        }

        // INSERISCO LA CONDOTTA
        $numeromaterie++;
        $codmat[] = -1;
        $tipoval[] = "TU";
        $intestazione[] = "COMPO";
        $intestazione[] = "Annotazioni COMPO";


        // INSERISCO IL GIUDIZIO COMPLESSIVO
        $intestazione[] = "Giorni di assenza";
        $intestazione[] = "Giudizio complessivo";
        $intestazione[] = "Data scrutinio";
        fputcsv($fp, $intestazione, ";");
    }


    $query = "SELECT idalunno, cognome, nome, datanascita,idcomnasc,codfiscale from tbl_alunni
             where idalunno in ($elencoalunni) ORDER BY cognome, nome, datanascita";
    $ris = mysqli_query($conn, inspref($query));

    while ($rec = mysqli_fetch_array($ris))
    {
        $numeroalunni++;
        $idalunno = $rec['idalunno'];
        $nominativo = array();

        $nominativo[] = $rec['cognome'];
        $nominativo[] = $rec['nome'];
        $nominativo[] = data_italiana($rec['datanascita']);
        $nominativo[] = decodifica_comune($rec['idcomnasc'], $conn);
        $nominativo[] = $rec['codfiscale'];
        if (substr($rec['codfiscale'],9,2)>35)
            $nominativo[]='F';
        else
            $nominativo[]='M';
        $nominativo[] = estrai_sigla_provincia($rec['idcomnasc'],$conn);
        // ESTRAGGO I DATI DELLA CLASSE
        $querycla = "select * from tbl_classi where idclasse=$idclasse";
        $riscla = mysqli_query($conn, inspref($querycla)) or die ("Errore nella query: " . mysqli_error($conn));
        $reccla = mysqli_fetch_array($riscla);

        $nominativo[] = $reccla['anno'];
        $nominativo[] = $reccla['sezione'];
        $nominativo[] = $reccla['specializzazione'];

        // ESTRAGGO I VOTI
        for ($i = 0; $i < count($codmat); $i++)
        {
            $cm = $codmat[$i];
            $tv = $tipoval[$i];


            //if ($recval= mysqli_fetch_array($risval))
            //{
            if (strpos($tv, "S") != false)
            {
                $nominativo[] = dec_to_pag(ricerca_voto($idalunno, $cm, "S", $alu, $mattipo, $valu));

            }
            if (strpos($tv, "O") != false)
            {
                $nominativo[] = dec_to_pag(ricerca_voto($idalunno, $cm, "O", $alu, $mattipo, $valu));

            }
            if (strpos($tv, "P") != false)
            {
                $nominativo[] = dec_to_pag(ricerca_voto($idalunno, $cm, "P", $alu, $mattipo, $valu));

            }

            if (strpos($tv, "U") != false)
            {
                $nominativo[] = dec_to_pag(ricerca_voto($idalunno, $cm, "U", $alu, $mattipo, $valu));

            }
            if ($cm != -1)   // SE non è il comportamento
            {
                $nominativo[] = ricerca_assenze($idalunno, $cm, $alu, $mattipo, $assenze);
            }
            $nominativo[] = ricerca_note($idalunno, $cm, $alu, $mattipo, $annot);

        }
        // CALCOLO GIORNI DI ASSENZA

        $perioquery = "and true";
        if ($periodo == "1")
        {
            $perioquery = " and data <= '" . $fineprimo . "'";
        }
        if ($periodo == "2" & $numeroperiodi == 2)
        {
            $perioquery = " and data > '" . $fineprimo . "'";
        }
        if ($periodo == "2" & $numeroperiodi == 3)
        {
            $perioquery = " and data > '" . $fineprimo . "' and data <=  '" . $finesecondo . "'";
        }
        if ($periodo == "3")
        {
            $perioquery = " and data > '" . $finesecondo . "'";
        }

        $query = "SELECT COUNT(*) as numassenze from tbl_assenze where idalunno=$idalunno $perioquery";

        $risasse = mysqli_query($conn, inspref($query));

        if ($recasse = mysqli_fetch_array($risasse))
        {
            $numasse = $recasse['numassenze'];
            $nominativo[] = $numasse;
        }
        else
        {
            $nominativo[] = 0;
        }
        // ESTRAGGO IL GIUDIZIO
        $query = "SELECT giudizio from tbl_giudizi
               WHERE idalunno=$idalunno
               AND periodo='$periodo'";

        $risgiud = mysqli_query($conn, inspref($query));
        if ($recgiud = mysqli_fetch_array($risgiud))
        {

            $nominativo[] = $recgiud['giudizio'];

        }
        else
        {
            $nominativo[] = "";
        }
        $nominativo[]=$datascrutinioita;
        fputcsv($fp, $nominativo, ";");
    }
    fclose($fp);

    return ($numeroalunni * $numeromaterie);

}


