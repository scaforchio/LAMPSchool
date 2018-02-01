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

// istruzioni per tornare alla pagina di login se non c'� una sessione valida
////session_start();
$idclasse = stringa_html('idclasse');
$idalunno = stringa_html('idalunno');


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

$titolo = "Scheda d'esame alunno";
$script = "<script>

               function stampaA4()
               {
                  //datast=document.getElementById('datastampa').value;
                  //firmadir=document.getElementById('firmadirigente').value;
                  //gioass=document.getElementById('gioass').value;
                  link='stampaschedeesamealu.php?idalunno=$idalunno';
                  // &firma='+firmadir+'&data='+datast+'&gioass='+gioass;
                  // document.location.href=link;
                  window.open(link);
			   }

               function ricalcola()
               {
                  var tot = 0;
                  var cont = 0;
                  var med = 0;
                  var mediascam=0;
                  var mediafina=0;
                  var votofina=0;
                  var flagNC = false;

                  var elements = document.votialu.getElementsByTagName('INPUT');
                  for (var i = 0; i < elements.length; i++)
                  {
                     if (elements[i].id.substr(0,5)=='votom')
                     {
                        if ((parseInt(elements[i].value) >= 1) & (parseInt(elements[i].value) <= 10))
                        {
                           tot += parseInt(elements[i].value);
                           cont++;
                           nomecamposcheda='schvotom'+i;
                           document.getElementById(nomecamposcheda).value=parseInt(elements[i].value);
						}
					 }
                  }
                  if (!(document.getElementById('privatista').value=='1'))
                  {
                     tot += parseInt(document.getElementById('votoamm').value);
                     cont++;
                  }
                  mediascam=tot/cont*100;
                  mediascam=Math.round(mediascam);
                  mediascam=mediascam/100;
                  document.getElementById('mediaamsc').value=mediascam;
                  document.getElementById('mediaamsch').value=mediascam;
                  mediafina=tot+parseFloat(document.getElementById('votocoll').value);
                  document.getElementById('votoorale').value=parseFloat(document.getElementById('votocoll').value);
                  mediafina=mediafina/(cont+1);
                  mediafina=Math.round(mediafina*100)/100;
                  if (!isNaN(mediafina))
                  {
                     document.getElementById('mediafina').value=mediafina;
                     document.getElementById('mediafinah').value=mediafina;
                  }
                  else
                  {
                     document.getElementById('mediafina').value='';
                     document.getElementById('mediafinah').value='';
                  }
                  votofina=Math.round(mediafina);
                  if (votofina==10)
                      document.getElementById('lode').disabled=false;
                  else
                      {
                      document.getElementById('lode').checked=false;
                      document.getElementById('lode').disabled=true;
                      }
                  if (document.getElementById('privatista').value=='1')
                      if (votofina<6)
                      {
                          document.getElementById('amm3').style.display='block';
                          document.getElementById('ammissioneterza').disabled=false;
                      }
                      else
                      {
                          document.getElementById('amm3').style.display='none';
                          document.getElementById('ammissioneterza').checked=false;
                          document.getElementById('ammissioneterza').disabled=true;
                      }
                  if (!isNaN(votofina))
                  {
                        document.getElementById('vtfina').value=votofina;
                        document.getElementById('vtfinah').value=votofina;
                        document.getElementById('votofinale').value=votofina;
                  }
                  else
                  {
                       document.getElementById('vtfina').value='';
                        document.getElementById('vtfinah').value='';
                  }
                  scarto=Math.round((votofina-mediafina)*100)/100;
                  if (!isNaN(scarto))
                  {
                      document.getElementById('scarto').value=scarto;
                      document.getElementById('scartoh').value=scarto;
                   }
                   else
                   {
                      document.getElementById('scarto').value='';
                      document.getElementById('scartoh').value='';
                   }

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
	                 $('#datacolloquio').datepicker({ dateFormat: 'dd/mm/yy' });

	             });

         </script>";

stampa_head($titolo, "", $script, "E");

stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");


print "<center><br><b><big><big>";

if (!privatista($idalunno, $con))
{
    print estrai_dati_alunno($idalunno, $con) . " - " . decodifica_classe($idclasse, $con);
    print "<input type='hidden' id='privatista' value='0'>";
    $privatista = false;
}
else
{
    print estrai_dati_alunno($idalunno, $con) . " - Privatista (" . decodifica_classe($idclasse, $con) . ")";
    print "<input type='hidden' id='privatista' value='1'>";
    $privatista = true;
}
print "<small><small></small></b></center><center>";

print "<center>";
print "<form method='post' action='insschedaesamealu.php' name='votialu'>";


$query = "select * from tbl_esesiti,tbl_alunni,tbl_classi,tbl_esami3m
        where tbl_esesiti.idalunno=tbl_alunni.idalunno
              and tbl_alunni.idclasseesame=tbl_classi.idclasse
              and tbl_classi.idclasse=tbl_esami3m.idclasse
              and tbl_esesiti.idalunno=$idalunno";

$ris = mysqli_query($con, inspref($query)) or die("Errore: " . inspref($query, false));
$val = mysqli_fetch_array($ris);



$query = "select * from tbl_esmaterie where idclasse=$idclasse";


$rismat = mysqli_query($con, inspref($query)) or die("Errore: " . inspref($query, false));
$recmat = mysqli_fetch_array($rismat);

if ($val['stato'] == 'C')
{
    print "<br><center><font color='red'>Dati in sola lettura! (Esame chiuso)</font></center><br>";
}


// VERIFICA PRESENZA SCRUTINIO PER VOTO AMMISSIONE

$query = "select * from tbl_esiti where idalunno=$idalunno";
$risva = mysqli_query($con, inspref($query));
if (mysqli_num_rows($risva) == 0)
{
    $presenteva = false;
}
else
{
    $recva = mysqli_fetch_array($risva);
    if ($recva['votoammissione'] != 0)
    {
        $presenteva = true;
    }
    else
    {
        $presenteva = false;
    }
}

print "<br><fieldset><legend>Attribuzione voti:</legend>";
print "<table><tr>";
if ($val['votoammissione'] == 0) $votamm = 6;
else $votamm = $val['votoammissione'];
if (!$privatista)
{
    if (!$presenteva)
    {
        print "<td><table bgcolor='#00ffff'><tr><td align='center'>V.amm.</td></tr><tr><td align='center'><input name='votoamm' id='votoamm' type='number' value='" . $votamm . "' min='6' max='10' size='2' ONCHANGE='ricalcola()'></td></tr></table></td>";
    }
    else
    {
        print "<td><table bgcolor='#00ffff'><tr><td align='center'>V.amm.</td></tr><tr><td align='center'><input name='votoamm' id='votoamm' type='number' value='$votamm' min='$votamm' max='$votamm' size='2' ONCHANGE='ricalcola()'></td></tr></table></td>";
    }
}
else
{
    print "<td><table bgcolor='#00ffff'><tr><td align='center'>V.amm.</td></tr><tr><td align='center'>&nbsp;<input name='votoamm' id='votoamm' type='hidden' value='--'></td></tr></table></td>";
}
//print "<td><table bgcolor='#00ffff'><tr><td align='center'>V.amm.</td></tr><tr><td align='center'><input name='va' id='va' type='text' value='".$votamm."' size='2' disabled><input name='votoamm' id='votoamm' type='hidden' value='".$votamm."'></td></tr></table></td>";
/* $mat=$recmat['m1s'];
print "<td><table><tr><td align='center'>$mat</td></tr><tr><td align='center'><input name='votom1' id='votom1' type='number' value='".$val['votom1']."' min='1' max='10' size='2' ONCHANGE='ricalcola()'></td></tr></table></td>";
$mat=$recmat['m2s'];
print "<td><table><tr><td align='center'>$mat</td></tr><tr><td align='center'><input name='votom2' id='votom2' type='number' value='".$val['votom2']."' min='1' max='10' size='2' ONCHANGE='ricalcola()'></td></tr></table></td>";
$mat=$recmat['m3s'];
print "<td><table><tr><td align='center'>$mat</td></tr><tr><td align='center'><input name='votom3' id='votom3' type='number' value='".$val['votom3']."' min='1' max='10' size='2' ONCHANGE='ricalcola()'></td></tr></table></td>";
$mat=$recmat['m4s'];
print "<td><table><tr><td align='center'>$mat</td></tr><tr><td align='center'><input name='votom4' id='votom4' type='number' value='".$val['votom4']."' min='1' max='10' size='2' ONCHANGE='ricalcola()'></td></tr></table></td>";
*/
for ($i = 1; $i <= 9; $i++)
{
    $nomecampo = "m" . $i . "s";
    $nomecampomedia = "m" . $i . "m";
    $mat = $recmat[$nomecampo];
    if ($recmat[$nomecampo] != "")
    {
        if ($recmat[$nomecampomedia])
        {
            print "<td><table><tr><td align='center'>$mat</td></tr><tr><td align='center'><input name='votom" . $i . "' id='votom" . $i . "' type='number' value='" . $val['votom' . $i] . "' min='0' max='10' size='2' ONCHANGE='ricalcola()'></td></tr></table></td>";
        }
        else
        {
            print "<td><table bgcolor='grey'><tr><td align='center'>$mat</td></tr><tr><td align='center'><input name='votom" . $i . "' id='vtm" . $i . "' type='number' value='" . $val['votom' . $i] . "' min='0' max='10' size='2' ONCHANGE='ricalcola()'></td></tr></table></td>";
        }
    }
    else
    {
        print "<input type='hidden' name='votom" . $i . "' value='0'>";
    }
}

print "<td><table><tr><td align='center'>Media va + scr.</td></tr><tr><td align='center'><input name='mediaamsc' id='mediaamsc' type='text' size='4' disabled><input name='mediaamsch' id='mediaamsch' type='hidden'></td></tr></table></td>";
print "<td><table><tr><td align='center'>Colloquio</td></tr><tr><td align='center'><input name='votocoll' id='votocoll' type='number'  value='" . $val['votoorale'] . "' min='0' max='10' size='2' ONCHANGE='ricalcola()'></td></tr></table></td>";

print "<td><table><tr><td align='center'>Media fin.</td></tr><tr><td align='center'><input name='mediafina' id='mediafina' type='text' size='4' disabled><input name='mediafinah' id='mediafinah' type='hidden'></td></tr></table></td>";

print "<td><table bgcolor='yellow'><tr><td align='center'>Voto finale</td></tr><tr><td align='center'><input name='vtfina' id='vtfina' type='text' size='2' disabled><input name='vtfinah' id='vtfinah' type='hidden'></td></tr></table></td>";
print "<td><table><tr><td align='center'>Scarto</td></tr><tr><td align='center'><input name='scarto' id='scarto' type='text' size='4' disabled><input name='scartoh' id='scartoh' type='hidden'></td></tr></table></td>";

print "</tr></table>";

print "</fieldset>";


print "<fieldset><legend>Consiglio orientativo del C.d.C.</legend>";

print "<textarea rows='3' cols='80' name='consorientcons'>" . $val['consorientcons'] . "</textarea>";

print "</fieldset>";

print "<fieldset><legend>Prove d'esame - Prove scritte</legend>";

for ($i = 1; $i <= 9; $i++)
{
    $nomecri = "criteri$i";
    $nomesce = "provasceltam$i";
    $nomevoto = "votom$i";

    $nomecampo = "m" . $i . "s";
    $nomecampoesteso = "m" . $i . "e";
    $mat = $recmat[$nomecampo];

    if ($mat != '')
    {
        if ($recmat['numpni'] != $i)
        {
            print "<br>Prova scritta di <b>" . $recmat[$nomecampoesteso] . "</b>";
            print "<br><left>Prova scelta: <input type='text' name='$nomesce' size='6' value='" . $val[$nomesce] . "'></left> ";
            print "<br>Criteri:<br><textarea rows='3' cols='80' name='$nomecri'>" . $val[$nomecri] . "</textarea>";
            print "<right><br>Valutazione complessiva: <input type='text' name='sch" . $nomevoto . "'  id='sch" . $nomevoto . "' size='2' value='" . $val[$nomevoto] . "' disabled></right><br>";
        }
        else
        {
            print "<br>Prova scritta <b>Invalsi</b>";
            print "<br><left>Prova Italiano: <input type='number' name='votopniita' size='2' value='" . $val['votopniita'] . "' min='0' max='50'></left> ";
            print "<br><left>Prova Matematica: <input type='number' name='votopnimat' size='2' value='" . $val['votopnimat'] . "' min='0' max='50'></left> ";
            print "<right><br>Valutazione complessiva: <input type='text' name='sch" . $nomevoto . "'  id='sch" . $nomevoto . "' size='2' value='" . $val[$nomevoto] . "' disabled></right><br>";
        }
    }

}
print "</fieldset>";

print "<fieldset><legend>Colloquio</legend>";
print "Traccia colloquio:<br><textarea rows='3' cols='80' name='tracciacolloquio'>" . $val['tracciacolloquio'] . "</textarea>";
print "<br>Giudizio colloquio:<br><textarea rows='3' cols='80' name='giudiziocolloquio'>" . $val['giudiziocolloquio'] . "</textarea>";
print "<br>Data colloquio:<br><input type='text' name='datacolloquio' id='datacolloquio' class='datepicker' size='8' maxlength='10' value='" . data_italiana($val['datacolloquio']) . "'>";
print "<right><br>Valutazione colloquio: <input type='text' id='votoorale' size='2' disabled></right><br>";
print "</fieldset>";


print "<fieldset><legend>RISULTANZE DELL'ESAME</legend>";

print "Giudizio complessivo:<br><textarea rows='3' cols='80' name='giudiziocomplessivo'>" . $val['giudiziocomplessivo'] . "</textarea>";

print "<br>Valutazione complessiva: <input type='text' id='votofinale' size='2' disabled></right>&nbsp;";
if ($val['lode'])
{
    $cklode = ' checked';
}
else
{
    $cklode = '';
}

if ($val['ammissioneterza'])
{
    $ckamm3 = ' checked';
}
else
{
    $ckamm3 = '';
}

if ($val['unanimita'])
{
    $ckuna = ' checked';
}
else
{
    $ckuna = '';
}


// TTTT

print "Lode: <input type='checkbox' id='lode' name='lode'$cklode></right><br>";
if ($privatista)
{
    print "<div id='amm3'>Ammissione terza: <input type='checkbox' id='ammissioneterza' name='ammissioneterza'$ckamm3></right><br></div>";
}
else
{
    print "<div id='amm3' style='display:none'>Ammissione terza: <input type='checkbox' id='ammissioneterza' name='ammissioneterza'$ckamm3></right><br></div>";
}
print "Unanimità: <input type='checkbox' id='unanimita' name='unanimita'$ckuna></right><br>";
print "<br>Consiglio orientativo commissione:<br><textarea rows='3' cols='80' name='consorientcomm'>" . $val['consorientcomm'] . "</textarea>";
print "</fieldset>";


print "<input type='hidden' value='$idclasse' name='idclasse'>";
print "<input type='hidden' value='$idalunno' name='idalunno'>";
if ($val['stato'] == 'A')
{
    print "<input type='submit' value='Registra dati'>";
}
else
{
    print "Esame chiuso!<br><a href='rieptabesame.php?cl=$idclasse'>Indietro</a><br><img src='../immagini/stampaA4.png'  onclick='stampaA4()'  onmouseover=$(this).css('cursor','pointer')>";
}
print "</form>";
print "<script>ricalcola();</script>";
print "</center>";

// fclose($fp);
mysqli_close($con);

stampa_piede("");

function privatista($idalunno, $con)
{
    $query = "select idclasse,idclasseesame from tbl_alunni where idalunno=$idalunno";
    $ris = mysqli_query($con, inspref($query)) or die("Errore " . inspref($query, false));
    $rec = mysqli_fetch_array($ris);
    if ($rec['idclasse'] == '0')
    {
        return true;
    }
    else
    {
        return false;
    }
}