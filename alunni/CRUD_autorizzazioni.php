<?php
session_start();

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

$soloclasse= stringa_html('soloclasse');
$daticrud = array();
// Tabella da modificare
$daticrud['titolo'] = 'GESTIONE AUTORIZZAZIONI ED ESONERI';


$daticrud['tabella'] = ("tbl_alunni");

$daticrud['larghezzatabella'] = "100%";
// Nome della tabella per visualizzazioni
$daticrud['aliastabella'] = "alunni";
// Campo con l'id univoco per la tabella
$daticrud['campochiave'] = "idalunno";

// Campi in base ai quali ordinare (specificare gli alias (14° valore nella descrizione del campo)
// se ci sono campi con lo stesso nome)
// Aggiungere DESC in caso di ordinamento inverso
$daticrud['campiordinamento'] = "cognome, nome, datanascita";
// Condizione di selezione, specificare solo 'true' se non ce ne sono
$daticrud['condizione'] = "idclasse<>0";
if ($soloclasse=='yes')
{
    $iddocente = $_SESSION['idutente'];
    $elencoclassi = estrai_classi_coordinate($iddocente, $con);

    $daticrud['condizione'] = ("idclasse IN ($elencoclassi)");
}




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
 * 17 - selesternadaaltro - condizione di selezione dei valori della tabella esterna basata su un valore ricavabile
 *      in fase di preparazione dei daticrud (Es. iddocente, tipoutente, ecc.)
 * 18 - clausdistinct - clausola distinct nella selezione dei valori della tabella esterna (1-sì, 0-no) 
 */
if ($soloclasse!='yes')

{
    $daticrud['campi'] = [
        ['0' => 'cognome', '1' => '1', '5' => 30, '6' => 'Cognome', '7' => 1, '8' => 'text', '15' => 1],
        ['0' => 'nome', '1' => '2', '5' => 30, '6' => 'Nome', '7' => 2, '8' => 'text', '15' => 1],
        ['0' => 'datanascita', '1' => '3', '5' => 10, '6' => 'Data nascita', '7' => 3, '8' => 'text', '10' => 1, '11' => '1', '13' => 0, '15' => 1],
        ['0' => 'idclasse', '1' => '4', '2' => 'tbl_classi', '3' => 'idclasse', '4' => 'anno,sezione,specializzazione', '5' => 0, '6' => 'Classe', '7' => 4, '15' => 1],
        ['0' => 'firmapropria', '1' => '5', '6' => 'Aut. firma propria', '7' => 5, '8' => 'boolean'],
        ['0' => 'autorizzazioni', '1' => '6', '6' => 'Autorizzazioni', '7' => 6, '8' => 'testo'],
        ['0' => 'autentrata', '1' => '7', '5' => 30, '6' => 'Autorizzazioni entrata', '7' => 7, '8' => 'text'],
        ['0' => 'autuscita', '1' => '8', '5' => 30, '6' => 'Autorizzazioni uscita', '7' => 8, '8' => 'text'],
        ['0' => 'autuscitaantclasse', '1' => '9', '6' => 'Aut. uscita ant. con classe', '7' => 9, '8' => 'boolean']
    ];
} else
{
    $daticrud['campi'] = [
        ['0' => 'cognome', '1' => '1', '5' => 30, '6' => 'Cognome', '7' => 1, '8' => 'text', '15' => 1],
        ['0' => 'nome', '1' => '2', '5' => 30, '6' => 'Nome', '7' => 2, '8' => 'text', '15' => 1],
        ['0' => 'datanascita', '1' => '3', '5' => 10, '6' => 'Data nascita', '7' => 3, '8' => 'text', '10' => 1, '11' => '1', '13' => 0, '15' => 1],
        ['0' => 'idclasse', '1' => '4', '2' => 'tbl_classi', '3' => 'idclasse', '4' => 'anno,sezione,specializzazione', '5' => 0, '6' => 'Classe', '7' => 4, '15' => 1],
        ['0' => 'autuscitaantclasse', '1' => 5, '6' => 'Aut. uscita ant. con classe', '7' => 5, '8' => 'boolean']
    ];
}
$_SESSION['daticrud'] = $daticrud;

header("location: ../lib/CRUD.php?suffisso=" . $_SESSION['suffisso']);

