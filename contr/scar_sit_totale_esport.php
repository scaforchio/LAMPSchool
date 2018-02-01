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

// istruzioni per tornare alla pagina di login se non c'è una sessione valida
////session_start();

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
$iddocente = $_SESSION["idutente"];
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}


// APERTURA FILE CSV PER MEMORIZZAZIONE PROPOSTE
$nf = 'tabelloni-' . date("YmdHis") . '.csv';
$nomefile = "$cartellabuffer/" . $nf;
$fp = fopen($nomefile, 'w');

// DEFINIZIONE ARRAY PER MEMORIZZAZZIONE IN CSV
$lista = array();

// MEMORIZZO L'ELENCO DELLE CATTEDRE DA SALVARE
$cattedre = is_stringa_html('cattedre') ? stringa_html('cattedre') : array();

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));


if (is_stringa_html('tab'))
{

    fputcsv($fp, array('*** TABELLONI ***'), ";");

    foreach ($cattedre as $cattedra)
    {
        generatabellone($cattedra, $fp, $con);
        //generaargomenti($cattedra,$fp);
    }
}
if (is_stringa_html('arg'))
{

    fputcsv($fp, array('*** ARGOMENTI ***'), ";");

    foreach ($cattedre as $cattedra)
    {
        generaargomenti($cattedra, $fp, $con);
        //generaargomenti($cattedra,$fp);
    }
}


if (is_stringa_html('not'))
{


    fputcsv($fp, array('*** NOTE DISCIPLINARI ***'), ";");
    generanote($cattedra, $fp, $con);

}

fclose($fp);
mysqli_close($con);
header("location: " . $nomefile);
// stampa_piede(""); 

function generatabellone($catt, $file, $con)
{

    $perioquery = " and true";
    $lista = array();
    $query = "select idcattedra, cognome, nome,tbl_cattnosupp.iddocente, tbl_cattnosupp.idmateria, tbl_classi.idclasse,tbl_classi.anno,tbl_classi.sezione,tbl_classi.specializzazione, tbl_materie.denominazione
    from tbl_cattnosupp,tbl_classi,tbl_materie,tbl_docenti
    where tbl_cattnosupp.idclasse=tbl_classi.idclasse
    and tbl_cattnosupp.idmateria=tbl_materie.idmateria 
    and tbl_cattnosupp.iddocente=tbl_docenti.iddocente
    and tbl_cattnosupp.idcattedra=$catt";
    $ris = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
    if ($val = mysqli_fetch_array($ris))
    {
        $classe = $val["anno"] . " " . $val["sezione"] . " " . $val["specializzazione"];
        $docente = $val["cognome"] . " " . $val["nome"];
        $materia = $val["denominazione"];
        $idclasse = $val["idclasse"];
        $iddocente = $val["iddocente"];
        $idmateria = $val["idmateria"];


        //   $lista[]="Docente:";
        //   $lista[]=$docente;
        //   $lista[]="Classe:";
        //   $lista[]=$classe;
        //   $lista[]="Materia:";
        //   $lista[]=$materia;

        $query = "select sum(numeroore) as numtotore from tbl_firme,tbl_lezioni
               where tbl_firme.idlezione=tbl_lezioni.idlezione
               and tbl_lezioni.idclasse='$idclasse' and tbl_firme.iddocente='$iddocente' and idmateria='$idmateria' " . $perioquery;
        $risore = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
        if ($valore = mysqli_fetch_array($risore))
        {

            $oretotalilezione = isset($valore["numtotore"]) ? $valore["numtotore"] : 0;

            $lista[] = "$docente - $materia - $classe - $oretotalilezione ore";

        }

        fputcsv($file, $lista, ";");
        $giornilezione = array();

        $query = "select tbl_firme.idlezione,datalezione, numeroore,orainizio from tbl_firme, tbl_lezioni
       where tbl_firme.idlezione=tbl_lezioni.idlezione
       and tbl_lezioni.idclasse='$idclasse' and tbl_firme.iddocente='$iddocente' and idmateria='$idmateria' " . $perioquery . " order by datalezione";
        $rislez = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
        while ($reclez = mysqli_fetch_array($rislez))
        {
            $gio = substr($reclez['datalezione'], 8, 2);
            $mes = substr($reclez['datalezione'], 5, 2);
            $ann = substr($reclez['datalezione'], 0, 4);
            $gio = $ann . $mes . $gio . $reclez['datalezione'] . $reclez['orainizio'] . $reclez['numeroore'] . $reclez['idlezione'];
            $giornilezione[] = $gio;
        }
        /*     // AGGIUNGO AI GIORNI DI LEZIONE LE GIORNATE IN CUI SONO PRESENTI VALUTAZIONI
             $query="select distinct data from tbl_valutazioniintermedie,tbl_alunni where tbl_valutazioniintermedie.idalunno=tbl_alunni.idalunno and idclasse='".$idclasse."' and iddocente='$iddocente' and idmateria='".$idmateria."' ".$perioquery." order by data";
             $risvot=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));
             while ($recvot=mysqli_fetch_array($risvot))
             {
                $presente=false;
                foreach($giornilezione as $ggia)
                {
                   if (substr($ggia,6,2)==substr($recvot['data'],8,2) & substr($ggia,4,2)==substr($recvot['data'],5,2))
                      $presente=true;
                }
                if (!$presente)
                {
                   $gio=substr($recvot['data'],8,2);
                   $mes=substr($recvot['data'],5,2);
                   $ann=substr($recvot['data'],0,4);
                   $gio=$ann.$mes.$gio.$recvot['data']."0"."00000000000";
                   $giornilezione[]=$gio;
                }
             }
             */
        // ORDINO I GIORNI DI LEZIONE
        sort($giornilezione);
        // MEMORIZZO I GIORNI DI LEZIONE
        $giorni = array();
        $giorni[] = "";
        foreach ($giornilezione as $gg)
        {
            $strore = substr($gg, 8, 1) . ">" . ((substr($gg, 19, 1) + substr($gg, 8, 1) - 1));
            $giorni[] = "" . giorno_settimana(substr($gg, 9, 10)) . " " . substr($gg, 6, 2) . "/" . substr($gg, 4, 2) . " $strore";
        }
        fputcsv($file, $giorni, ";");

        // ESTRAGGO LE ASSENZE DEGLI ALUNNI
        $assore = array();
        $assid = array();

        $queryass = "select idlezione, oreassenza,tbl_asslezione.idalunno from tbl_asslezione,tbl_alunni
                 where tbl_asslezione.idalunno=tbl_alunni.idalunno
                 and tbl_alunni.idclasse=$idclasse 
                 and idmateria=$idmateria order by idlezione";
        $risass = mysqli_query($con, inspref($queryass)) or die ("Errore nella query: " . mysqli_error($con));
        while ($valass = mysqli_fetch_array($risass))
        {

            $assore[] = $valass['oreassenza'];
            //   print ($valass['data']."|".$valass['idalunno']);
            $assid[] = $valass['idlezione'] . "|" . $valass['idalunno'];

        }

        // ESTRAGGO I VOTI DEGLI ALUNNI
        $votval = array();
        $votid = array();

        $queryvot = "select voto, idlezione, tipo, tbl_valutazioniintermedie.idalunno from tbl_valutazioniintermedie,tbl_alunni
                 where tbl_valutazioniintermedie.idalunno=tbl_alunni.idalunno
                 and tbl_alunni.idclasse=$idclasse 
                 and idmateria=$idmateria 
                 order by idlezione";
        $risvot = mysqli_query($con, inspref($queryvot)) or die ("Errore nella query: " . mysqli_error($con));
        while ($valvot = mysqli_fetch_array($risvot))
        {

            $votval[] = $valvot['voto'];
            //   print ($valass['data']."|".$valass['idalunno']);
            $votid[] = $valvot['idlezione'] . "|" . $valvot['tipo'] . "|" . $valvot['idalunno'];

        }


        $query = 'SELECT * FROM tbl_alunni WHERE idclasse="' . $idclasse . '" ORDER BY cognome,nome,datanascita';
        $risalu = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));

        while ($valalu = mysqli_fetch_array($risalu))
        {
            $arralu = array();
            $arralu[] = $valalu['cognome'] . " " . $valalu['nome'] . " " . data_italiana($valalu['datanascita']);
            $totoreass = 0;
            // Codice per stampa dati sugli tbl_alunni

            foreach ($giornilezione as $gg)
            {


                $stringagiorno = "";

                // RICERCA ORE DI ASSENZA
                $assenzadacercare = substr($gg, 20, 11) . "|" . $valalu['idalunno'];
                // print $assenzadacercare;
                if ($pos = array_search($assenzadacercare, $assid))
                {

                    $stringagiorno = "A" . $assore[$pos] . " ";
                    $totoreass = $totoreass + $assore[$pos];
                }


                // RICERCA VOTI SCRITTI
                $votodacercare = substr($gg, 20, 11) . "|S|" . $valalu['idalunno'];

                if ($pos = array_search($votodacercare, $votid))
                {
                    if ($votval[$pos] != 99)
                    {
                        $stringagiorno .= dec_to_csv($votval[$pos]) . "s ";
                    }
                    else
                    {
                        $stringagiorno .= "Gi.s. ";
                    }
                }

                // RICERCA VOTI ORALI
                $votodacercare = substr($gg, 20, 11) . "|O|" . $valalu['idalunno'];

                if ($pos = array_search($votodacercare, $votid))
                {
                    if ($votval[$pos] != 99)
                    {
                        $stringagiorno .= dec_to_csv($votval[$pos]) . "o ";
                    }
                    else
                    {
                        $stringagiorno .= "Gi.o. ";
                    }
                }

                // RICERCA VOTI PRATICI
                $votodacercare = substr($gg, 20, 11) . "|P|" . $valalu['idalunno'];

                if ($pos = array_search($votodacercare, $votid))
                {
                    if ($votval[$pos] != 99)
                    {
                        $stringagiorno .= dec_to_csv($votval[$pos]) . "p ";
                    }
                    else
                    {
                        $stringagiorno .= "Gi.p. ";
                    }
                }

                // RICERCA VOTI SCRITTI
                //      $votodacercare=substr($gg,20,11)."|M|".$valalu['idalunno'];
                //
                //      if ($pos = array_search($votodacercare,$votid))
                //      {
                //         if ($votval[$pos]!=99)
                //            $stringagiorno.=dec_to_csv($votval[$pos])."M ";
                //         else
                //            $stringagiorno.="Ann.M ";
                //      }

                $arralu[] = $stringagiorno;
            }
            fputcsv($file, $arralu, ";");
        }
        $lista = array('');
        fputcsv($file, $lista, ";");
    }
}

function generaargomenti($catt, $file, $con)
{

    $perioquery = " and true";
    $lista = array();
    $query = "select idcattedra, cognome, nome,tbl_cattnosupp.iddocente, tbl_cattnosupp.idmateria, tbl_classi.idclasse,tbl_classi.anno,tbl_classi.sezione,tbl_classi.specializzazione, tbl_materie.denominazione
    from tbl_cattnosupp,tbl_classi,tbl_materie,tbl_docenti
    where tbl_cattnosupp.idclasse=tbl_classi.idclasse
    and tbl_cattnosupp.idmateria=tbl_materie.idmateria 
    and tbl_cattnosupp.iddocente=tbl_docenti.iddocente
    and tbl_cattnosupp.idcattedra=$catt";
    $ris = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
    if ($val = mysqli_fetch_array($ris))
    {
        $classe = $val["anno"] . " " . $val["sezione"] . " " . $val["specializzazione"];
        $docente = $val["cognome"] . " " . $val["nome"];
        $materia = $val["denominazione"];
        $idclasse = $val["idclasse"];
        $iddocente = $val["iddocente"];
        $idmateria = $val["idmateria"];


        //   $lista[]="Docente:";
        //   $lista[]=$docente;
        //   $lista[]="Classe:";
        //   $lista[]=$classe;
        //   $lista[]="Materia:";
        //   $lista[]=$materia;

        $query = "select sum(numeroore) as numtotore from tbl_lezioni where idclasse='" . $idclasse . "' and iddocente='$iddocente' and idmateria='" . $idmateria . "' " . $perioquery;
        $risore = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
        if ($valore = mysqli_fetch_array($risore))
        {

            $oretotalilezione = isset($valore["numtotore"]) ? $valore["numtotore"] : 0;

            $lista[] = "$docente - $materia - $classe - $oretotalilezione ore";

        }

        fputcsv($file, $lista, ";");


        $query = "select * from tbl_lezioni where idclasse=$idclasse and idmateria=$idmateria order by datalezione";

        $risarg = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
        while ($recarg = mysqli_fetch_array($risarg))
        {
            $arrarg = array();
            fputcsv($file, array(data_italiana($recarg['datalezione'])), ";");
            if ($recarg['argomenti'] != '')
            {
                fputcsv($file, array($recarg['argomenti']), ";");
            }
            if ($recarg['attivita'] != '')
            {
                fputcsv($file, array($recarg['attivita']), ";");
            }

        }

    }

    fputcsv($file, array(""), ";");
}

function generanote($catt, $file, $con)
{

    $perioquery = " and true";
    $lista = array();

    $query = "SELECT * FROM tbl_classi ORDER BY anno, sezione, specializzazione";
    $riscla = mysqli_query($con, inspref($query));
    while ($valcla = mysqli_fetch_array($riscla))
    {


        fputcsv($file, array("Note della classe " . $valcla['anno'] . " " . $valcla['sezione'] . " " . $valcla['specializzazione']), ";");
        //
        //  VISUALIZZO LE NOTE DI CLASSE
        //
        $querynotecla = "SELECT * FROM tbl_noteclasse,tbl_docenti
                        WHERE tbl_noteclasse.iddocente=tbl_docenti.iddocente
                        AND idclasse=" . $valcla['idclasse'] . " ORDER BY data";
        // print (inspref($querynotecla));
        $risnotecla = mysqli_query($con, inspref($querynotecla));
        while ($valnotecla = mysqli_fetch_array($risnotecla))
        {
            $data = data_italiana($valnotecla['data']);
            $docente = $valnotecla['cognome'] . " " . $valnotecla['nome'];
            $alunno = "";
            $testo = $valnotecla['testo'];
            $provvedimenti = $valnotecla['provvedimenti'];
            fputcsv($file, array($data, $docente, $alunno, $testo, $provvedimenti), ";");
        }


//
// VISUALIZZO LE NOTE INDIVIDUALI  

        $querynotealu = "SELECT data, tbl_alunni.cognome AS cognalunno, tbl_alunni.nome AS nomealunno, tbl_alunni.datanascita AS dataalunno, tbl_docenti.cognome AS cogndocente,        tbl_docenti.nome AS nomedocente, testo, provvedimenti
                FROM tbl_noteindalu,tbl_notealunno, tbl_alunni, tbl_docenti
                WHERE
                tbl_noteindalu.idnotaalunno=tbl_notealunno.idnotaalunno
                AND tbl_noteindalu.idalunno=tbl_alunni.idalunno
                AND tbl_notealunno.iddocente=tbl_docenti.iddocente
                AND tbl_notealunno.idclasse = " . $valcla['idclasse'] .
            " ORDER BY data";
        $risnotealu = mysqli_query($con, inspref($querynotealu));

        while ($valnotealu = mysqli_fetch_array($risnotealu))
        {
            $data = data_italiana($valnotealu['data']);
            $docente = $valnotealu['cogndocente'] . " " . $valnotealu['nomedocente'];
            $alunno = $valnotealu['cognalunno'] . " " . $valnotealu['nomealunno'];
            $testo = $valnotealu['testo'];
            $provvedimenti = $valnotecla['provvedimenti'];
            fputcsv($file, array($data, $docente, $alunno, $testo, $provvedimenti), ";");

        }
        fputcsv($file, array(""), ";");
    }


}



