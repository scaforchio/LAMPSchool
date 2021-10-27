<?php

require_once '../lib/req_apertura_sessione.php';
/*
  Copyright (C) 2018 Pietro Tamburrano
  Questo programma è un software libero; potete redistribuirlo e/o modificarlo secondo i termini della
  GNU Affero General Public License come pubblicata
  dalla Free Software Foundation; sia la versione 3,
  sia (a vostra scelta) ogni versione successiva.

  Questo programma è distribuito nella speranza che sia utile
  ma SENZA ALCUNA GARANZIA; senza anche l'implicita garanzia di
  POTER ESSERE VENDUTO o di IDONEITA' A UN PROPOSITO PARTICOLARE.
  Vedere la GNU Affero General Public License per ulteriori dettagli.

  Dovreste aver ricevuto una copia della GNU Affero General Public License
  in questo programma; se non l'avete ricevuta, vedete http://www.gnu.org/licenses/
 */


require_once '../php-ini' . $_SESSION['suffisso'] . '.php';
require_once '../lib/funzioni.php';
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);
$idalunno= stringa_html('idalunno');

$idclasse= estrai_classe_alunno($idalunno, $con);

$query="select * from tbl_obiettivi where idclasse=$idclasse";
 print $query;
$ris= eseguiQuery($con, $query);
while ($rec=mysqli_fetch_array($ris))
{
    $idobiettivo=$rec['idobiettivo'];
    $query="select * from tbl_valutazioniobiettivi where idobiettivo=$idobiettivo and idalunno=$idalunno";
     print $query;
    $risval= eseguiQuery($con, $query);
    if (mysqli_num_rows($risval)==0)
    {
        $query="insert into tbl_valutazioniobiettivi(idalunno,idobiettivo)"
                . " values ($idalunno,$idobiettivo)";
         print $query;
        eseguiQuery($con, $query);
    }
}
$daticrud = array();
// Tabella da modificare
$daticrud['titolo'] = 'GESTIONE VALUTAZIONE OBIETTIVI PER ALUNNO '. estrai_dati_alunno($idalunno, $con);


$daticrud['tabella'] = ("tbl_valutazioniobiettivi");

$daticrud['larghezzatabella'] = "80%";
// Nome della tabella per visualizzazioni
$daticrud['aliastabella'] = "";
// Campo con l'id univoco per la tabella
$daticrud['campochiave'] = "idvalutazioneobiettivo";

// Campi in base ai quali ordinare (specificare gli alias (14° valore nella descrizione del campo)
// se ci sono campi con lo stesso nome)
// Aggiungere DESC in caso di ordinamento inverso
$daticrud['campiordinamento'] = "idmateria,progressivo";
// Condizione di selezione, specificare solo 'true' se non ce ne sono


$daticrud['condizione'] = "idalunno=$idalunno and periodo='2'"; 


$daticrud['abilitazionemodifica'] = 1;
$daticrud['abilitazionecancellazione'] = 0;
$daticrud['abilitazioneinserimento'] = 0;

// Dati per conferma cancellazione (0 senza conferma, 1 con conferma ed elenco dei campi da visualizzare per conferma)

$daticrud['confermacancellazione'] = [1, ''];
// Vincoli per possibilità di cancellazione. Non devono esserci riferimenti nelle seguenti tabelle nel campo
// specificato
$daticrud['vincolicanc'] = [
    
];


/*
  // Significato valori
 * 0 - campo -nome campo tabella principale
 * 1 - ordtabella - ordine di visualizzazione in tabella (0 non visualizzata)
 * 2 - tabesterna - tabella esterna
 * 3 - chiavetabesterna - campo chiave nella tabella esterna
 * 4 - campivistabesterna - campo o campi (separati da virgola) dei dati da visualizzare
 * 5 - dimmaxinput - dimensione massima del campo per input (0 per chiavi esterne)
 * 6 - labelcampo - Label per indicazione campo
 * 7 - ordinput - Ordine di visualizzazione in maschere di inserimento o modifica (0 non presenti)
 * 8 - tipocampo - Tipo campo per inserimento o modifica (text, date, time, number, boolean, testo)
 * 9 - spiegazione - Spiegazione del contenuto del campo (visualizzato in piccolo sotto alla Label)
 * 10 - obbligatorio - obbligatorio (0 - no, 1 -sì)
 * 11 - valmin - valore minimo ('' per non usarlo)
 * 12 - valmax - valore massimo ('' per non usarlo)
 * 13 - possselfiltro - possibilità di selezione per filtro
 * 14 - elcampialias - elenco eventuali alias dei campi se chiave esterna da visualizzare se usato alias
 * 15 - disabmodifica - modifica disabilitata
 * 16 - selesternadaprimaria - condizione selezione dei valori della tabella esterna, select che restituisce un elenco di valori chiave primaria 
 *      della tabella esterna con riferimento alla chiave primaria della tabella principale
 * 17 - selesternadaaltro - condizione di selezione dei valori della tabella esterna basata su un elenco di valorivalore ricavabile
 *      in fase di preparazione dei daticrud (Es. iddocente, tipoutente, ecc.)
 * 18 - clausdistinct - clausola distinct nella selezione dei valori della tabella esterna (1-sì, 0-no) 
 */

$daticrud['campi'] = [
 ['0' => 'idobiettivo', '1' => '1', '2' => 'tbl_obiettivi', '3' => 'idobiettivo', '4' => 'obiettivo', '6' => 'Obiettivo', '7' => 1, '8' => 'Testo', '15' => 1],
 ['0' => 'idlivelloobiettivo', '1' => '2', '2' => 'tbl_livelliobiettivi', '3' => 'idlivelloobiettivo', '4' => 'abbreviazione', '6' => 'Livello', '7' => 2, '8' => 'text', '15' => 0]
];



$_SESSION['daticrud'] = $daticrud;

header("location: ../lib/CRUD.php?suffisso=" . $_SESSION['suffisso']);

