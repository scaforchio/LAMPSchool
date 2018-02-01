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

//
//    Parte iniziale della pagina
//

$titolo = "Inserimento e modifica voti";
$script = "";
stampa_head($titolo, "", $script, "SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");


$modo = stringa_html('modo'); // 'norm' o 'sost'
$cattedra = stringa_html('cattedra');
$tipo = stringa_html('tipo');
$alunno = stringa_html('alunno');
$giorno = stringa_html('gio');
$meseanno = stringa_html('mese');
$idlezione = stringa_html('idlezione');
$orainizioold = stringa_html('orainizioold');

$idgruppo = "";

$nominativo = "";
$idclasse = "";
$idmateria = "";

$pei = 0;


$alunni = array();
$abilita = array();
$giudizi = array();
$voti = array();


// Divido il mese dall'anno
$mese = substr($meseanno, 0, 2);
$anno = substr($meseanno, 5, 4);

if ($giorno == '')
{
    $giorno = date('d');
}
if ($mese == '')
{
    $mese = date('m');
}
if ($anno == '')
{
    $anno = date('Y');
}


$giornosettimana = giorno_settimana($anno . "-" . $mese . "-" . $giorno);

/*
$a=$anno;
$m=$mese;
$g=$giorno;
*/
print ("
   <form method='post' action='valaluabilcono.php' name='valabil'>
   <input type=hidden value='$modo' name=modo>
   <p align='center'>
   <table align='center'>");

//
//   Leggo il nominativo del docente e lo visualizzo
//


$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));

$query = "select iddocente, cognome, nome from tbl_docenti where iddocente=$iddocente";

$ris = mysqli_query($con, inspref($query)) or die("Errore: " . inspref($query));


if ($nom = mysqli_fetch_array($ris))
{
    $iddocente = $nom["iddocente"];
    $cognomedoc = $nom["cognome"];
    $nomedoc = $nom["nome"];
    $nominativo = $nomedoc . " " . $cognomedoc;
}

print("
             <tr>
              <td><b>Docente</b></td>

          <td>
          <INPUT TYPE='text' VALUE='$nominativo' disabled>
          <input type='hidden' value='$iddocente' name='iddocente'>
          </td></tr>");


print("
   
   <tr>
      <td width='50%'><b>Cattedra</b></p></td>
      <td width='50%'>
      <SELECT ID='cattedra' NAME='cattedra' ONCHANGE='valabil.submit()'> <option value=''>&nbsp ");


if ($modo == 'norm')
{
    $query = "select idcattedra,tbl_classi.idclasse,tbl_materie.idmateria, anno, sezione, specializzazione, denominazione from tbl_cattnosupp, tbl_classi, tbl_materie where iddocente=$iddocente and tbl_cattnosupp.idalunno=0 and tbl_cattnosupp.idclasse=tbl_classi.idclasse and tbl_cattnosupp.idmateria = tbl_materie.idmateria order by anno, sezione, specializzazione, denominazione";
}
else
{
    $query = "select idcattedra,tbl_classi.idclasse,tbl_materie.idmateria,idalunno, anno, sezione, specializzazione, denominazione from tbl_cattnosupp, tbl_classi, tbl_materie where iddocente=$iddocente and tbl_cattnosupp.idalunno<>0 and tbl_cattnosupp.idclasse=tbl_classi.idclasse and tbl_cattnosupp.idmateria = tbl_materie.idmateria order by idalunno, denominazione";
}
$ris = mysqli_query($con, inspref($query)) or die ("Errore: " . inspref($query));
while ($nom = mysqli_fetch_array($ris))
{
    print "<option value='";
    print ($nom["idcattedra"]);
    print "'";
    if ($cattedra == $nom["idcattedra"])
    {
        print " selected";
        $idmateria = $nom["idmateria"];   // Memorizzo materia e classe della cattedra selezionata
        $idclasse = $nom["idclasse"];
        if ($modo == 'sost') $alunno = $nom["idalunno"];
    }
    print ">";
    if ($modo == 'norm')
    {
        print ($nom["anno"]);
        print "&nbsp;";
        print($nom["sezione"]);
        print "&nbsp;";
        print($nom["specializzazione"]);
        print "&nbsp;-&nbsp;";
        print($nom["denominazione"]);
    }
    else
    {
        print (estrai_dati_alunno($nom['idalunno'], $con));
        print "&nbsp;-&nbsp;";
        print($nom["denominazione"]);

    }


}

print "</select>";
print ("
         

         <tr>
         <td width='50%'><b>Data (gg/mm/aaaa)</b></td>");


//
//   Inizio visualizzazione della data
//


print("   <td width='50%'>");
print("   <select name='gio'  ONCHANGE='valabil.submit()'>");
for ($g = 1; $g <= 31; $g++)
{
    if ($g < 10)
    {
        $gs = '0' . $g;
    }
    else
    {
        $gs = '' . $g;
    }
    if ($gs == $giorno)
    {
        print("<option selected>$gs");
    }
    else
    {
        print("<option>$gs");
    }
}
print("</select>");

print("   <select name='mese' ONCHANGE='valabil.submit()'>");
for ($m = 9; $m <= 12; $m++)
{
    if ($m < 10)
    {
        $ms = '0' . $m;
    }
    else
    {
        $ms = '' . $m;
    }
    if ($ms == $mese)
    {
        print("<option selected>$ms - $annoscol");
    }
    else
    {
        print("<option>$ms - $annoscol");
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
    if ($ms == $mese)
    {
        print("<option selected>$ms - $annoscolsucc");
    }
    else
    {
        print("<option>$ms - $annoscolsucc");
    }
}
print("</select>");


print("        
      </td></tr>");


// VERIFICO SE SI TRATTA DI UNA CATTEDRA LEGATA A UN GRUPPO
if ($cattedra != '')
{
    $idcl = estrai_id_classe($cattedra, $con);
    $idmat = estrai_id_materia($cattedra, $con);

    $query = "select distinct tbl_gruppi.idgruppo from tbl_gruppialunni,tbl_alunni,tbl_gruppi
			  where tbl_gruppi.idgruppo=tbl_gruppialunni.idgruppo
			  and tbl_gruppialunni.idalunno=tbl_alunni.idalunno
			  and tbl_alunni.idclasse=$idcl
			  and tbl_gruppi.idmateria=$idmat
			  and tbl_gruppi.iddocente=$iddocente";
    $ris = mysqli_query($con, inspref($query)) or die("Errore: " . inspref($query));
    if ($rec = mysqli_fetch_array($ris))
    {
        $idgruppo = $rec['idgruppo'];
    }
}
//


// Se è stata selezionata la cattedra riempio la select degli alunni      
if ($modo == 'norm')
{
    if ($idclasse != "")
    {
        print("
			<tr>
				 <td><b>Alunno</b>
				 </td>
				 <td span>
				 <SELECT ID ='alunno' NAME='alunno' ONCHANGE='valabil.submit()'><option value=''>&nbsp");


        // $query="select idalunno,cognome, nome, datanascita from tbl_alunni where idclasse=$idclasse order by cognome, nome";
        if ($idgruppo == '')
        {
            $query = "select * from tbl_alunni where idclasse='$idclasse' order by cognome,nome,datanascita";
        }
        else
        {
            $query = "select tbl_alunni.idalunno,cognome,nome,datanascita
                  from tbl_gruppi,tbl_gruppialunni,tbl_alunni
                  where
                       tbl_gruppi.idgruppo=tbl_gruppialunni.idgruppo
                       and tbl_gruppialunni.idalunno=tbl_alunni.idalunno
                       and tbl_alunni.idclasse=$idclasse
                       and tbl_gruppi.idgruppo  in (select idgruppo from tbl_gruppi where idmateria=$idmateria and iddocente=$iddocente)";
        }//=$idgruppo";


        $ris = mysqli_query($con, inspref($query)) or die("Errore: " . inspref($query));
        while ($nom = mysqli_fetch_array($ris))
        {
            if (!alunno_certificato($nom['idalunno'], $con))
            {
                $cert = "";
            }
            else
            {
                $cert = " (*)";
            }
            print "<option value='";
            print ($nom["idalunno"]);
            print "'";
            if ($alunno == $nom["idalunno"])
            {
                print " selected";

            }
            print ">";
            print ($nom["cognome"]);
            print "&nbsp;";
            print($nom["nome"]);
            print "&nbsp;";
            print(data_italiana($nom["datanascita"]) . $cert);


        }

    }

    print("</select>
				 
				 </td>
				 
			</tr>");
}
print("      
      <tr>
          <td><b>Tipo verifica</b>
          </td>
          <td span>
          <SELECT ID ='tipo' NAME='tipo' ONCHANGE='valabil.submit()'>");
print("<option value=''>&nbsp;");
print("<option value='S'");
if ($tipo == "S") print" selected";
print ">Scritto";
print("<option value='O'");
if ($tipo == "O") print" selected";
print ">Orale";
print("<option value='P'");
if ($tipo == "P") print" selected";
print ">Pratico";


print("</select>
          
          </td>
          
      </tr>");

// VERIFICO SE ESISTE UNA LEZIONE E SE PIU' DI UNA FACCIO SCEGLIERE
if ($idclasse != '' & $idmateria != '' & $giorno != '' & $mese != '')
{
    // Verifico se esiste già qualche lezione nella giornata

    $query = "select idlezione, orainizio, numeroore from tbl_lezioni
           where idclasse='$idclasse' and idmateria='$idmateria' and datalezione='$anno-$mese-$giorno'";
    // print inspref($query);
    $reslezpres = mysqli_query($con, inspref($query)) or die("Errore: " . inspref($query));
    if (mysqli_num_rows($reslezpres) == 0)
    {
        echo("<tr><td colspan=2><font color='red'><center><b>Non ci sono lezioni per la giornata scelta!</b></center></font></td></tr>");

    }
    if (mysqli_num_rows($reslezpres) == 1)
    {
        $lez = mysqli_fetch_array($reslezpres);
        $idlezione = $lez['idlezione'];
    }
    if (mysqli_num_rows($reslezpres) > 1)
    {
        echo "<tr><td>Lezione:</td><td>";
        print "<select name='orainizioold' ONCHANGE='valabil.submit()'><option value=''>&nbsp;</option>";
        while ($vallezpres = mysqli_fetch_array($reslezpres))
        {
            $strore = $vallezpres['orainizio'] . "-" . ($vallezpres['orainizio'] - 1 + $vallezpres['numeroore']);
            if ($strore != $orainizioold)
            {
                print "<option value='$strore'>" . $strore;
            }
            else
            {
                print "<option value='$strore' selected>" . $strore;
                $idlezione = $vallezpres['idlezione'];
            }

        }
        print "</select>";
        echo "</td></tr>";
    }


}


print("</table></form>");


if (!checkdate($mese, $giorno, $anno))
{
    print "<center>Il giorno selezionato non &egrave; valido.</center>";
}
elseif (($giornosettimana == "Dom"))
{
    print "<center>Il giorno selezionato &egrave; una domenica.</center>";
}
elseif ($cattedra == "")
{
    print "";
}
elseif ($alunno == "")
{
    print "";
}
elseif ($tipo == "")
{
    print "";
}
elseif ($idlezione == "")
{
    print "";
}
elseif (CercaAltroVoto($tipo, $alunno, $idlezione, $con))
{
    print "<center>Gi&agrave; presente una valutazione di questo tipo non legata alle competenze!</center>";
}
elseif ($idgruppo != "" && !VerificaAlunnoGruppo($alunno, $idlezione, $con))
{
    print"<center>Alunno e lezione non combaciano!</center>";
}


else
{
    // INIZIO CODICE RIEMPIMENTO PAGINA

    // VERIFICO SE CI SONO GIA' DEI VOTI INSERITI

    // Carico in un array tutti i voti alle abilità e conoscenze


    $query = "select tbl_valutazioniabilcono.voto as voto,tbl_valutazioniabilcono.idabilita as idabilcono,idalunno
                   from tbl_valutazioniabilcono, tbl_valutazioniintermedie
                     where tbl_valutazioniabilcono.idvalint=tbl_valutazioniintermedie.idvalint
                     and idlezione='$idlezione' and tipo = '$tipo' and idalunno='$alunno'";
    // print inspref($query);
    //      $query="select tbl_valutazioniabilcono.idabilita, tbl_valutazioniintermedie.idalunno,tbl_valutazioniintermedie.voto as votom,tbl_valutazioniabilcono.voto as votoac,tbl_valutazioniintermedie.giudizio
    //              from tbl_valutazioniabilcono,tbl_valutazioniintermedie,  tbl_alunni, tbl_classi where
    //              tbl_valutazioniabilcono.idvalint = tbl_valutazioniintermedie.idvalint and
    //              tbl_valutazioniintermedie.idalunno=tbl_alunni.idalunno
    //              and tbl_alunni.idclasse = tbl_classi.idclasse
    //              and tbl_valutazioniintermedie.idlezione='$idlezione'
    //              and tbl_alunni.idalunno=$alunno
    //              and tipo='$tipo'" ;


    $ris = mysqli_query($con, inspref($query)) or die("Errore: " . inspref($query));

    while ($nom = mysqli_fetch_array($ris))
    {

        $alunni[] = $nom['idalunno'];
        $abilita[] = $nom['idabilcono'];
        // $giudizi[]=$nom['giudizio'];
        $voti[] = $nom['voto'];

    }

    // Carico in una combobox a scelta multipla tutte le voci della programmazione

    print "<form method='post' action='insvalaluabilcono.php' name='votiabil' >";

    print "<table align='center' border=2>
					 <tr>
						 <td valign='top' colspan=2> <center><b>Abilit&agrave; e conoscenze verificate:</b><br/></center></td><td><center><b>Altre valutazioni</b></center></td></tr>";


    $pei = alunno_certificato_pei($alunno, $idmateria, $con);
    if (!$pei)
    {
        $query = "select * from tbl_competdoc
	                 where tbl_competdoc.idmateria = $idmateria and  tbl_competdoc.idclasse = $idclasse
	                 order by numeroordine";
    }
    else
    {
        $query = "select * from tbl_competalu
	                 where idmateria = $idmateria and  idalunno = $alunno
	                 order by numeroordine";
    }

    $riscomp = mysqli_query($con, inspref($query)) or die("Errore: " . inspref($query));

    while ($nomcomp = mysqli_fetch_array($riscomp))
    {
        $idcompetenza = $nomcomp['idcompetenza'];
        $numordcomp = $nomcomp['numeroordine'];
        print "<tr><td colspan=2 bgcolor='grey'>$numordcomp. " . $nomcomp['sintcomp'] . "</td></tr>";


        //CARICO LE CONOSCENZE
        if (!$pei)
        {
            $query = "select * from tbl_abildoc
	              where idcompetenza=$idcompetenza
	              and abil_cono = 'C'
	              order by numeroordine";
        }
        else
        {
            $query = "select * from tbl_abilalu
	              where idcompetenza=$idcompetenza
	              and abil_cono = 'C'
	              order by numeroordine";
        }

        $risabil = mysqli_query($con, inspref($query)) or die("Errore: " . inspref($query));

        while ($nomabil = mysqli_fetch_array($risabil))
        {
            $idabilita = $nomabil['idabilita'];
            $sintabil = $nomabil['sintabilcono'];
            $numord = $nomabil['numeroordine'];
            $obmin = $nomabil['obminimi'];
            if (!$obmin)
            {
                if (!alunno_certificato($alunno, $con) | $pei | (alunno_certificato($alunno, $con) & alunno_certificato_norm($alunno, $idmateria, $con)))
                {
                    print "<tr><td>CO $numordcomp.$numord $sintabil</td>";
                }
            }
            else
            {
                print "<tr><td><i>CO $numordcomp.$numord $sintabil</i></td>";
            }

            if (!alunno_certificato($alunno, $con) | $pei | ($obmin | (alunno_certificato($alunno, $con) & alunno_certificato_norm($alunno, $idmateria, $con))))
            {
                print "<td>
							<select name='voto$idabilita'><option value=99>&nbsp;";

                $votoinserito = CercaVotoAbilita($idabilita, $abilita, $voti);
                if ($ordinevalutazioni == 'C')
                {
                    for ($v = $votominimoattribuibile; $v <= 10; $v = $v + 0.25)
                    {
                        if ($votoinserito == $v)
                        {
                            echo '<option value=' . $v . ' selected>' . dec_to_mod($v);
                        }
                        else
                        {
                            echo '<option value=' . $v . '>' . dec_to_mod($v);
                        }
                    }
                }
                else
                {
                    for ($v = 10; $v >= $votominimoattribuibile; $v = $v - 0.25)
                    {
                        if ($votoinserito == $v)
                        {
                            echo '<option value=' . $v . ' selected>' . dec_to_mod($v);
                        }
                        else
                        {
                            echo '<option value=' . $v . '>' . dec_to_mod($v);
                        }
                    }
                }
                echo "</select></td>";

                // Visualizzo i voti precedenti già inseriti
                echo "<td>";
                $query = "select data,tipo,tbl_valutazioniabilcono.voto from tbl_valutazioniabilcono,tbl_valutazioniintermedie
									where tbl_valutazioniabilcono.idvalint=tbl_valutazioniintermedie.idvalint
									and idabilita=$idabilita
									and idalunno=$alunno
									and (data <> '$anno-$mese-$giorno')
									order by data";

                $risvotiprec = mysqli_query($con, inspref($query)) or die("Errore: " . inspref($query));
                $numvoti = 0;
                $totvoti = 0;
                while ($nomvotiprec = mysqli_fetch_array($risvotiprec))
                {
                    $totvoti += $nomvotiprec["voto"];
                    $numvoti++;

                    if ($nomvotiprec["voto"] >= 6)
                    {
                        echo '<font face="courier" size=1 color=green>';
                    }
                    else
                    {
                        echo '<font face="courier" size=1 color=red>';
                    }
                    echo data_italiana($nomvotiprec["data"]);
                    echo '&nbsp;-&nbsp;';
                    echo dec_to_mod($nomvotiprec["voto"]);
                    if ($nomvotiprec["tipo"] != "$tipo")
                    {
                        echo '&nbsp;-&nbsp;';
                        echo $nomvotiprec["tipo"];
                    }

                    echo '</font><br/>';

                }
                if ($totvoti != 0)
                {
                    print ("<center><font size='1'>MEDIA: " . dec_to_mod($totvoti / $numvoti) . "</font></center>");
                }
                echo "</td>";
                echo "</tr>";
            }

        }


        //CARICO LE ABILITA'
        if (!$pei)
        {
            $query = "select * from tbl_abildoc
	              where idcompetenza=$idcompetenza
	              and abil_cono = 'A'
	              order by numeroordine";
        }
        else
        {
            $query = "select * from tbl_abilalu
	              where idcompetenza=$idcompetenza
	              and abil_cono = 'A'
	              order by numeroordine";
        }

        $risabil = mysqli_query($con, inspref($query)) or die("Errore: " . inspref($query));

        while ($nomabil = mysqli_fetch_array($risabil))
        {
            $idabilita = $nomabil['idabilita'];
            $sintabil = $nomabil['sintabilcono'];
            $numord = $nomabil['numeroordine'];
            $obmin = $nomabil['obminimi'];
            if (!$obmin)
            {
                if (!alunno_certificato($alunno, $con) | $pei | (alunno_certificato($alunno, $con) & alunno_certificato_norm($alunno, $idmateria, $con)))
                {
                    print "<tr><td>AB $numordcomp.$numord $sintabil</td>";
                }
            }
            else

            {
                print "<tr><td><i>AB $numordcomp.$numord $sintabil</i></td>";
            }

            if (!alunno_certificato($alunno, $con) | $pei | ($obmin | (alunno_certificato($alunno, $con) & alunno_certificato_norm($alunno, $idmateria, $con))))
            {
                print "<td>
								<select name='voto$idabilita'><option value=99>&nbsp;";

                $votoinserito = CercaVotoAbilita($idabilita, $abilita, $voti);
                if ($ordinevalutazioni == 'C')
                {
                    for ($v = $votominimoattribuibile; $v <= 10; $v = $v + 0.25)
                    {
                        if ($votoinserito == $v)
                        {
                            echo '<option value=' . $v . ' selected>' . dec_to_mod($v);
                        }
                        else
                        {
                            echo '<option value=' . $v . '>' . dec_to_mod($v);
                        }
                    }
                }
                else
                {
                    for ($v = 10; $v >= $votominimoattribuibile; $v = $v - 0.25)
                    {
                        if ($votoinserito == $v)
                        {
                            echo '<option value=' . $v . ' selected>' . dec_to_mod($v);
                        }
                        else
                        {
                            echo '<option value=' . $v . '>' . dec_to_mod($v);
                        }
                    }
                }
                echo "</select></td>";

                // Visualizzo i voti precedenti già inseriti
                echo "<td>";
                $query = "select data,tipo,tbl_valutazioniabilcono.voto from tbl_valutazioniabilcono,tbl_valutazioniintermedie
										where tbl_valutazioniabilcono.idvalint=tbl_valutazioniintermedie.idvalint
										and idabilita=$idabilita
										and idalunno=$alunno
										and (data <> '$anno-$mese-$giorno')
										order by data";

                $risvotiprec = mysqli_query($con, inspref($query)) or die("Errore: " . inspref($query));
                $numvoti = 0;
                $totvoti = 0;
                while ($nomvotiprec = mysqli_fetch_array($risvotiprec))
                {
                    $totvoti += $nomvotiprec["voto"];
                    $numvoti++;


                    if ($nomvotiprec["voto"] >= 6)
                    {
                        echo '<font face="courier" size=1 color=green>';
                    }
                    else
                    {
                        echo '<font face="courier" size=1 color=red>';
                    }

                    echo data_italiana($nomvotiprec["data"]);
                    echo '&nbsp;-&nbsp;';
                    echo dec_to_mod($nomvotiprec["voto"]);
                    if ($nomvotiprec["tipo"] != "$tipo")
                    {
                        echo '&nbsp;-&nbsp;';
                        echo $nomvotiprec["tipo"];
                    }

                    echo '</font><br/>';

                }
                if ($totvoti != 0)
                {
                    print ("<center><font size='1'>MEDIA: " . dec_to_mod($totvoti / $numvoti) . "</font></center>");
                }
                echo "</td>";


                echo "</tr>";
            }
        }
    }
    print "</table>";


    echo "<center><input type=hidden value='$idclasse' name=cl>
	        <input type=hidden value='$modo' name=modo>
            <input type=hidden value='$giorno' name=gio>
            <input type=hidden value='$mese' name=mese>
            <input type=hidden value='$anno' name=anno>
            <input type=hidden value='$idmateria' name=materia>
            <input type=hidden value='$idlezione' name=idlezione>
            <input type=hidden value='$orainizioold' name=orainizioold>
            <input type=hidden value='$tipo' name=tipo>
            <input type=hidden value='$alunno' name=alunno>
            <input type=hidden value='$iddocente' name=iddocente>
	        <input type=hidden value='$cattedra' name=cattedra><br/>";

    if (controlla_scadenza($maxgiorniritardolez, $giorno, $mese, $anno))  // Verifica se non è passato il tempo e che non c'è una deroga
    {
        print "<center><input type='submit' value='Inserisci voti'></center>";
    }
    else
    {
        print '<p align="center"><font color="red"><b>Tempo scaduto per modifica lezioni! Rivolgersi a dirigente scolastico!</b></font></p>';
    }
    print "</center></form>";


}


mysqli_close($con);
stampa_piede("");


function CercaAltroVoto($tipo, $alunno, $lezione, $con)
{
    $query = "select * from tbl_valutazioniintermedie
	        where tipo='$tipo' 
	        and idlezione='$lezione'
	        and idalunno='$alunno'
	        and voto<>99";


    $risvotiprec = mysqli_query($con, inspref($query)) or die("Errore: " . inspref($query));
    if (mysqli_num_rows($risvotiprec) > 0)
    {
        $vot = mysqli_fetch_array($risvotiprec);
        $codvoto = $vot['idvalint'];
        $query2 = "select * from tbl_valutazioniabilcono
	        where idvalint='$codvoto'";
        $risvotiabprec = mysqli_query($con, inspref($query2)) or die("Errore: " . inspref($query2));
        if (mysqli_num_rows($risvotiabprec) == 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    else
    {
        return false;
    }
}

function VerificaAlunnoGruppo($alunno, $idlezione, $con1)
{
    $idlezionegruppo = estrai_lezione_gruppo($idlezione, $con1);
    $query = "select idgruppo from tbl_lezionigruppi where idlezionegruppo='$idlezionegruppo'";
    $ris = mysqli_query($con1, inspref($query)) or die("Errore: " . inspref($query));
    $rec = mysqli_fetch_array($ris);
    $idgruppo = $rec['idgruppo'];
    $query = "select idgruppoalunno from tbl_gruppialunni where idgruppo='$idgruppo' and idalunno='$alunno'";
    $ris = mysqli_query($con1, inspref($query)) or die("Errore: " . inspref($query));
    if (mysqli_num_rows($ris) > 0)
    {
        return true;
    }
    else
    {
        return false;
    }
}

function CercaVotoAbilita($codice, $abil, $vot)
{
    $numabil = count($abil);

    $votoinserito = 0;

    for ($i = 0; $i < $numabil; $i++)
    {

        if ($codice == $abil[$i])

        {
            $votoinserito = $vot[$i];
        }
    }
    return $votoinserito;
}          

