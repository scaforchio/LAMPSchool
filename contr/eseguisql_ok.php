<?php session_start();


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

/*Programma per la visualizzazione del menu principale.*/

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


$titolo = "ESECUZIONE SQL";
$script = "";
stampa_head($titolo, "", $script,"PMSD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");

$querydaeseguire=$_POST["que"];
// SVUOTO LA CARTELLA BUFFER DAI VECCHI FILE SQL E CSV
//  svuota_cartella("$cartellabuffer/", ".sql");
// svuota_cartella("$cartellabuffer/", ".csv");
// svuota_cartella("$cartellabuffer/", ".txt");
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));
esecuzioneSQL($querydaeseguire,$con);
/*
$ris = mysqli_query($con,inspref($querydaeseguire)) or die("Errore: ".mysqli_error($con)." <br> Query: ".inspref($querydaeseguire,false));

$numrec=mysqli_affected_rows($con);

print "<br><center><b>Query eseguita correttamente, righe influenzate: $numrec !</b></center>";*/
print "<form action='eseguisql.php' method='POST'>";

print "<CENTER><br><table border='0'>";
// print "<tr><td ALIGN='CENTER'><b>".$dati['parametro']."</b></td></tr>";
print "<tr><td ALIGN='CENTER'><b></b></td></tr>";
print '<tr><td ALIGN="CENTER"><input type="hidden" name="que" value="'.$querydaeseguire.'"></td></tr>';
print "<tr><td ALIGN='CENTER'><input type='submit' value='Ritorna'></td></tr>";
print "</table></form>";

mysqli_close($con);
stampa_piede("");

function split_sql_commands($sql, $delimiter)
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

function esecuzioneSQL($comandisql, $con)
{

    $sql_query = split_sql_commands($comandisql, ';');
    mysqli_set_charset($con, "utf8");
    //$i = 1;
    foreach ($sql_query as $sql)
    {

        $ris=mysqli_query($con, inspref($sql)) or die("Errore: ".mysqli_error($con)." <br> Query: ".inspref($sql,false));

        print "<br><b>Eseguito: ".inspref($sql,false)."</b><br><br>";
        $numrecinfl=mysqli_affected_rows($con);
        //print "Ris: $ris";
        if (is_object($ris))
        {

            print "<table border='1' align='center'>";

            $primo = true;
            while($row = mysqli_fetch_assoc($ris))
            {
                if($primo)
                {
                    $keys = array_keys($row);
                    print "<tr>";
                    foreach($keys as $key)
                    {
                        print "<td><b>$key</b></td>";
                    }
                    print "</tr>";
                    $primo = false;
                }
                print "<tr>";
                foreach($row as $column)
                {
                    print "<td>$column</td>";
                }
                print "</tr>";
            }
            print "</table>";
        }
        else
        {
            print "Righe influenzate dalla query: $numrecinfl";
        }


        //if ()

        // print "Righe influenzate: $numrec !<br>";

    }


}