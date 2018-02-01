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

$idclasse = stringa_html('cl');


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

$titolo = "Tabellone esami di stato";

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
               
               function stampaA4(alu)
               {
                  //datast=document.getElementById('datastampa').value;
                  //firmadir=document.getElementById('firmadirigente').value;
                  //gioass=document.getElementById('gioass').value;
                  if (alu==0)
                     link='stampaschedeesamealu.php?classe=$idclasse';
                  else
                     link='stampaschedeesamealu.php?idalunno='+alu;
                  // &firma='+firmadir+'&data='+datast+'&gioass='+gioass;
                  // document.location.href=link;
                  window.open(link);
			   }
               
               function stampaSEP()
               {
                  datast=document.getElementById('datastampa').value;
                  firmadir=document.getElementById('firmadirigente').value;
                  link='stampaschedeseparatefin.php?classe=$idclasse&firma='+firmadir+'&data='+datast;
                  // document.location.href=link;
                  window.open(link);
			   }
               function stampaA3()
               {
                  datast=document.getElementById('datastampa').value;
                  firmadir=document.getElementById('firmadirigente').value;
                  link='stampaschedefinalialu_A3.php?classe=$idclasse&firma='+firmadir+'&data='+datast;
                  // document.location.href=link;
                  window.open(link);
					}
               
               function stampaTAB()
               {

                   link='stampatabesame.php?classe=$idclasse';

                   window.open(link);
			   }

                function stampaESI()
               {

                   link='stampaesitiesame.php?classe=$idclasse';

                   window.open(link);
			   }

			   function stampaVER()
               {

                  link='stampaverbaleesa.php?classe=$idclasse';
                  // document.location.href=link;
                  window.open(link);
		}

               function stampaREG()
               {

                  link='stamparegistroesame.php?classe=$idclasse';
                  // document.location.href=link;
                  window.open(link);
		}
               function stampaMIN()
               {
                  datast=document.getElementById('datastampa').value;
                  firmadir=document.getElementById('firmadirigente').value;

                  link='stampaschedamodmin.php?classe=$idclasse&firma='+firmadir+'&data='+datast;
                  // document.location.href=link;
                  window.open(link);
				}

               function stampaCRI()
               {

                  link='stampacriteri.php?classe=$idclasse';
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
				 $('#datascrutinio').datepicker({ dateFormat: 'dd/mm/yy' });
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

stampa_head($titolo, "", $script, "E");

stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");

$idesame = 0;


$id_ut_doc = $_SESSION["idutente"];


print ('
         <form method="post" action="rieptabesame.php" name="voti">
   
         <p align="center">
         <table align="center">');


//
//   Classi
//

print('
        <tr>
        <td width="50%"><b>Classe</b></td>
        <td width="50%">
            <SELECT ID="cl" NAME="cl" ONCHANGE="voti.submit()"><option value=""></option>  ');

//
//  Riempimento combobox delle classi
//
$coordinatore = false;
if ($livello_scuola == '2')
{
    $ricercaterze = " AND anno='3' ";
}
else
{
    $ricercaterze = " AND anno='8' ";
}

$query = "SELECT DISTINCT tbl_classi.idclasse,anno,sezione,specializzazione FROM tbl_classi
              WHERE 1=1 $ricercaterze
              ORDER BY anno,sezione,specializzazione";

//print inspref($query);
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

print ("</select></td></tr></table></form><br><br>");

if ($idclasse != "")
{

    // Se non esistono i dati dell'esame relativi alla classe li inserisco
    $query = "select * from tbl_esami3m where idclasse=$idclasse";
    $ris = mysqli_query($con, inspref($query)) or die("Errore: " . inspref($query, false));
    if (mysqli_num_rows($ris) == 0)
    {
        $query = "insert into tbl_esami3m(idclasse,stato) values ('$idclasse','A')";
        mysqli_query($con, inspref($query)) or die("Errore: " . inspref($query, false));
        $query = "select * from tbl_esami3m where idclasse=$idclasse";
        $ris = mysqli_query($con, inspref($query)) or die("Errore: " . inspref($query, false));
    }
    // Visualizzo i dati dello scrutinio
    $rec = mysqli_fetch_array($ris);
    $datascrutinio = $rec['datascrutinio'];
    $luogoscrutinio = $rec['luogoscrutinio'];
    $orainizio = $rec['orainizio'];
    $orafine = $rec['orafine'];
    $idcommissione = $rec['idcommissione'];
    $testo1 = $rec['testo1'];
    $testo2 = $rec['testo2'];
    $testo3 = $rec['testo3'];
    $testo4 = $rec['testo4'];
    if ($testo1 == "") $testo1 = estrai_testo('testoverbesa01', $con);
    if ($testo2 == "") $testo2 = estrai_testo('testoverbesa02', $con);
    if ($testo3 == "") $testo3 = estrai_testo('testoverbesa03', $con);
    if ($testo4 == "") $testo4 = estrai_testo('testoverbesa04', $con);
    $stato = $rec['stato'];

    print "<center>";
    print "<form name='ricaricaproposte' action='ricaricavotiammissione.php' method='post'>
						 <input type='hidden' name='idclasse' value='$idclasse'>

						 <input type='submit' value='Ricarica dati alunni'></form><br>";

    print "</center>";

    $query = "SELECT * FROM tbl_esesiti,tbl_alunni
        where tbl_esesiti.idalunno=tbl_alunni.idalunno
        and tbl_alunni.idclasseesame=$idclasse";
    $ris = mysqli_query($con, inspref($query));
    $numesitiesame = mysqli_num_rows($ris);



    if ($numesitiesame == 0)
    {
        // VERIFICO SE E' POSSIBILE IMPORTARE VOTI DI AMMISSIONE

        $query = "SELECT * FROM tbl_alunni
                      where tbl_alunni.idclasseesame=$idclasse";
        $risalu = mysqli_query($con, inspref($query));

        while ($recalu = mysqli_fetch_array($risalu))
        {
            $idalunno = $recalu['idalunno'];
            // Verifico se è già presente l'id dell'alunno negli esiti esami
            $query = "SELECT * FROM tbl_esesiti
                      where idalunno=$idalunno";
            $risesame = mysqli_query($con, inspref($query));
            if (!$recesame = mysqli_fetch_array($risesame))
            {
                // Se c'è un esito positivo o non c'è un esito inserisco i dati nella tabella degli esiti esami
                $inserimento = true;
                $votoammissione = 0;
                $query = "SELECT * FROM tbl_esiti
                      where idalunno=$idalunno";
                $risesito = mysqli_query($con, inspref($query));
                if ($recesito = mysqli_fetch_array($risesito))
                {
                    if (!passaggio($recesito['esito'], $con))
                    {
                        $inserimento = false;
                    }
                    else
                    {
                        $votoammissione = $recesito['votoammissione'];
                    }
                }
                if ($inserimento)
                {
                    $query = "insert into tbl_esesiti(idalunno,votoammissione,unanimita) values ($idalunno,$votoammissione,1)";
                    mysqli_query($con, inspref($query)) or die ("Errore: " . inspref($query, false));
                }

            }

        }


    }

    $datitabella = array();


    // RICHIAMO LA FUNZIONE PER LA CREAZIONE DEL FILE CSV
    creaFileCSV($idclasse, $datitabella, $con); // $comune_scuola,$annoscol);

    // SELEZIONO LE MATERIE
    $query = "SELECT * from tbl_esmaterie
	              WHERE idclasse=$idclasse";
    $ris = mysqli_query($con, inspref($query));
    if (mysqli_num_rows($ris) > 0)
    {

        $recmat = mysqli_fetch_array($ris);
        print ("<table align='center' border='1'><tr class='prima' align='center'><td>Alunno</td><td>Scheda</td>");
        $tottipoval = "";
        print ("<td>");
        print "VOTO AMM.";
        print ("</td>");


        for ($i = 1; $i <= 9; $i++)
        {
            $nomecampo = "m" . $i . "s";
            if ($recmat[$nomecampo] != "")
            {
                print ("<td>");
                print ($recmat[$nomecampo]);

                print ("</td>");
            }

        }


        // MEDIA SCR.+OR.
        print ("<td>");
        print "SCR.+AMM.";
        print ("</td>");


        // ORALE
        print ("<td>");
        print "COLLOQUIO";
        print ("</td>");

        // MEDIA FINALE
        print ("<td>");
        print "MEDIA FIN.";
        print ("</td>");

        // GIUDIZIO FINALE
        print ("<td>");
        print "VOTO IN LETT.";
        print ("</td>");


        // VOTO FINALE
        print ("<td>");
        print "VOTO FINALE";
        print ("</td>");


        // VOTO FINALE
        print ("<td>");
        print "SCARTO";
        print ("</td>");


      //  if ($stato=='C')
      //  {
            print ("<td>");
            print "St.";
            print ("</td>");
      //  }
        // FINE RIGA
        print ("</tr>");


        $numeroalunno = 0;

        $query = "SELECT * FROM tbl_esesiti,tbl_alunni
                      WHERE tbl_esesiti.idalunno=tbl_alunni.idalunno
                      AND tbl_alunni.idclasseesame=$idclasse
                      ORDER BY idclasse DESC,cognome,nome,datanascita";

       /* $query = "SELECT * FROM tbl_alunni
                      WHERE tbl_alunni.idclasseesame=$idclasse
                      ORDER BY idclasse DESC,cognome,nome,datanascita"; */
        // TTTT
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

            if ($val['idclasse']==0)
                $colore = '#CCCC00';
            echo "<tr bgcolor=$colore>";

            if (!$val['certificato'])
            {
                $cert = "";
            }
            else
            {
                $cert = "<img src='../immagini/apply_small.png'>";
            }


            echo "<td><b>" . $val['cognome'] . " " . $val['nome'] . " " . data_italiana($val['datanascita']) . "$cert</b></td>";
            echo "<td align='center'><a href='schedaesamealu.php?idclasse=$idclasse&idalunno=$idalunno&prov=tab'><img src='../immagini/scrutinio.gif'></a></td>";
            print "<td align='center'>" . $datitabella['vtam' . $idalunno] . "</td>";
            $totalescritti = 0;
            $numeroscritti = 0;
            for ($i = 1; $i <= 9; $i++)
            {
                $nomecampo = "m" . $i . "s";
                if ($recmat[$nomecampo] != "")
                {
                    print ("<td align='center'>");
                    print ($datitabella['vtm' . $i . $idalunno]);
                    $totalescritti += $datitabella['vtm' . $i . $idalunno];
                    $numeroscritti++;
                    print ("</td>");

                }
            }

            // $mediascrittivotoammissione=round($totalescritti/$numeroscritti,2);
            print "<td align='center'>" . $datitabella['scam' . $idalunno] . "</td>";
            print "<td align='center'>" . $datitabella['coll' . $idalunno] . "</td>";

            // $mediafinale=round(($totalescritti+$datitabella['coll'.$idalunno])/($numeroscritti+1),2);

            print "<td align='center'>" . $datitabella['mdfi' . $idalunno] . "</td>";
            //  $votofinale=round($mediafinale);
            if ($datitabella['lode' . $idalunno])
            {
                $lodelett = " e lode";
                $lodenum = "<sup>L</sup>";
            }
            else
            {
                $lodelett = "";
                $lodenum = "";
            }
            print "<td align='center'>" . dec_to_pag($datitabella['vtfi' . $idalunno]) . $lodelett . "</td>";
            print "<td align='center'>" . $datitabella['vtfi' . $idalunno] . $lodenum . "</td>";
            //  $scarto=$votofinale-$mediafinale;
            print "<td align='center'>" . $datitabella['scar' . $idalunno] . "</td>";
       //     if ($stato=='C')
       //     {
                print ("<td>");
                print "<center><img width='50%' height='50%' src='../immagini/stampaA4.png'  onclick='stampaA4($idalunno)'  onmouseover=$(this).css('cursor','pointer')>";
                print ("</td>");
       //     }
            print "</tr>";


        }
        print "</table>";

        //
        //   Gestione verbale
        //
        if (scrutinio_aperto($idclasse, $numeroperiodi, $con))
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
        print "<form name='registradativerbale' action='insdatiesame.php' method='post'>";
        print "<fieldset><legend>DATI VERBALE</legend>";
        print "<center>
						<input type='hidden' name='idesame' value='$idesame'>";
        print "<input type='hidden' name='idclasse' value='$idclasse'>";
        print "Data scrutinio <input type='text' name='datascrutinio' id='datascrutinio' class='datepicker' size='8' maxlength='10' value ='" . data_italiana($datascrutinio) . "'>";
        print "&nbsp;Luogo (aula, ecc.): <input type='text' name='luogoscrutinio' value ='" . $luogoscrutinio . "'>";

        print "&nbsp;Commissione <select name='idcommissione'><option value=''>&nbsp;</option>";
        $querycomm = "SELECT * FROM tbl_escommissioni ORDER BY denominazione";
        $riscomm = mysqli_query($con, inspref($querycomm)) or die("Errore: " . inspref($querycomm, false));
        while ($reccomm = mysqli_fetch_array($riscomm))
        {
            if ($reccomm['idescommissione'] == $idcommissione)
            {
                print "<option value='" . $reccomm['idescommissione'] . "' selected>" . $reccomm['denominazione'] . "</option>";
            }
            else
            {
                print "<option value='" . $reccomm['idescommissione'] . "'>" . $reccomm['denominazione'] . "</option>";
            }
        }
        print "</select>";

        print "
					Ora inizio: <input type='text' name='orainizio' value='" . substr($orainizio, 0, 5) . "' id='orainizio' maxlength='5' size='5'>
					Ora fine: <input type='text' name='orafine' value='" . substr($orafine, 0, 5) . "' id='orafine' maxlength='5' size='5'>";


		print "	<br>Testo 1 scrutinio<br><textarea name='testo1' rows='5' cols='100'>" . $testo1 . "</textarea>

					<br>Testo 2 scrutinio<br><textarea name='testo2' rows='5' cols='100'>" . $testo2 . "</textarea>
					<br>[ESITI ESAMI]<br>Testo 3 scrutinio<br><textarea name='testo3' rows='5' cols='100'>" . $testo3 . "</textarea>
					<br>Testo 4 scrutinio<br><textarea name='testo4' rows='5' cols='100'>" . $testo4 . "</textarea>
					<br>[ELENCO FIRME DOCENTI]<br>

					";

        print "<input type='hidden' name='idclasse' value='$idclasse'>";



        if ($stato == 'A')
        {
            print "&nbsp;<input type='submit' value='Registra dati esame'>";
        }
        else
        {
            print "<b>Scrutinio d'esame chiuso!</b>";
        }



        print "</fieldset>";
        print "</form>";
        print "</div>";


        //
        //   FINE GESTIONE VERBALE
        //


        print "<br><center><img src='../immagini/stampaA4.png'  onclick='stampaA4(0)'  onmouseover=$(this).css('cursor','pointer')>";
        print "&nbsp;&nbsp;&nbsp;<img src='../immagini/stampaTAB.png' onclick='stampaTAB()'  onmouseover=$(this).css('cursor','pointer')>";
        print "&nbsp;&nbsp;&nbsp;<img src='../immagini/stampaESI.png' onclick='stampaESI()'  onmouseover=$(this).css('cursor','pointer')>";
        print "&nbsp;&nbsp;&nbsp;<img src='../immagini/stampaVERB.png' onclick='stampaVER()'  onmouseover=$(this).css('cursor','pointer')>";
        print "&nbsp;&nbsp;&nbsp;<img src='../immagini/stampaVERB.png' onclick='stampaREG()'  onmouseover=$(this).css('cursor','pointer')>";
        $nf = "esami_" . decodifica_classe($idclasse, $con) . ".csv";
        $nf = str_replace(" ", "_", $nf);
        $nomefile = "$cartellabuffer/" . $nf;
        print ("&nbsp;&nbsp;&nbsp;<a href='$nomefile' target='_blank'><img src='../immagini/csv.png'></a></center>");


    }

    else
    {
        print("<center><b><br>Stabilire le materie della classe!</b></center>");
    }

}

mysqli_close($con);
stampa_piede("");


function creaFileCSV($idclasse, &$datitabella, $conn)
{

    //
    //  PREPARO IL FILE CSV CON I DATI DEGLI ESAMI
    //


    //@require("../php-ini".$_SESSION['suffisso'].".php");
    global $cartellabuffer;
    global $comune_scuola;
    global $annoscol;

    $query = "select * from tbl_esmaterie where idclasse=$idclasse";
    $rismat = mysqli_query($conn, inspref($query)) or die("Errore: " . inspref($query, false));
    $recmat = mysqli_fetch_array($rismat);
    $nf = "esami_" . decodifica_classe($idclasse, $conn) . ".csv";
    $nf = str_replace(" ", "_", $nf);
    $nomefile = "$cartellabuffer/" . $nf;
    $fp = fopen($nomefile, 'w');

    // ESTRAGGO I DATI DELLE VALUTAZIONI D'ESAME PER IL FILE CSV
    $intestazione = array();

    $datitabella = array();
    $intestazione[] = "A.S.";
    $intestazione[] = "Classe";
    $intestazione[] = "Sottocommissione";

    $intestazione[] = "Cognome";
    $intestazione[] = "Nome";
    $intestazione[] = "Cod.Fiscale";
    $intestazione[] = "Comune nascita";
    $intestazione[] = "Provincia nascita";
    $intestazione[] = "Data nascita";
    $intestazione[] = "Prima lingua straniera";
    $intestazione[] = "Seconda lingua straniera";
    $intestazione[] = "Cons. orient. CdC";
    $intestazione[] = "Voto ammissione";

    $intestazione[] = "Scr. italiano scelta";
    $intestazione[] = "Scr. italiano criteri";
    $intestazione[] = "Scr. italiano voto";
    $intestazione[] = "Scr. matematica scelta";
    $intestazione[] = "Scr. matematica criteri";
    $intestazione[] = "Scr. matematica voto";
    $intestazione[] = "Scr. Inglese scelta";
    $intestazione[] = "Scr. Inglese criteri";
    $intestazione[] = "Scr. Inglese voto";
    // $intestazione[] = "Prova 4 scelta";
    // $intestazione[] = "Prova 4 criteri";
    // $intestazione[] = "Prova 4 voto";
    $intestazione[] = "P.N.I. Matematica";
    $intestazione[] = "P.N.I. Italiano";
    $intestazione[] = "P.N.I. Complessivo";
    $intestazione[] = $recmat['m5e'] . " scelta";
    $intestazione[] = $recmat['m5e'] . " criteri";
    $intestazione[] = $recmat['m5e'] . " voto";
    $intestazione[] = $recmat['m6e'] . " scelta";
    $intestazione[] = $recmat['m6e'] . " criteri";
    $intestazione[] = $recmat['m6e'] . " voto";
    $intestazione[] = $recmat['m7e'] . " scelta";
    $intestazione[] = $recmat['m7e'] . " criteri";
    $intestazione[] = $recmat['m7e'] . " voto";
    $intestazione[] = $recmat['m8e'] . " scelta";
    $intestazione[] = $recmat['m8e'] . " criteri";
    $intestazione[] = $recmat['m8e'] . " voto";
    $intestazione[] = $recmat['m9e'] . " scelta";
    $intestazione[] = $recmat['m9e'] . " criteri";
    $intestazione[] = $recmat['m9e'] . " voto";

    $intestazione[] = "Traccia colloquio";
    $intestazione[] = "Giudizio colloquio";
    $intestazione[] = "Voto colloquio";
    $intestazione[] = "Data colloquio";
    $intestazione[] = "Giudizio complessivo";
    $intestazione[] = "Voto esame";
    $intestazione[] = "Lode";
    $intestazione[] = "Cons. orient. Commiss.";
    $intestazione[] = "Media scritti e voto ammissione";
    $intestazione[] = "Media finale";
    $intestazione[] = "Scarto";

    $intestazione[] = "Luogo esame";
    $intestazione[] = "Data scrutinio";
    $intestazione[] = "Presidente";

    fputcsv($fp, $intestazione, ";");
    $query = "SELECT * FROM tbl_esesiti,tbl_alunni,tbl_esami3m
	          WHERE tbl_esesiti.idalunno=tbl_alunni.idalunno
	          AND tbl_alunni.idclasseesame=tbl_esami3m.idclasse
	          AND tbl_alunni.idclasseesame=$idclasse
	          order by cognome, nome, datanascita";
    //print inspref($query);
    $risvalu = mysqli_query($conn, inspref($query)) or die(mysqli_error($conn));
    while ($recval = mysqli_fetch_array($risvalu))
    {
        $alunno = array();
        $idalunno = $recval['idalunno'];

        $alunno[] = $annoscol . "-" . ($annoscol + 1);

        $alunno[] = decodifica_classe($recval['idclasseesame'], $conn, 1);
        if ($recval['idcommissione']!=0)
        {
            $query = "select * from tbl_esami3m, tbl_escommissioni
                  where tbl_esami3m.idcommissione=tbl_escommissioni.idescommissione
                  and idclasse=$idclasse";
            $riscomm = mysqli_query($conn, inspref($query)) or die ("Errore: " . inspref($query, false));
            $reccomm = mysqli_fetch_array($riscomm);
            $alunno[] = $reccomm['denominazione'];
            $commissioneregistrata=true;// sot
        }
        else
        {
            $commissioneregistrata=false;
            $alunno[] = "";// sot
        }


        $alunno[] = $recval['cognome'];
        $datitabella["cogn" . $idalunno] = $recval['cognome'];

        $alunno[] = $recval['nome'];
        $datitabella["nome" . $idalunno] = $recval['nome'];

        $alunno[] = $recval['codfiscale'];
        $datitabella["cfis" . $idalunno] = $recval['codfiscale'];

        $alunno[] = decodifica_comune($recval['idcomnasc'], $conn);

        $alunno[] = estrai_sigla_provincia($recval['idcomnasc'], $conn);

        $alunno[] = data_italiana($recval['datanascita']);
        $datitabella["dnas" . $idalunno] = data_italiana($recval['datanascita']);

        $alunno[] = "Inglese"; // prima lingua straniera

        $query="select * from tbl_esmaterie where idclasse=$idclasse";
        $ris=mysqli_query($conn,inspref($query));
        $rec=mysqli_fetch_array($ris);
        $campo2l="m".$rec['num2lin']."e";
        $alunno[] = $rec[$campo2l]; // seconda lingua straniera

        $alunno[] = $recval['consorientcons'];
        $alunno[] = $recval['votoammissione'];
        $datitabella["vtam" . $idalunno] = $recval['votoammissione'];

        $alunno[] = $recval['provasceltam1'];
        $alunno[] = $recval['criteri1'];
        $alunno[] = $recval['votom1'];
        $datitabella["vtm1" . $idalunno] = $recval['votom1'];

        $alunno[] = $recval['provasceltam2'];
        $alunno[] = $recval['criteri2'];
        $alunno[] = $recval['votom2'];
        $datitabella["vtm2" . $idalunno] = $recval['votom2'];

        $alunno[] = $recval['provasceltam3'];
        $alunno[] = $recval['criteri3'];
        $alunno[] = $recval['votom3'];
        $datitabella["vtm3" . $idalunno] = $recval['votom3'];

        $alunno[] = $recval['votopnimat'];
        $alunno[] = $recval['votopniita'];
        $alunno[] = $recval['votom4'];
        $datitabella["vtm4" . $idalunno] = $recval['votom4'];

        $alunno[] = $recval['provasceltam5'];
        $alunno[] = $recval['criteri5'];
        $alunno[] = $recval['votom5'];
        $datitabella["vtm5" . $idalunno] = $recval['votom5'];


        $alunno[] = $recval['provasceltam6'];
        $alunno[] = $recval['criteri6'];
        $alunno[] = $recval['votom6'];
        $datitabella["vtm6" . $idalunno] = $recval['votom6'];

        $alunno[] = $recval['provasceltam7'];
        $alunno[] = $recval['criteri7'];
        $alunno[] = $recval['votom7'];
        $datitabella["vtm7" . $idalunno] = $recval['votom7'];

        $alunno[] = $recval['provasceltam8'];
        $alunno[] = $recval['criteri8'];
        $alunno[] = $recval['votom8'];
        $datitabella["vtm8" . $idalunno] = $recval['votom8'];

        $alunno[] = $recval['provasceltam9'];
        $alunno[] = $recval['criteri9'];
        $alunno[] = $recval['votom9'];
        $datitabella["vtm9" . $idalunno] = $recval['votom9'];


        $alunno[] = $recval['tracciacolloquio'];

        $alunno[] = $recval['giudiziocolloquio'];

        $alunno[] = $recval['votoorale'];
        $datitabella["coll" . $idalunno] = $recval['votoorale'];

        $alunno[] = data_italiana($recval['datacolloquio']);

        $alunno[] = $recval['giudiziocomplessivo'];

        $alunno[] = $recval['votofinale'];
        $datitabella["vtfi" . $idalunno] = $recval['votofinale'];

        if ($recval['lode'])
        {
            $alunno[] = "LODE";
        }
        else
        {
            $alunno[] = "";
        }
        $datitabella["lode" . $idalunno] = $recval['lode'];
        $alunno[] = $recval['consorientcomm'];

        $alunno[] = $recval['mediascramm'];
        $datitabella["scam" . $idalunno] = $recval['mediascramm'];

        $alunno[] = $recval['mediafinale'];
        $datitabella["mdfi" . $idalunno] = $recval['mediafinale'];

        $alunno[] = $recval['scarto'];
        $datitabella["scar" . $idalunno] = $recval['scarto'];


        $alunno[] = $comune_scuola;

        $alunno[] = data_italiana($recval['datascrutinio']);

        if ($commissioneregistrata)
            $alunno[] = $reccomm['nomepresidente'] . " " . $reccomm['cognomepresidente']; // sottocommissione
        else
            $alunno[] = "";
        fputcsv($fp, $alunno, ";");

    }


    fclose($fp);


}
