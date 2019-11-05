<?php

session_start();


/*
  Copyright (C) 2015 Pietro Tamburrano
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

/* Programma per la visualizzazione del menu principale. */

@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

// istruzioni per tornare alla pagina di login se non c'è una sessione valida
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
$idesterno = "";
if ($tipoutente == "")
{
    header("location: login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}


$titolo = "BACKUP DATI";
$script = "";
stampa_head($titolo, "", $script, "MP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");

// SVUOTO LA CARTELLA BUFFER DAI VECCHI FILE SQL E CSV
//  svuota_cartella("$cartellabuffer/", ".sql");
// svuota_cartella("$cartellabuffer/", ".csv");
// svuota_cartella("$cartellabuffer/", ".txt");

$arrbkp = is_stringa_html('tabbkp') ? stringa_html('tabbkp') : array();
$tabelle = array();
foreach ($arrbkp as $ambito)
{
    if ($ambito == 'ana')
    {
        $tabelle[] = 'tbl_docenti';
        $tabelle[] = 'tbl_tutori';
        $tabelle[] = 'tbl_alunni';
        $tabelle[] = 'tbl_cambiamenticlasse';
        $tabelle[] = 'tbl_amministrativi';
        $tabelle[] = 'tbl_cattnosupp';
        $tabelle[] = 'tbl_cattsupp';
        $tabelle[] = 'tbl_utenti';
        $tabelle[] = 'tbl_gruppi';
        $tabelle[] = 'tbl_gruppialunni';
        $tabelle[] = 'tbl_confermatelegram';
    }
    if ($ambito == 'avv')
    {
        $tabelle[] = 'tbl_sms';
        $tabelle[] = 'tbl_testisms';
        $tabelle[] = 'tbl_sospinviosms';
        $tabelle[] = 'tbl_prenotazioni';
        $tabelle[] = 'tbl_orericevimento';
        $tabelle[] = 'tbl_circolari';
        $tabelle[] = 'tbl_diffusionecircolari';
        $tabelle[] = 'tbl_festivita';
        $tabelle[] = 'tbl_avvisi';
        $tabelle[] = 'tbl_annotazioni';
        $tabelle[] = 'tbl_logacc';
        $tabelle[] = 'tbl_paramcomunicazpers';
        $tabelle[] = 'tbl_diariocl';
        $tabelle[] = 'tbl_sospensionicolloqui';
        $tabelle[] = 'tbl_collegamenti';
        $tabelle[] = 'tbl_menu';
    }
    if ($ambito == 'pro')
    {
        $tabelle[] = 'tbl_competdoc';
        $tabelle[] = 'tbl_competscol';
        $tabelle[] = 'tbl_abildoc';
        $tabelle[] = 'tbl_abilscol';
        $tabelle[] = 'tbl_competalu';
        $tabelle[] = 'tbl_abilalu';
        $tabelle[] = 'tbl_tipoprog';
        $tabelle[] = 'tbl_compob';
        $tabelle[] = 'tbl_compsubob';
    }
    if ($ambito == 'ass')
    {
        $tabelle[] = 'tbl_assenze';
        $tabelle[] = 'tbl_ritardi';
        $tabelle[] = 'tbl_usciteanticipate';
        $tabelle[] = 'tbl_asslezione';
        $tabelle[] = 'tbl_deroghe';
        $tabelle[] = 'tbl_derogheinserimento';
        $tabelle[] = 'tbl_presenzeforzate';
        $tabelle[] = 'tbl_timbrature';
        $tabelle[] = 'tbl_entrateclassi';
        $tabelle[] = 'tbl_autorizzazioniuscite';
    }

    if ($ambito == 'val')
    {

        $tabelle[] = 'tbl_valutazioniintermedie';
        $tabelle[] = 'tbl_valutazioniabilcono';
        $tabelle[] = 'tbl_valutazionicomp';
        $tabelle[] = 'tbl_valutazioniobcomp';
        $tabelle[] = 'tbl_osssist';
        $tabelle[] = 'tbl_esami3m';
        $tabelle[] = 'tbl_escommissioni';
        $tabelle[] = 'tbl_escompcommissioni';
        $tabelle[] = 'tbl_esesiti';
        $tabelle[] = 'tbl_esmaterie';
        $tabelle[] = 'tbl_certcompcompetenze';
        $tabelle[] = 'tbl_certcomplivelli';
        $tabelle[] = 'tbl_certcompvalutazioni';
        $tabelle[] = 'tbl_certcompproposte';
        $tabelle[] = 'tbl_consorientativi';
    }
    if ($ambito == 'scr')
    {
        $tabelle[] = 'tbl_scrutini';
        $tabelle[] = 'tbl_esiti';
        $tabelle[] = 'tbl_proposte';
        $tabelle[] = 'tbl_valutazionifinali';
        $tabelle[] = 'tbl_giudizi';
    }
    if ($ambito == 'not')
    {
        $tabelle[] = 'tbl_notealunno';
        $tabelle[] = 'tbl_noteclasse';
        $tabelle[] = 'tbl_noteindalu';
    }

    if ($ambito == 'lez')
    {
        $tabelle[] = 'tbl_firme';
        $tabelle[] = 'tbl_lezioni';
        $tabelle[] = 'tbl_lezionigruppi';
        $tabelle[] = 'tbl_lezionicert';
        $tabelle[] = 'tbl_assemblee';
        $tabelle[] = 'tbl_richiesteferie';
        $tabelle[] = 'tbl_recuperipermessi';
    }

    if ($ambito == 'tab')
    {
        $tabelle[] = 'tbl_classi';
        $tabelle[] = 'tbl_sezioni';
        $tabelle[] = 'tbl_specializzazioni';
        $tabelle[] = 'tbl_materie';
        $tabelle[] = 'tbl_tipiesiti';
        $tabelle[] = 'tbl_parametri';
        $tabelle[] = 'tbl_testi';
        $tabelle[] = 'tbl_tipidocumenti';
        $tabelle[] = 'tbl_materiesidi';
        $tabelle[] = 'tbl_orario';
        $tabelle[] = 'tbl_goindirizzo';
        $tabelle[] = 'tbl_gopercorso';
        $tabelle[] = 'tbl_gosettore';
        $tabelle[] = 'tbl_torlist';
    }

    if ($ambito == 'com')
    {
        $tabelle[] = 'tbl_comuni';
        $tabelle[] = 'tbl_province';
    }

    if ($ambito == 'pdf')
    {
        $tabelle[] = 'tbl_documenti';
    }
}
$tabs = array();
foreach ($tabelle as $tab)
    $tabs[] = inspref($tab);
header("location: " . backup_tables($db_server, $db_user, $db_password, $db_nome, $tabs, $cartellabuffer));


/*
  $db_server="127.0.0.1";
  $db_user="root";
  $db_password="";
  $db_nome="lampschool2012itis";
  $prefisso_tabelle="'";
 */


/* backup the db OR just a table */

function backup_tables($host, $user, $pass, $name, $tables, $cartellabuffer)
{

    $link = mysqli_connect($host, $user, $pass, $name);

    $tables = is_array($tables) ? $tables : explode(',', $tables);
    // print "tables ".$row;// TTTT
    $tabcontr = array_reverse($tables);
    $return = 'SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";';
    $return .= "\n\n";

    //cycle through
    foreach ($tabcontr as $table)
    {
        $return .= 'DROP TABLE IF EXISTS ' . $table . ';';
    }

    foreach ($tables as $table)
    {

        //print $table; // TTTT
        $result = mysqli_query($link, "SELECT * FROM $table");
        $num_fields = mysqli_num_fields($result);

        //	$return.= 'DROP TABLE IF EXISTS '.$table.';';
        // print $return; // TTTT
        $row2 = mysqli_fetch_row(mysqli_query($link, 'SHOW CREATE TABLE ' . $table));
        $return .= "\n\n" . $row2[1] . ";\n\n";

        //   for ($i = 0; $i < $num_fields; $i++)
        //   {

        $numerorighe = mysqli_num_rows($result);
        if ($numerorighe > 0)
            $return .= "INSERT INTO $table VALUES";
        $contarighe = 0;
        while ($row = mysqli_fetch_row($result))
        {
            $contarighe++;
            $return .= '(';
            for ($j = 0; $j < $num_fields; $j++)
            {
                $row[$j] = addslashes($row[$j]);
                // $row[$j] = preg_replace("\n","\\n",$row[$j]);
                if (isset($row[$j]))
                {
                    $return .= '"' . $row[$j] . '"';
                } else
                {
                    $return .= '""';
                }
                if ($j < ($num_fields - 1))
                {
                    $return .= ',';
                }
            }
            if ($contarighe < $numerorighe)
                $return .= "),\n";
            else
                $return .= ");\n";
        }

        //  }
        $return .= "\n";
    }

    //save file
    $nomefile = $cartellabuffer . '/lampschool-' . $_SESSION['suffisso'] . "-" . date("YmdHis") . '.sql';
    $handle = fopen($nomefile, 'w+');
    fwrite($handle, $return);
    fclose($handle);
    mysqli_close($link);

    return $nomefile;
}
