<?php session_start();

/*
Copyright (C) 2015 Pietro Tamburrano
Questo programma è un software libero; potete redistribuirlo e/o modificarlo modificarlo 
* secondo i termini della 
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
$id_ut_doc = $_SESSION['idutente'];
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

// preparazione del link per tornare indietro nel registro di classe
$goback = goBackRiepilogoRegistro();

$titolo = "Selezione classe arrivo alunni promossi";
$script = "";
stampa_head($titolo, "", $script,"MA");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a>$goback[1] - $titolo", "", "$nome_scuola", "$comune_scuola");


$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));

$errore = 0;
$query = "select * from tbl_classiold where anno<>$numeroanni order by anno, specializzazione, sezione";
$ris = mysqli_query($con, inspref($query)) or $errore = mysqli_errno($con);
if ($errore == 1146)   // Tabella non esistente
{
    echo "<center><br><br><b><big>Importazione non effettuata o trasferimento alunni già avvenuto!</big></b></center>";
}
else
{
    if ($errore != 0)
    {
        echo "<center><br><br><b><big>Errore: " . inspref($query) . "</big></b></center>";
    }
    else
    {
        $progr = 0;
        echo "<br>";
        echo "<form action='riass_classi.php' method='post' name='seleclassi'>";
        echo "<table align='center' border='1'><tr class='prima'><td>Classe partenza</td><td>Classe destinazione</td><td>Passaggio per tutti</td></tr>";
        while ($rec = mysqli_fetch_array($ris))
        {
            $progr++;
            echo "<tr>";
            echo "<td>";
            echo $rec['anno'] . "&nbsp;" . $rec['sezione'] . "&nbsp;" . $rec['specializzazione'];
            echo "<input type='hidden' name='part$progr' value='" . $rec['idclasse'] . "'>";
            echo "</td>";
            echo "<td>";
            echo "<select name='dest$progr'><option value='0'>&nbsp</option>";
            // 17/08/2015 Non utilizzato per dare la possibilità di separare la gestione negli IC
            //  $query2 = "SELECT * FROM tbl_classi WHERE anno>".$rec['anno']." ORDER BY anno, specializzazione, sezione";
            $query2 = "SELECT * FROM tbl_classi WHERE anno>1 ORDER BY anno, specializzazione, sezione";
            $ris2 = mysqli_query($con, inspref($query2)) or die ("Errore nella selezione delle classi di destinazione!");
            while ($rec2 = mysqli_fetch_array($ris2))
            {
                echo "<option value='" . $rec2['idclasse'] . "'";
                if (($rec['anno'] + 1) == $rec2['anno'] && $rec['sezione'] == $rec2['sezione'] && $rec['specializzazione'] == $rec2['specializzazione'])
                {
                    echo " SELECTED";
                }
                echo ">" . $rec2['anno'] . "&nbsp;" . $rec2['sezione'] . "&nbsp;" . $rec2['specializzazione'] . "</option>";

            }
            echo "</select>";
            echo "</td>";
            echo "<td>";
            echo "<select name='tutti$progr'><option value='0' selected>no</option><option value='1'>s&igrave;</option>";
            echo "</select>";
            echo "</td>";
            echo "</tr>";
        }
        echo "<tr>";
        echo "<td>";
        echo "CLASSI TERMINALI";
        echo "</td>";
        echo "<td>";
        echo "<select name='terminali'><option value='0'>Elimina</option><option value='1'>Mantieni senza classe</option>";
        echo "</select>";
        echo "</td>";
        echo "<td>";
        echo "<select name='termcond'><option value='0' selected>no</option><option value='1'>s&igrave;</option>";
        echo "</select>";
        echo "</td>";
        echo "</tr>";
        echo "</table>";
        echo "<input type='hidden' name='numclassi' value='$progr'>";
        echo "<br/>";
        echo "<center><input type='submit' value='Riassegna classi'></center>";
        echo "</form>";
        echo "<br/>";
    }
}
stampa_piede();

