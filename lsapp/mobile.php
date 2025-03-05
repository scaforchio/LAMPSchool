<?php
require_once 'mobileinit.php';

/*
  ###########################
       SEZIONE VALUTAZIONI
  ###########################
*/

$querymediasg = "select avg(voto) as votomedio from tbl_valutazioniintermedie where idalunno=$idalunno and voto<99";
$rismediasg = eseguiQuery($con, $querymediasg);
$recmediasg = mysqli_fetch_array($rismediasg);
//arrotondiamo a due cifre decimali
$mediacalcsg = round(
    floatval(
        $recmediasg['votomedio']
    ),
    2,
    PHP_ROUND_HALF_UP
);

$valutazioni = array(
    "mediaGlobale" => $mediacalcsg,
    "materie" => [] // lista di array con chiave "materia" e "valutazioni"
);

$query_v = "select * from tbl_valutazioniintermedie, tbl_materie
          where tbl_valutazioniintermedie.idmateria=tbl_materie.idmateria
          and idalunno=$idalunno
          order by denominazione, data desc";
$ris_v = eseguiQuery($con, $query_v);

if (mysqli_num_rows($ris_v) > 0)
{
    $materia = "";

    while ($val = mysqli_fetch_array($ris_v))
    {
        if ($materia != $val['denominazione'])
        {
            $materia = $val['denominazione'];
            $idmateria = $val["idmateria"];

            //////// MEDIA GLOBALE ////////

            $querymedia = "select avg(voto) as votomedio from tbl_valutazioniintermedie where idalunno=$idalunno and idmateria=$idmateria and voto<99";
            $rismedia = eseguiQuery($con, $querymedia);
            $recmedia = mysqli_fetch_array($rismedia);

            $mediacalc = round(
                floatval(
                    $recmedia['votomedio']
                ),
                2,
                PHP_ROUND_HALF_UP
            );

            //////// MEDIA PRIMO QUADRIMESTRE ////////

            $fpdt = new DateTime($_SESSION['fineprimo']);
            $fpdt->modify("+1 day");
            $iniziosecondo = date_format($fpdt,"Y-m-d");
            $querymediaprimo = "select avg(voto) as votomedio from tbl_valutazioniintermedie where idalunno=$idalunno and idmateria=$idmateria and voto<99 and data < '$iniziosecondo' ";
            $rismediaprimo = eseguiQuery($con, $querymediaprimo);
            $recmediaprimo = mysqli_fetch_array($rismediaprimo);

            $mc_primo = round(
                floatval(
                    $recmediaprimo['votomedio']
                ),
                2,
                PHP_ROUND_HALF_UP
            );

            //////// MEDIA SECONDO QUADRIMESTRE ////////

            $fp = $_SESSION['fineprimo'];
            $querymediasecondo= "select avg(voto) as votomedio from tbl_valutazioniintermedie where idalunno=$idalunno and idmateria=$idmateria and voto<99 and data > '$fp' ";
            $rismediasecondo = eseguiQuery($con, $querymediasecondo);
            $recmediasecondo = mysqli_fetch_array($rismediasecondo);

            $mc_secondo = round(
                floatval(
                    $recmediasecondo['votomedio']
                ),
                2,
                PHP_ROUND_HALF_UP
            );

            $valutazioni['materie'][] = array(
                "materia" => $materia,
                "mediaGlobale" => $mediacalc,
                "mediaPrimoQuadrimestre" => $mc_primo,
                "mediaSecondoQuadrimestre" => $mc_secondo,
                "valutazioni" => []
            );
        }

        $giudizio = $val['giudizio'];
        if (substr($giudizio, 0, 1) == "(")
            $giudizio = "";

        $valutazioni['materie'][count($valutazioni['materie']) - 1]['valutazioni'][] = array(
            "data" => $val['data'],
            "tipo" => $val['tipo'],
            "voto" => $val['voto'],
            "giudizio" => $giudizio,
            "cambioClasse" => ($val['idclasse'] != $objalunno['idclasse'])
        );
    }
}

/*
  ###########################
       SEZIONE NOTE
  ###########################
*/

$notedisciplinari = array(
    "individuali" => [],
    "classe" => []
);

$querynalu = "select tbl_notealunno.idnotaalunno, data, tbl_alunni.cognome as cognalunno, tbl_alunni.nome as nomealunno, tbl_alunni.datanascita as dataalunno, tbl_docenti.cognome as cogndocente, tbl_docenti.nome as nomedocente, tbl_alunni.datanascita, testo, provvedimenti 
            from tbl_noteindalu, tbl_notealunno,tbl_classi, tbl_alunni, tbl_docenti 
            where 
            tbl_noteindalu.idnotaalunno=tbl_notealunno.idnotaalunno
            and tbl_noteindalu.idalunno=tbl_alunni.idalunno
            and tbl_notealunno.idclasse=tbl_classi.idclasse and  tbl_notealunno.iddocente=tbl_docenti.iddocente 
            and tbl_noteindalu.idalunno=$idalunno 
            order by tbl_notealunno.data desc";

$risnalu = eseguiQuery($con, $querynalu);

while ($rec = mysqli_fetch_array($risnalu))
{
    $notedisciplinari['individuali'][] = array(
        "docente" => $rec['cogndocente'] . " " . $rec['nomedocente'],
        "data" => $rec['data'],
        "testo" => $rec['testo'],
        "provvedimenti" => $rec['provvedimenti']
    );
}

$idclasse = $objalunno['idclasse'];
$queryncl = "select idnotaclasse, data, tbl_docenti.cognome as cogndocente, tbl_docenti.nome as nomedocente, testo, provvedimenti 
            from tbl_noteclasse, tbl_classi, tbl_docenti 
            where tbl_noteclasse.idclasse=tbl_classi.idclasse and  tbl_noteclasse.iddocente=tbl_docenti.iddocente  
            and tbl_classi.idclasse=$idclasse 
            and data not in (select data from tbl_assenze where idalunno = $idalunno)
            order by tbl_noteclasse.data desc";

$risncl = eseguiQuery($con, $queryncl);

while ($rec = mysqli_fetch_array($risncl))
{
    $notedisciplinari['classe'][] = array(
        "docente" => $rec['cogndocente'] . " " . $rec['nomedocente'],
        "data" => $rec['data'],
        "testo" => $rec['testo'],
        "provvedimenti" => $rec['provvedimenti']
    );
}

/*
  ###########################
       SEZIONE ASSENZE
  ###########################
*/
$assenze = [];
$assenzeQuery = eseguiQuery($con,"select * from tbl_assenze where idalunno=$idalunno order by data desc");

while ($assenza = mysqli_fetch_array($assenzeQuery)) {
    $assenze[] = array(
        "data" => $assenza['data'],
        "giustifica" => $assenza['giustifica'] == 1,
    );
}

/*
  ###########################
       SEZIONE RITARDI
  ###########################
*/

$ritardi = [];
$ritardiQuery = eseguiQuery($con,"select * from tbl_ritardi where idalunno=$idalunno order by data desc");

while ($ritardo = mysqli_fetch_array($ritardiQuery)) {
    $ritardi[] = array(
        "data" => $ritardo['data'],
        "giustifica" => $ritardo['giustifica'] == 1,
    );
}

/*
  ###########################
       SEZIONE USCITE ANT.
  ###########################
*/

$uscite = [];
$usciteQuery = eseguiQuery($con,"select * from tbl_usciteanticipate where idalunno=$idalunno order by data desc");

while ($uscita = mysqli_fetch_array($usciteQuery)) {

    $dataAut = $uscita['data'];
    $qta = "select * from tbl_autorizzazioniuscite where idalunno=$idalunno and data='$dataAut'";
    $testoAutorizzazione = eseguiQuery($con, $qta)->fetch_assoc()['testoautorizzazione'];

    $uscite[] = array(
        "data" => $uscita['data'],
        "testoAutorizzazione" => $testoAutorizzazione,
    );
}

/*
  ###########################
       SEZIONE LEZIONI
  ###########################
*/

$lezioniPerMateria = [];

$queryMaterie = 
"SELECT DISTINCT tbl_materie.idmateria as idmateria, tbl_alunni.idclasse as idclasse, denominazione
    FROM tbl_alunni, tbl_materie, tbl_cattnosupp, tbl_docenti
    WHERE tbl_alunni.idclasse = tbl_cattnosupp.idclasse
    AND tbl_cattnosupp.iddocente = tbl_docenti.iddocente
    AND tbl_cattnosupp.idmateria = tbl_materie.idmateria
    AND tbl_alunni.idalunno =$idalunno
    AND tbl_docenti.iddocente <>1000000000
    ORDER BY denominazione";

$risMaterie = eseguiQuery($con, $queryMaterie);

while ($materia = mysqli_fetch_array($risMaterie)) {
    $materiaObj = array(
        "denominazione" => $materia["denominazione"],
        "lezioni" => []
    );

    $idmateria = $materia["idmateria"];
    $idclasse = $materia["idclasse"];
    $queryLez = "select * from tbl_lezioni where idclasse='$idclasse' and idmateria='$idmateria' and (argomenti<>'' or attivita<>'') order by datalezione";

    $risLez = eseguiQuery($con, $queryLez);

    while ($lez = mysqli_fetch_array($risLez)) {
        $materiaObj["lezioni"][] = array(
            "data" => $lez['datalezione'],
            "argomenti" => html_entity_decode($lez['argomenti']),
            "attivita" => html_entity_decode($lez['attivita'])
        );
    }

    $lezioniPerMateria[] = $materiaObj;
}

/*
  ###########################
       SEZIONE CIRCOLARI
  ###########################
*/

$circolari = [];

$dataoggi = date('Y-m-d');
$queryCirc = "select tbl_diffusionecircolari.idcircolare,tbl_circolari.iddocumento,ricevuta,tbl_circolari.descrizione,datainserimento, datalettura,dataconfermalettura,docsize,docnome,doctype
			  from tbl_diffusionecircolari,tbl_circolari,tbl_documenti
			  where tbl_diffusionecircolari.idcircolare=tbl_circolari.idcircolare
			  and tbl_circolari.iddocumento=tbl_documenti.iddocumento
			  and tbl_diffusionecircolari.idutente=$idutente
			  and tbl_circolari.datainserimento<='$dataoggi'
              and tbl_documenti.doctype in ('application/pdf')
			  order by datainserimento desc";

$risCirc = eseguiQuery($con, $queryCirc);

while ($circolare = mysqli_fetch_array($risCirc)) {
    $circolari[] = array(
        "descrizione" => $circolare['descrizione'],
        "dataInserimento" => $circolare['datainserimento'],
        "dataLettura" => $circolare['datalettura'],
        "idDocumento" => $circolare['iddocumento'],
        "idCircolare" => $circolare['idcircolare'],
    );
}

/*
  ###########################
       SEZIONE AVVISI
  ###########################
*/

$avvisiObj = array(
    "avvisi" => [],
    "avvisiClasse" => [],
    "sondaggiInSospeso" => false
);


/////////// SEZIONE SONDAGGI ///////////

$res_sond = eseguiQuery($con, 
"SELECT tbl_rispostesondaggi.idsondaggio, tbl_sondaggi.oggetto 
FROM tbl_rispostesondaggi, tbl_sondaggi 
WHERE tbl_rispostesondaggi.idutente = $idalunno 
    AND tbl_rispostesondaggi.idopzione = -1 
    AND tbl_sondaggi.idsondaggio = tbl_rispostesondaggi.idsondaggio 
    AND tbl_sondaggi.attivo = 1"
);

if (mysqli_num_rows($res_sond) > 0) {
    $avvisiObj["sondaggiInSospeso"] = true;
}

/////////// SEZIONE AVVISI CLASSE ///////////

$datalimiteinferiore = aggiungi_giorni(date('Y-m-d'), -1);
$queryAvvisiClasse = 
"select * from tbl_annotazioni,tbl_docenti
where tbl_annotazioni.iddocente=tbl_docenti.iddocente
    and idclasse=$idclasse
    and data>'$datalimiteinferiore'
    and visibilitaalunni=true
    order by data";

$risAvvisiClasse = eseguiQuery($con, $queryAvvisiClasse);

while ($avviso = mysqli_fetch_array($risAvvisiClasse)) {
    $avvisiObj["avvisiClasse"][] = array(
        "docente" => $avviso['cognome'] . " " . $avviso['nome'],
        "data" => $avviso['data'],
        "testo" => $avviso['testo']
    );
}

/////////// SEZIONE AVVISI SCUOLA ///////////
$queryAS = "select * from tbl_avvisi where inizio<='$dataoggi' and fine>='$dataoggi' and LOCATE('$tipoutente',destinatari)<>0 order by inizio desc";
$risAS = eseguiQuery($con, $queryAS);

while ($avviso = mysqli_fetch_array($risAS)) {

    $testo = inserisci_parametri($avviso["testo"], $con);
    $testok = html_entity_decode($testo, ENT_QUOTES, 'UTF-8');

    $avvisiObj["avvisi"][] = array(
        "titolo" => $avviso['oggetto'],
        "testoHTML" => $testok,
        "inizio" => $avviso['inizio'],
    );
}

/*
  ###########################
       SEZIONE OUTPUT
  ###########################
*/

$response = array(
    "suffisso" => $suff,
    "alunno" => $idalunno,
    "finePrimoQuadrimestre" => $_SESSION['fineprimo'],
    "valutazioni" => $valutazioni,
    "noteDisciplinari" => $notedisciplinari,
    "assenze" => $assenze,
    "ritardi" => $ritardi,
    "usciteAnticipate" => $uscite,
    "lezioni" => $lezioniPerMateria,
    "circolari" => $circolari,
    "avvisi" => $avvisiObj
);

header('Content-Type: application/json');
echo json_encode($response);