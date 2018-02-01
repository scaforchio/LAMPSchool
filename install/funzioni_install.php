<?php
/**
 * Funzioni per le installazioni o gli aggiornamenti
 *
 * @copyright  Copyright (C) 2014 Renato Tamilio
 * @license    GNU Affero General Public License versione 3 o successivi; vedete agpl-3.0.txt
 */

/**
 * Controlla se il database esiste
 *
 * @param string $db_server nome del server
 * @param string $db_user nome dell'utente del db
 * @param string $db_password parola chiave per l'utente
 * @param string $db_name nome del database
 * @param string $pref prefisso delle tabelle
 * @return int
 *  0 - Esiste già un'installazione con il database e le tabelle
 *  1 - Non si riesce a connettere al database
 *  3 - Non esistono le tabelle con il prefisso dato
 */

function check_db($db_server, $db_user, $db_password, $db_name, $pref)
{
    $errore = 0;
    $con = mysqli_connect($db_server, $db_user, $db_password, $db_name);

    if (!$con)
    {
        $errore = 1;
    }
    else
    {

        if (mysqli_num_rows(mysqli_query($con, "SHOW TABLES LIKE '" . $pref . "tbl_parametri'")))
        {
            $errore = 0;
        }
        else
        {
            $errore = 3;
        }
    }
    mysqli_close($con);

    return $errore;
}


/**
 * Stampa la testata solo per la fase di installazione o di aggiornamento
 *
 * @param string $titolo
 * @param string $sottotitolo
 * @param string $funzione
 */


function stampa_testata_installer($titolo, $sottotitolo, $funzione)
{
    print "
<body>
 <div class='contenuto'>
  <div id='testata'>
  <div class='titolo'>$titolo</div>
  <div class='sottotitolo'>$sottotitolo</div>
 </div>
 <div id='funzione'>$funzione</div>
 <br/>";
}

/**
 * Restituisce i link per il codice CSS e il codice Javascript
 *
 * @return string contenuto i tag html
 */

function getCssJavascript()
{
    return "
<link rel='stylesheet' type='text/css' href='installer.css' />
<script src='installer.js'></script>";
}

/**
 * Fornisce i pulsanti con le pagine da chiamare
 *
 * @param string $indietro pagina per tornare indietro
 * @param string $avanti pagina per andare avanti
 * @param string $testoAvanti valore dell'etichetta del pulsante Avanti
 * @param string $testoIndietro valore dell'etichetta del pulsante Indietro
 * @return string il codice hrml dei pulsanti Indietro e Avanti
 */
function stampaPulsanti($indietro, $avanti, $testoIndietro = 'Indietro', $testoAvanti = 'Avanti')
{

    print "
<div class='contenitore'>";
    if ($indietro != "")
    {
        print "
    <div onclick='setAction(\"$indietro.php\")' class='pulsante'>$testoIndietro</div>";
    }
    if ($avanti != "")
    {
        print "
    <div onclick='setAction(\"$avanti.php\")' class='pulsante'>$testoAvanti</div>";
    }
    print "
</div>
";
}

/**
 * Esecuzione del file sql
 *
 * @param string $nomefilesql
 * @param array $credenzialiDB dati per la connessione al database
 * @return boolean true se non ci sono errori
 */

function remove_comments(&$output)
{
    $lines = explode("\n", $output);
    $output = "";

    // try to keep mem. use down
    $linecount = count($lines);

    $in_comment = false;
    for ($i = 0; $i < $linecount; $i++)
    {
        if (preg_match("/^\/\*/", preg_quote($lines[$i])))
        {
            $in_comment = true;
        }

        if (!$in_comment)
        {
            $output .= $lines[$i] . "\n";
        }

        if (preg_match("/\*\/$/", preg_quote($lines[$i])))
        {
            $in_comment = false;
        }
    }

    unset($lines);
    return $output;
}

//
// remove_remarks will strip the sql comment lines out of an uploaded sql file
//
function remove_remarks($sql)
{
    $lines = explode("\n", $sql);

    // try to keep mem. use down
    $sql = "";

    $linecount = count($lines);
    $output = "";

    for ($i = 0; $i < $linecount; $i++)
    {
        if (($i != ($linecount - 1)) || (strlen($lines[$i]) > 0))
        {
            if (isset($lines[$i][0]) && $lines[$i][0] != "#")
            {
                $output .= $lines[$i] . "\n";
            }
            else
            {
                $output .= "\n";
            }
            // Trading a bit of speed for lower mem. use here.
            $lines[$i] = "";
        }
    }

    return $output;

}

//
// split_sql_file will split an uploaded sql file into single sql statements.
// Note: expects trim() to have already been run on $sql.
//
function split_sql_file($sql, $delimiter)
{
    // Split up our string into "possible" SQL statements.
    $tokens = explode($delimiter, $sql);

    // try to save mem.
    $sql = "";
    $output = array();

    // we don't actually care about the matches preg gives us.
    $matches = array();

    // this is faster than calling count($oktens) every time thru the loop.
    $token_count = count($tokens);
    for ($i = 0; $i < $token_count; $i++)
    {
        // Don't wanna add an empty string as the last thing in the array.
        if (($i != ($token_count - 1)) || (strlen($tokens[$i] > 0)))
        {
            // This is the total number of single quotes in the token.
            $total_quotes = preg_match_all("/'/", $tokens[$i], $matches);
            // Counts single quotes that are preceded by an odd number of backslashes,
            // which means they're escaped quotes.
            $escaped_quotes = preg_match_all("/(?<!\\\\)(\\\\\\\\)*\\\\'/", $tokens[$i], $matches);

            $unescaped_quotes = $total_quotes - $escaped_quotes;

            // If the number of unescaped quotes is even, then the delimiter did NOT occur inside a string literal.
            if (($unescaped_quotes % 2) == 0)
            {
                // It's a complete sql statement.
                $output[] = $tokens[$i];
                // save memory.
                $tokens[$i] = "";
            }
            else
            {
                // incomplete sql statement. keep adding tokens until we have a complete one.
                // $temp will hold what we have so far.
                $temp = $tokens[$i] . $delimiter;
                // save memory..
                $tokens[$i] = "";

                // Do we have a complete statement yet?
                $complete_stmt = false;

                for ($j = $i + 1; (!$complete_stmt && ($j < $token_count)); $j++)
                {
                    // This is the total number of single quotes in the token.
                    $total_quotes = preg_match_all("/'/", $tokens[$j], $matches);
                    // Counts single quotes that are preceded by an odd number of backslashes,
                    // which means they're escaped quotes.
                    $escaped_quotes = preg_match_all("/(?<!\\\\)(\\\\\\\\)*\\\\'/", $tokens[$j], $matches);

                    $unescaped_quotes = $total_quotes - $escaped_quotes;

                    if (($unescaped_quotes % 2) == 1)
                    {
                        // odd number of unescaped quotes. In combination with the previous incomplete
                        // statement(s), we now have a complete statement. (2 odds always make an even)
                        $output[] = $temp . $tokens[$j];

                        // save memory.
                        $tokens[$j] = "";
                        $temp = "";

                        // exit the loop.
                        $complete_stmt = true;
                        // make sure the outer loop continues at the right point.
                        $i = $j;
                    }
                    else
                    {
                        // even number of unescaped quotes. We still don't have a complete statement.
                        // (1 odd and 1 even always make an odd)
                        $temp .= $tokens[$j] . $delimiter;
                        // save memory.
                        $tokens[$j] = "";
                    }

                } // for..
            } // else
        }
    }

    return $output;
}
function esecuzioneFile($nomefilesql, $credenzialiDB)
{

    $dbms_schema = $nomefilesql;

    $sql_query = @fread(@fopen($dbms_schema, 'r'), @filesize($dbms_schema)) or die('Problema lettura file sql ');
    $sql_query = remove_remarks($sql_query);
    $sql_query = split_sql_file($sql_query, ';');
    $connessione = mysqli_connect(
        $credenzialiDB['server'],
        $credenzialiDB['user'],
        $credenzialiDB['password'],
        $credenzialiDB['nomedb']);
    mysqli_set_charset($connessione, "utf8");


    //$i = 1;
    foreach ($sql_query as $sql)
    {
        $pref=$credenzialiDB['prefisso'];

        $sql=str_replace("tbl_",$pref."tbl_",$sql);

        mysqli_query($connessione, $sql) or die("Errore nella query" . $sql);

    }


}
/*function esecuzioneFile($nomefilesql, $credenzialiDB)
{
    $erroredb = false;

    if (file_exists($nomefilesql))
    {
        $sql = file($nomefilesql);
        $connection = mysqli_connect(
            $credenzialiDB['server'],
            $credenzialiDB['user'],
            $credenzialiDB['password'],
            $credenzialiDB['nomedb']);
        mysqli_set_charset($connection, "utf8");

        // mysqli_query($connection,"BEGIN");
        $query = '';

        foreach ($sql as $line)
        {
            $tsl = trim($line); // si cancellano gli spazi a inizio e fine riga

            if ($sql != '' && substr($tsl, 0, 2) != "--" && substr($tsl, 0, 1) != '#')
            { // Salta le righe con commenti
                $query .= $line;

                if (preg_match('/;\s*$/', $line))
                { // ; a fine riga obbligatorio
                    $query = str_replace("tbl_", $credenzialiDB['prefisso'] . "tbl_", $query);
                    mysqli_query($connection, $query);
                    $err = mysqli_error($connection);
                    $erroredb = !empty($err);

                    if ($erroredb)
                    {
                        print '<p>Errore: ' . $err . '</p>';
                        print '<p>Query fallita: ' . $query . '</p>';

                        // Ripristina il backup del file di configurazione
                        copy('php-ini.php.bkp', '../php-ini.php');

                        break;
                    }
                    $query = '';
                }
            }
        }
        //if (!$erroredb)
        //    mysqli_query($connection,"COMMIT");
        //else
        //    mysqli_query($connection,"ROLLBACK");

        mysqli_close($connection);
    }
    else
    {
        $erroredb = true;
    }

    return $erroredb;
}*/


/**
 * Esecuzione del file sql
 *
 * @param string $nomefilesql
 * @param array $credenzialiDB dati per la connessione al database
 * @return boolean true se non ci sono errori
 */


function esecuzionePHP($credenzialiDB, $versione)
{
    $erroredb = false;


    /*  if ($versione=="1.5")
      {

          $connection = mysqli_connect(
                  $credenzialiDB['server'],
                  $credenzialiDB['user'],
                  $credenzialiDB['password'],
                  $credenzialiDB['nomedb']);
          $query="ALTER TABLE ".$credenzialiDB['prefisso']."tbl_tipiesiti CHANGE id_tipoesito idtipoesito INT( 11 ) NOT NULL AUTO_INCREMENT ";

          try
          {
                 mysqli_query($connection,$query);
            }
          catch(Exception $e)
          {
                 $erroredb = $e;
            }
            mysqli_close($connection);
       } */
    return $erroredb;
}


