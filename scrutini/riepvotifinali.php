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

@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));

// istruzioni per tornare alla pagina di login se non c'è una sessione valida
////session_start();

$idclasse = stringa_html('cl');
$integrativo = stringa_html('integrativo');


if ($integrativo == 'yes')
{
    $scrutiniintegrativi = true;
}
else
{
    $scrutiniintegrativi = false;
}

// DEFINIZIONE ARRAY PER MEMORIZZAZZIONE IN CSV
$listamaterie = array();
$listamaterie[] = "Alunno";


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
if ($scrutiniintegrativi)
{
    $numper = '9';
}
else
{
    $numper = $numeroperiodi;
}
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
               
               function stampaA4(alunno='')
               {
                  datast=document.getElementById('datastampa').value;
                  firmadir=document.getElementById('firmadirigente').value;
                  gioass=document.getElementById('gioass').value;
                  link='stampaschedefinalialu.php?classe=$idclasse&periodo=$numper&firma='+firmadir+'&data='+datast+'&gioass='+gioass;
                  if (alunno!='')
                     link='stampaschedefinalialu.php?firma='+firmadir+'&data='+datast+'&gioass='+gioass+'&idalunno='+alunno;
                  
                  window.open(link);
			   }
               
               function stampaSEP(alunno='')
               {
                  datast=document.getElementById('datastampa').value;
                  firmadir=document.getElementById('firmadirigente').value;
                  link='stampaschedeseparatefin.php?classe=$idclasse&periodo=$numper&firma='+firmadir+'&data='+datast;
                  if (alunno!='')
                      link='stampaschedeseparatefin.php?firma='+firmadir+'&data='+datast+'&idalunno='+alunno;
                      
                  // document.location.href=link;
                  window.open(link);
			   }
               function stampaA3(alunno='')
               {
                  datast=document.getElementById('datastampa').value;
                  firmadir=document.getElementById('firmadirigente').value;
                  link='stampaschedefinalialu_A3.php?classe=$idclasse&periodo=$numper&firma='+firmadir+'&data='+datast;
                  if (alunno!='')
                      link='stampaschedefinalialu_A3.php?firma='+firmadir+'&data='+datast+'&idalunno='+alunno;
                  // document.location.href=link;
                  window.open(link);
					}
               
               function stampaTAB()
               {
                   datast=document.getElementById('datastampa').value;
                   firmadir=document.getElementById('firmadirigente').value;
                   votitab=document.getElementById('votitab').value;
                   votinp=document.getElementById('votinp').value;
                   mediatab=document.getElementById('mediatab').value;
                   voamtab=document.getElementById('voamtab').value;
                   credtab=document.getElementById('credtab').value;
                   link='stampatabfinale.php?classe=$idclasse&periodo=$numper&firma='+firmadir+'&data='+datast+'&votitab='+votitab+'&votinp='+votinp+'&mediatab='+mediatab+'&credtab='+credtab +'&voamtab='+voamtab;
                   // document.location.href=link;
                   window.open(link);
			   }

		function stampaVER()
               {
                  datast=document.getElementById('datastampa').value;
                  firmadir=document.getElementById('firmadirigente').value;

                  link='stampaverbalefin.php?classe=$idclasse&periodo=$numper';
                  // document.location.href=link;
                  window.open(link);
				}

               function stampaMIN(alunno='')
               {
                  datast=document.getElementById('datastampa').value;
                  firmadir=document.getElementById('firmadirigente').value;

                  link='stampaschedamodmin.php?classe=$idclasse&periodo=$numper&firma='+firmadir+'&data='+datast;
                  if (alunno!='')
                       link='stampaschedamodmin.php?firma='+firmadir+'&data='+datast+'&idalunno='+alunno;
                  // document.location.href=link;
                  window.open(link);
				}

               function stampaCRI()
               {

                  link='stampacriteri.php?classe=$idclasse&periodo=$numper';
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


                $(document).ready(function(){
	                 $('#datastampa').datepicker({ dateFormat: 'dd/mm/yy' });
	             });
         //-->
         </script>
         ";

stampa_head($titolo, "", $script, "SDMAP");

stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");

$idscrutinio = 0;


$id_ut_doc = $_SESSION["idutente"];


print ('
         <form method="post" action="riepvotifinali.php" name="voti">
   
         <p align="center">
         <table align="center">');


//
//   Classi
//

print('
        <tr>
        <td width="50%"><b>Classe</b></td>
        <td width="50%">
        <input type="hidden" name="integrativo" value="' . $integrativo . '">
        <SELECT ID="cl" NAME="cl" ONCHANGE="voti.submit()"><option value=""></option>  ');

//
//  Riempimento combobox delle classi
//
$coordinatore = false;
if ($scrutiniintegrativi)
{
    $ricercaintegrativi = " AND idclasse in (SELECT DISTINCT idclasse from tbl_esiti WHERE (validita='1' OR validita='2') AND esito=0) ";
}
else
{
    $ricercaintegrativi = "";
}
if ($tipoutente == "S" | $tipoutente == "P" | $tipoutente == "A")
{
    $query = "SELECT DISTINCT tbl_classi.idclasse,anno,sezione,specializzazione FROM tbl_classi
              WHERE 1=1 $ricercaintegrativi
              ORDER BY anno,sezione,specializzazione";
}
else
{
    $query = "SELECT DISTINCT tbl_classi.idclasse,anno,sezione,specializzazione FROM tbl_classi
              WHERE idcoordinatore=" . $_SESSION['idutente'] . "
              $ricercaintegrativi
              ORDER BY anno,sezione,specializzazione";
    $coordinatore = true;
}
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


    if ($tipoutente != 'A')
    {
// VERIFICO SE E' POSSIBILE IMPORTARE PROPOSTE

        $query = "SELECT * FROM tbl_proposte,tbl_alunni
        where tbl_proposte.idalunno=tbl_alunni.idalunno
        and idclasse=$idclasse
        and periodo=$numeroperiodi";

        $ris = mysqli_query($con, inspref($query));
        $numproposte = mysqli_num_rows($ris);

        $query = "SELECT * FROM tbl_valutazionifinali,tbl_alunni
        where tbl_valutazionifinali.idalunno=tbl_alunni.idalunno
        and idclasse=$idclasse
        and periodo=$numeroperiodi";
        $ris = mysqli_query($con, inspref($query));
        $numvalutazioni = mysqli_num_rows($ris);


// INSERISCO LO SCRUTINIO SE NON E' PRESENTE

        if ($scrutiniintegrativi)
        {
            $numper = 9;
            $textverb = 'verbscruting';
        }
        else
        {
            $numper = $numeroperiodi;
            $textverb = 'verbscrutfin';
        }

        $queryver = "SELECT * FROM tbl_scrutini WHERE idclasse=$idclasse AND periodo='$numper'";
        $risver = mysqli_query($con, inspref($queryver)) or die("Errore nella query: " . mysqli_error($con));
        if (!($valver = mysqli_fetch_array($risver)))
        {

            $orainizioscrutinio = "";
            $orafinescrutinio = "";
            $dataverbale = "";
            $luogoscrutinio = "";
            $sostituzioni = "";
            $segretario = "";
            $datascrutinio = "";
            $testo1 = estrai_testo($textverb . '01', $con);
            $testo2 = estrai_testo($textverb . '02', $con);
            $testo3 = estrai_testo($textverb . '03', $con);
            $testo4 = estrai_testo($textverb . '04', $con);
            $criteri = estrai_testo('criterival', $con);
            $queryscr = "INSERT into tbl_scrutini(idclasse,periodo,datascrutinio,stato)
	                VALUES ($idclasse,$numper,'" . date("Y-m-d") . "','A')";
            $risscr = mysqli_query($con, inspref($queryscr)) or print ("<br><center><b>Errore in inserimento scrutinio!</b></center><br>");
            $idscrutinio = mysqli_insert_id($con);
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
            $criteri = $valver['criteri'];
            if ($testo1 == "")
                $testo1 = estrai_testo($textverb . '01', $con);
            if ($testo2 == "")
                $testo2 = estrai_testo($textverb . '02', $con);
            if ($testo3 == "")
                $testo3 = estrai_testo($textverb . '03', $con);
            if ($testo4 == "")
                $testo4 = estrai_testo($textverb . '04', $con);
            if ($criteri == "")
                $criteri = estrai_testo('criterival', $con);
        }


        if ($numproposte > 0 and $numvalutazioni == 0)
        {
            // IMPORTO LE PROPOSTE PER LA CLASSE

            $queryins = "INSERT into tbl_valutazionifinali(idalunno,idmateria,votounico,votoscritto,votoorale,votopratico,assenze,periodo)
							  SELECT tbl_proposte.idalunno,idmateria,unico,scritto,orale,pratico,assenze,periodo from tbl_proposte,tbl_alunni 
							  where tbl_proposte.idalunno=tbl_alunni.idalunno
							  and idclasse=$idclasse and periodo=$numeroperiodi";

            $risins = mysqli_query($con, inspref($queryins)) or die(mysqli_error($con));
            print "<br><center><b>Voti importati dalle proposte di voto!</b></center><br>";
            // CALCOLO IL VOTO DI CONDOTTA PER TUTTI GLI ALUNNI
            $query = "SELECT idalunno FROM tbl_alunni WHERE idclasse=$idclasse";
            $ris = mysqli_query($con, inspref($query)) or die(mysqli_error($con));
            while ($nom = mysqli_fetch_array($ris))
            {
                $idal = $nom['idalunno'];
                $queryins = "INSERT into tbl_valutazionifinali(idalunno,idmateria,votounico,periodo)
						 VALUES ($idal,-1," . calcola_media_condotta($idal, $numeroperiodi, $con) . ",$numeroperiodi)";
                $risins = mysqli_query($con, inspref($queryins)) or die(mysqli_error($con));
            }
        }

        $alunni = array();
        $mattipo = array();
        $valutazioni = array();
        // RICHIAMO LA FUNZIONE PER LA CREAZIONE DEL FILE CSV
        $numerovalutazioni = creaFileCSV($idclasse, $numeroperiodi, $alunni, $mattipo, $valutazioni, $con);

        // SELEZIONO LE MATERIE
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
            print ("</td>");

            // INSERISCO VOTO MEDIO
            print ("<td>");
            print "V.M.";
            print ("</td>");

            // INSERISCO GIUDIZIO AMMISSIONE PER TERZA MEDIA
            if (($livello_scuola == '2' & decodifica_classe_no_spec($idclasse, $con) == 3) | ($livello_scuola == '3' & decodifica_classe_no_spec($idclasse, $con) == 8))
            {
                print ("<td>");
                print "G.Amm.";
                print ("</td>");
            }
            // INSERISCO ESITO

            print ("<td>");
            print "Esito scrutini";
            print ("</td>");

            // FINE RIGA
            print ("</tr>");
            $numeroalunno = 0;
            if ($scrutiniintegrativi)
            {
                $query = "SELECT * FROM tbl_alunni WHERE idclasse='$idclasse'
                           and idalunno in (select idalunno from tbl_esiti WHERE (validita='1' OR validita='2') AND esito=0)
                           ORDER BY cognome,nome,datanascita";
            }
            else
            {
                $query = "SELECT * FROM tbl_alunni WHERE idclasse='$idclasse'
                           ORDER BY cognome,nome,datanascita";
            }
            // TTTT
            $ris = mysqli_query($con, inspref($query)) or die("Errore nella query: " . mysqli_error($con));
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
                echo "<td align='center'><a href='schedafinalealu.php?cl=$idclasse&idalunno=$idalunno&periodo=$numeroperiodi&prov=tab&integrativo=$integrativo'><img src='../immagini/scrutinio.gif'></a></td>";
                $listavoti[] = $val["cognome"] . " " . $val["nome"] . " " . data_italiana($val["datanascita"]);
                $contavoti = 0;
                $sommavoti = 0;
                for ($nummat = 0; $nummat < count($codmat); $nummat++)
                {
                    $cm = $codmat[$nummat];

                    $votounico = ricerca_voto($idalunno, $cm, "U", $alunni, $mattipo, $valutazioni);

                    print "<td align='center'><table><tr>";
                    
                   // if (strpos($tipoval[$nummat], "U") != false)
                   // {
                        if (insufficiente($votounico))
                        {
                            $colore = "'red'";
                        }
                        else
                        {
                            $colore = "''";
                        }
                        print "<td align='center' bgcolor=$colore>" . dec_to_vot($votounico) . "</td>";
                        if (($votounico >= 1 && $votounico <= 10) && calcola_media($cm, $con))
                        {
                            $contavoti++;
                            $sommavoti += $votounico;
                        }
               //     }

                    print "</tr></table></td>";
                }

                // INSERISCO VOTO MEDIO
                if ($contavoti > 0)
                {
                    print ("<td align=center>" . round($sommavoti / $contavoti, 2) . "</td>");
                }
                else
                {
                    print ("<td align=center>--</td>");
                }
                // INSERISCO GIUDIZIO AMMISSIONE
                if (($livello_scuola == '2' & decodifica_classe_no_spec($idclasse, $con) == 3) | ($livello_scuola == '3' & decodifica_classe_no_spec($idclasse, $con) == 8))
                {
                    print ("<td align=center>");
                    $gi = estrai_voto_ammissione($idalunno, $con);
                    if ($gi != "--")
                    {
                        $gi .= "/10";
                    }
                    print $gi;

                    print ("</td>");
                }
                // INSERISCO ESITO

                print ("<td align=left>");
                $descr_esito = estrai_esito($idalunno, $con);
                if ($descr_esito == "" & $livello_scuola == 4)
                {
                    $descr_esito = "giudizio sospeso";
                }
                print $descr_esito;
                print ("</td>");


                print"</tr>";
            }
            print "</table>";

            //
            //   Gestione verbale
            //  TTTT
            // if (scrutinio_aperto($idclasse, $numeroperiodi, $con))
            if (scrutinio_aperto($idclasse, $numper, $con))
            {
                $abilscr = '';
            }
            else
            {
                $abilscr = ' disabled';
            }
            print "<br><center><input type='button' id='mv' value='Mostra dati verbale' onclick='mostra()' />
				   <input type='button' id='nv' value='Nascondi dati verbale' onclick='nascondi()' /></center>
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
            $risdoc = mysqli_query($con, inspref($querydoc)) or die("Errore nella query: " . inspref($querydoc, false));
            while ($recdoc = mysqli_fetch_array($risdoc))
            {
                print "<tr><td>" . $recdoc['cognome'] . " " . $recdoc['nome'] . "</td><td>";
                $querysost = "SELECT DISTINCT cognome,nome,iddocente FROM tbl_docenti
							WHERE iddocente<>" . $recdoc['iddocente'] . " ORDER BY cognome,nome";
                $rissost = mysqli_query($con, inspref($querysost)) or die("Errore nella query: " . inspref($querysost, false));
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
            $risdoc = mysqli_query($con, inspref($querydoc)) or die("Errore nella query: " . inspref($querydoc, false));
            while ($recdoc = mysqli_fetch_array($risdoc))
            {
                print "<tr><td>" . $recdoc['cognome'] . " " . $recdoc['nome'] . "</td><td>";
                $querysost = "SELECT DISTINCT cognome,nome,iddocente FROM tbl_docenti
							WHERE iddocente<>" . $recdoc['iddocente'] . " ORDER BY cognome,nome";
                $rissost = mysqli_query($con, inspref($querysost)) or die("Errore nella query: " . inspref($querysost, false));
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


            print "<center>

						<br>CRITERI GENERALI DI VALUTAZIONE<br><textarea name='criteri' rows='5' cols='100'>" . $criteri . "</textarea>
                         <input type='hidden' name='integrativo' value='$integrativo'>";
            print"   <br><input type='submit' value='Registra dati per verbale' $abilscr>
				   </center>";

            print "</fieldset>";
            print "</form>";
            print "</div>";

            print "<fieldset><legend>STAMPE</legend>";
            print "<center><br>Data stampa<input type='text' id='datastampa' size='10' maxlenght='10' name='datastampa' value='" . data_italiana(estrai_data_stampa($idclasse, $numper, $con)) . "'>";
            print "&nbsp;Firma dirigente<input type='text' id='firmadirigente' value='" . estrai_firma_scrutinio($idclasse, $numper, $con) . "'>&nbsp;
                   &nbsp;Voti in tabellone<select id='votitab'><option value='yes' selected>Si</option><option value='no'>No</option></select>
                   &nbsp;Giorni assenza nella scheda A4<select name='gioass' id='gioass'><option value='yes'>Sì</option><option value='no'>No</option></select>
                   &nbsp;Voti non promossi in tabellone<select id='votinp'><option value='yes'>Si</option><option value='no' selected>No</option></select>
                   &nbsp;Media in tabellone<select id='mediatab'><option value='yes'>Si</option><option value='no' selected>No</option></select>";


            if (($livello_scuola == '2' & decodifica_classe_no_spec($idclasse, $con) == 3) | ($livello_scuola == '3' & decodifica_classe_no_spec($idclasse, $con) == 8))
            {
                print "Voto ammissione in tabellone<select id='voamtab'><option value='yes' selected>Si</option><option value='no'>No</option></select>";
            }
            else
            {
                print "<input type='hidden' id='voamtab' value='no'>";
            }
            if (($livello_scuola == '4' & decodifica_classe_no_spec($idclasse, $con) > 2))
            {
                print "Credito in tabellone<select id='credtab'><option value='yes' selected>Si</option><option value='no'>No</option></select>";
            }
            else
            {
                print "<input type='hidden' id='credtab' value='no'>";
            }
            print "</center>";
            print "<br><center><img src='../immagini/stampaA4.png'  onclick='stampaA4()'  onmouseover=$(this).css('cursor','pointer')>";
            print "&nbsp;&nbsp;&nbsp;<img src='../immagini/stampaA3.png' onclick='stampaA3()'  onmouseover=$(this).css('cursor','pointer')>";
            print "&nbsp;&nbsp;&nbsp;<img src='../immagini/stampaMIN.png' onclick='stampaMIN()'  onmouseover=$(this).css('cursor','pointer')>";
            print "&nbsp;&nbsp;&nbsp;<img src='../immagini/stampaSEP.png' onclick='stampaSEP()'  onmouseover=$(this).css('cursor','pointer')>";
            print "&nbsp;&nbsp;&nbsp;<img src='../immagini/stampaTAB.png' onclick='stampaTAB()'  onmouseover=$(this).css('cursor','pointer')>";
            print "&nbsp;&nbsp;&nbsp;<img src='../immagini/stampaVERB.png' onclick='stampaVER()'  onmouseover=$(this).css('cursor','pointer')>";
            print "&nbsp;&nbsp;&nbsp;<img src='../immagini/stampaCRI.png' onclick='stampaCRI()'  onmouseover=$(this).css('cursor','pointer')>";

            $nf = "scrut_" . decodifica_classe($idclasse, $con) . "_" . $numeroperiodi . ".csv";
            $nf = str_replace(" ", "_", $nf);
            $nomefile = "$cartellabuffer/" . $nf;
            print ("&nbsp;&nbsp;&nbsp;<a href='$nomefile' target='_blank'><img src='../immagini/csv.png'></a></center>");

            print "</fieldset>";
            // VERIFICO SE LO SCRUTINIO E' COMPLETO E IN TAL CASO PROPONGO LA CHIUSURA
            // ALTRIMENTI VISUALIZZO AVVISO DI VOTI MANCANTI
            //  if (scrutinio_aperto($idclasse, $numeroperiodi, $con))
            if (scrutinio_aperto($idclasse, $numper, $con))
            {

                print "<center>";
                print "<br><form name='chiudiscrutinio' action='chiudiscrutinio.php' method='post'>
						 <input type='hidden' name='idscrutinio' value='$idscrutinio'>
						 
						 <input type='submit' value='Chiudi scrutinio'></form>";

                print "</center>";

                print ("<p align=center><font color='red'>L'attribuzione dei voti è incompleta!</font></p>");
                print "<center>";
                print "<form name='ricaricaproposte' action='ricaricaproposte.php' method='post'>
						 <input type='hidden' name='idscrutinio' value='$idscrutinio'>
						 <br>ATTENZIONE! La reimportazione delle proposte annullerà eventuali modifiche apportate.<br>
						 <input type='submit' value='Ricarica proposte'></form>";

                print "</center>";
            }
            else
            {
                //   $data_scrutinio = data_italiana(estrai_datascrutinio($idclasse, $numeroperiodo, $con));
                /* $data_scrutinio = data_italiana(estrai_datascrutinio($idclasse, $numper, $con));
                  print ("<p align=center><b>Scrutinio chiuso in data: $data_scrutinio!</b></p>"); */
                print ("<p align=center><b>Scrutinio chiuso!</b></p>");
            }
        }
        else
        {
            print("<center><b><br>Nessun alunno presente!</b></center>");
        }
    }
    else
    {
        // if (scrutinio_aperto($idclasse, $numeroperiodi, $con))
        if (scrutinio_aperto($idclasse, $numper, $con))
        {
            print "<div style=\"text-align: center;\"><b><br>Scrutinio non ancora chiuso!</b></div>";
        }
        else
        {
            /* MODIFICHE PER STAMPE SINGOLE DELLE PAGELLE Maggio 2017 */


            $alunni = array();
            $mattipo = array();
            $valutazioni = array();
            // RICHIAMO LA FUNZIONE PER LA CREAZIONE DEL FILE CSV
            $numerovalutazioni = creaFileCSV($idclasse, $numeroperiodi, $alunni, $mattipo, $valutazioni, $con);



            $elencoalunni = estrai_alunni_classe_data($idclasse, $datafinelezioni, $con);


            print ("<table align='center' border='1'><tr class='prima' align='center'><td>Alunno</td><td>Stampa</td></tr>");





            $numeroalunno = 0;


            //	$query='select * from tbl_alunni where idclasse="'.$idclasse.'" order by cognome,nome,datanascita';

            $query = "select * from tbl_alunni where idalunno in ($elencoalunni) order by cognome,nome,datanascita";
            $ris = mysqli_query($con, inspref($query)) or die("Errore nella query: " . mysqli_error($con));
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

                echo "<td align='center'><img src='../immagini/stampaA4.png' height='24' width='24' onclick='stampaA4($idalunno)'  onmouseover=$(this).css('cursor','pointer')>";
                echo "&nbsp;<img src='../immagini/stampaA3.png' height='24' width='24' onclick='stampaA3($idalunno)'  onmouseover=$(this).css('cursor','pointer')>";
                echo "&nbsp;<img src='../immagini/stampaMIN.png' height='24' width='24' onclick='stampaMIN($idalunno)'  onmouseover=$(this).css('cursor','pointer')>";
                echo "&nbsp;<img src='../immagini/stampaSEP.png' height='24' width='24' onclick='stampaSEP($idalunno)'  onmouseover=$(this).css('cursor','pointer')></td></tr>";
//     }
                // FINE MODIFICHE STAMPE SINGOLE
            }


            print "</table>";


            print "<fieldset><legend>STAMPE</legend>";
            print "<center><br>Data stampa<input type='text' id='datastampa' size='10' name='datastampa' value='" . date('d/m/Y') . "'>";
            print "&nbsp;Firma dirigente<input type='text' id='firmadirigente' value='" . estrai_dirigente($con) . "'>&nbsp;
                   Voti in tabellone<select id='votitab'><option value='yes' selected>Si</option><option value='no'>No</option></select>
                   &nbsp;Giorni assenza nella scheda A4<select name='gioass' id='gioass'><option value='yes'>Sì</option><option value='no'>No</option></select>
                   Voti non promossi in tabellone<select id='votinp'><option value='yes'>Si</option><option value='no' selected>No</option></select>
                   Media in tabellone<select id='mediatab'><option value='yes'>Si</option><option value='no' selected>No</option></select>";


            if (($livello_scuola == '2' & decodifica_classe_no_spec($idclasse, $con) == 3) | ($livello_scuola == '3' & decodifica_classe_no_spec($idclasse, $con) == 8))
            {
                print "Voto ammissione in tabellone<select id='voamtab'><option value='yes' selected>Si</option><option value='no'>No</option></select>";
            }
            else
            {
                print "<input type='hidden' id='voamtab' value='no'>";
            }
            if (($livello_scuola == '4' & decodifica_classe_no_spec($idclasse, $con) > 2))
            {
                print "Credito in tabellone<select id='credtab'><option value='yes' selected>Si</option><option value='no'>No</option></select>";
            }
            else
            {
                print "<input type='hidden' id='credtab' value='no'>";
            }
            print "</center>";


            print "<br><center><img src='../immagini/stampaA4.png'  onclick='stampaA4()'  onmouseover=$(this).css('cursor','pointer')>";
            print "&nbsp;&nbsp;&nbsp;<img src='../immagini/stampaA3.png' onclick='stampaA3()'  onmouseover=$(this).css('cursor','pointer')>";
            print "&nbsp;&nbsp;&nbsp;<img src='../immagini/stampaMIN.png' onclick='stampaMIN()'  onmouseover=$(this).css('cursor','pointer')>";
            print "&nbsp;&nbsp;&nbsp;<img src='../immagini/stampaSEP.png' onclick='stampaSEP()'  onmouseover=$(this).css('cursor','pointer')>";
            print "&nbsp;&nbsp;&nbsp;<img src='../immagini/stampaTAB.png' onclick='stampaTAB()'  onmouseover=$(this).css('cursor','pointer')>";
            print "&nbsp;&nbsp;&nbsp;<img src='../immagini/stampaVERB.png' onclick='stampaVER()'  onmouseover=$(this).css('cursor','pointer')>";
            print "&nbsp;&nbsp;&nbsp;<img src='../immagini/stampaCRI.png' onclick='stampaCRI()'  onmouseover=$(this).css('cursor','pointer')>";

            $nf = "scrut_" . decodifica_classe($idclasse, $con) . "_" . $numeroperiodi . ".csv";
            $nf = str_replace(" ", "_", $nf);
            $nomefile = "$cartellabuffer/" . $nf;
            print ("&nbsp;&nbsp;&nbsp;<a href='$nomefile' target='_blank'><img src='../immagini/csv.png'></a></center>");

            print "</fieldset>";
        }
    }
}

mysqli_close($con);
stampa_piede("");

function ricerca_voto($idalunno, $idmateria, $tipo, $alu, $mattipi, $valutaz)
{
 //   print "$idalunno $idmateria $tipo";
        
    for ($i = 0; $i < count($valutaz); $i++)
    {

        if ($idalunno == $alu[$i] & ($idmateria . $tipo) == $mattipi[$i])
        {
         //   print "VAL".$valutaz[$i];
            return $valutaz[$i];
        }
    }
  //  print "non trovata";
    return 0;
}

function ricerca_note($idalunno, $idmateria, $alu, $mattipi, $note)
{


    for ($i = 0; $i < count($note); $i++)
    {

        if ($idalunno == $alu[$i] & ($idmateria == substr($mattipi[$i], 0, strlen($mattipi[$i]) - 1)))
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

        if ($idalunno == $alu[$i] & ($idmateria == substr($mattipi[$i], 0, strlen($mattipi[$i]) - 1)))
        {
            return $assenze[$i];
        }
    }
    return 0;
}

function creaFileCSV($idclasse, $numeroperiodi, &$alu, &$mattipo, &$valu, $conn)
{

    //
    //  PREPARO IL FILE CSV CON I VOTI DEL PRIMO QUADRIMESTRE
    //


    //@require("../php-ini".$_SESSION['suffisso'].".php");
    global $cartellabuffer;
    global $plesso_specializzazione;
    global $fineprimo;
    global $finesecondo;
    global $numeroperiodi;

    $note1 = array();
    $assenze1 = array();
    $alu1 = array();
    $mattipo1 = array();
    $valu1 = array();


    //print "Sono nella funzione!";
    // ESTRAGGO I DATI DEL PRIMO QUADRIMESTRE PER IL FILE CSV

    $query = "SELECT tbl_valutazionifinali.*,tbl_materie.tipovalutazione FROM tbl_valutazionifinali,tbl_alunni,tbl_materie
	          WHERE tbl_valutazionifinali.idalunno=tbl_alunni.idalunno
	          AND tbl_valutazionifinali.idmateria=tbl_materie.idmateria
	          AND idclasse=$idclasse 
	          AND periodo='1'";
    //print inspref($query);
    $risvalu = mysqli_query($conn, inspref($query)) or die(mysqli_error($conn));
    while ($recval = mysqli_fetch_array($risvalu))
    {

        $alu1[] = $recval['idalunno'];
        $mattipo1[] = $recval['idmateria'] . "U";
        $valu1[] = $recval['votounico'];
        $note1[] = $recval['note'];
        $assenze1[] = $recval['assenze'];
    }

    $nf = "scrut_" . decodifica_classe($idclasse, $conn) . "_" . $numeroperiodi . ".csv";
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
    $siglamat = array();
    $tipoval = array();

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
        $intestazione[] = "Data scrutinio 1";
        while ($nom = mysqli_fetch_array($ris))
        {
            $numeromaterie++;
            $codmat[] = $nom["idmateria"];
            $siglamat[] = $nom["sigla"];
            $tipoval[] = $nom["tipovalutazione"];

            $intestazione[] = "Unico " . $nom['sigla'];

            $intestazione[] = "Assenze " . $nom['sigla'];
            $intestazione[] = "Annotazioni " . $nom['sigla'];
        }

        // INSERISCO LA CONDOTTA FINALE
        $numeromaterie++;
        $codmat[] = -1;
        $tipoval[] = "TU";
        $intestazione[] = "COMPO";
        $intestazione[] = "Annotazioni COMPO";

        // INSERISCO IL GIUDIZIO COMPLESSIVO
        $intestazione[] = "Giorni di assenza";
        $intestazione[] = "Giudizio complessivo";
        $intestazione[] = "Data scrutinio 2";


        // Aggiungo i voti finali nell'intestazione

        for ($i = 0; $i < count($codmat); $i++)
        {
            if ($codmat[$i] != -1)
            {
                $intestazione[] = "Finale " . $siglamat[$i];
                $intestazione[] = "Assenze finale " . $siglamat[$i];
                $intestazione[] = "Annotazioni finale " . $siglamat[$i];
            }
        }

        // INSERISCO LA CONDOTTA

        $intestazione[] = "COMPO finale";
        $intestazione[] = "Annotazioni COMPO finale";

        // INSERISCO IL GIUDIZIO COMPLESSIVO
        $intestazione[] = "Giorni di assenza finale";
        $intestazione[] = "Giudizio complessivo finale";
        // Inserisco i campi dell'esito
        $intestazione[] = "Esito finale";
        $intestazione[] = "Integrativo";
        $intestazione[] = "Voto medio finale";
        $intestazione[] = "Credito";
        $intestazione[] = "Credito totale";
        $intestazione[] = "Voto ammissione";


        fputcsv($fp, $intestazione, ";");
    }


    $query = "SELECT idalunno, cognome, nome, datanascita,idcomnasc,codfiscale from tbl_alunni where idclasse=$idclasse ORDER BY cognome, nome, datanascita";
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
        if (substr($rec['codfiscale'], 9, 2) > 35)
            $nominativo[] = 'F';
        else
            $nominativo[] = 'M';

        $nominativo[] = estrai_sigla_provincia($rec['idcomnasc'], $conn);
        // ESTRAGGO I DATI DELLA CLASSE
        $querycla = "select * from tbl_classi where idclasse=$idclasse";
        $riscla = mysqli_query($conn, inspref($querycla)) or die("Errore nella query: " . mysqli_error($conn));
        $reccla = mysqli_fetch_array($riscla);

        $classe = 0;
        if ($reccla['anno'] > 5)
        {
            $classe = $reccla['anno'] - 5;
        }
        else
        {
            $classe = $reccla['anno'];
        }
        $nominativo[] = $classe;
        $nominativo[] = $reccla['sezione'];
        $nominativo[] = $reccla['specializzazione'];
        $query = "SELECT datascrutinio from tbl_scrutini
	          WHERE idclasse=$idclasse 
	          AND periodo='1'";
        //print inspref($query);
        $risscrut = mysqli_query($conn, inspref($query)) or die(mysqli_error($conn));
        $recscrut = mysqli_fetch_array($risscrut);
        $datascrut1 = data_italiana($recscrut['datascrutinio']);
        $nominativo[] = $datascrut1;
        // ESTRAGGO I VOTI
        for ($i = 0; $i < count($codmat); $i++)
        {
            $cm = $codmat[$i];

            $nominativo[] = dec_to_pag(ricerca_voto($idalunno, $cm, "U", $alu1, $mattipo1, $valu1));


            if ($cm != -1)   // SE non è il comportamento
            {
                $nominativo[] = ricerca_assenze($idalunno, $cm, $alu1, $mattipo1, $assenze1);
            }
            $nominativo[] = ricerca_note($idalunno, $cm, $alu1, $mattipo1, $note1);
        }
        // CALCOLO GIORNI DI ASSENZA PRIMO

        $perioquery = " and true";

        $perioquery .= " and data <= '" . $fineprimo . "'";

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
               AND periodo='1'";

        $risgiud = mysqli_query($conn, inspref($query));
        if ($recgiud = mysqli_fetch_array($risgiud))
        {
            $nominativo[] = $recgiud['giudizio'];
        }
        else
        {
            $nominativo[] = "";
        }


        //
        // ESTRAGGO I VOTI DEL PERIODO FINALE
        //

        // Azzero gli array
        $assenze = array();
        $valu = array();
        $alu = array();
        $mattipo = array();
        $note = array();

        // ESTRAGGO I DATI DEL QUADRIMESTRE FINALE

        // TTTT AGGIUNTO filtro su idalunno il 10/06/2017   VERIFUCARE
        $query = "SELECT tbl_valutazionifinali.*,tbl_materie.tipovalutazione FROM tbl_valutazionifinali,tbl_alunni,tbl_materie
					 WHERE tbl_valutazionifinali.idalunno=tbl_alunni.idalunno
					 AND tbl_valutazionifinali.idmateria=tbl_materie.idmateria
                                         AND idclasse=$idclasse
					 AND periodo='$numeroperiodi'";

       // print inspref($query);

        $risvalu = mysqli_query($conn, inspref($query)) or die(mysqli_error($conn));

        while ($recval = mysqli_fetch_array($risvalu))
        {

            $alu[] = $recval['idalunno'];
            $mattipo[] = $recval['idmateria'] . "U";
            $valu[] = $recval['votounico'];
            $note[] = $recval['note'];
            $assenze[] = $recval['assenze'];
        }


        $query = "SELECT datascrutinio from tbl_scrutini
	          WHERE idclasse=$idclasse 
	          AND periodo='2'";
        //print inspref($query);
        $risscrut = mysqli_query($conn, inspref($query)) or die(mysqli_error($conn));
        $recscrut = mysqli_fetch_array($risscrut);
        $datascrut2 = data_italiana($recscrut['datascrutinio']);
        $nominativo[] = $datascrut2;
        // ESTRAGGO I VOTI

        for ($i = 0; $i < count($codmat); $i++)
        {
            $cm = $codmat[$i];

            $nominativo[] = dec_to_pag(ricerca_voto($idalunno, $cm, "U", $alu, $mattipo, $valu));
            if ($cm != -1)   // SE non è il comportamento
            {
                $nominativo[] = ricerca_assenze($idalunno, $cm, $alu, $mattipo, $assenze);
            }
            $nominativo[] = ricerca_note($idalunno, $cm, $alu, $mattipo, $note);
        }
        // CALCOLO GIORNI DI ASSENZA TOTALI

        $query = "SELECT COUNT(*) as numassenze from tbl_assenze where idalunno=$idalunno";

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


        // ESTRAGGO IL GIUDIZIO FINALE
        $query = "SELECT giudizio from tbl_giudizi
               WHERE idalunno=$idalunno
               AND periodo='$numeroperiodi'";

        $risgiud = mysqli_query($conn, inspref($query));
        if ($recgiud = mysqli_fetch_array($risgiud))
        {
            $nominativo[] = $recgiud['giudizio'];
        }
        else
        {
            $nominativo[] = "";
        }


        // ESTRAGGO GLI ESITI FINALI
        $query = "SELECT * from tbl_esiti
               WHERE idalunno=$idalunno";


        $risesito = mysqli_query($conn, inspref($query));
        if ($recesito = mysqli_fetch_array($risesito))
        {
            $nominativo[] = str_replace("|", " ", decodifica_esito($recesito['esito'], $conn));
            $nominativo[] = str_replace("|", " ", decodifica_esito($recesito['integrativo'], $conn));
            $nominativo[] = $recesito['media'];
            $nominativo[] = $recesito['credito'];
            $nominativo[] = $recesito['creditotot'];
            $nominativo[] = $recesito['votoammissione'];
        }
        else
        {
            $nominativo[] = "";
            $nominativo[] = "";
            $nominativo[] = "";
            $nominativo[] = "";
            $nominativo[] = "";
            $nominativo[] = "";
        }

        fputcsv($fp, $nominativo, ";");
    }
    fclose($fp);

    return ($numeroalunni * $numeromaterie);
}
