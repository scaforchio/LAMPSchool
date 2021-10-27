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

/* Programma per la visualizzazione dell'elenco delle tbl_classi. */

@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

// istruzioni per tornare alla pagina di login 

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}


$daticrud = $_SESSION['daticrud'];
$titolo = "Modifica record in tabella " . $daticrud['aliastabella'];
$script = "";
stampa_head($titolo, "", $script, "MAPSD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='CRUD.php'>ELENCO</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);

$id = stringa_html('id');

$daticrud = $_SESSION['daticrud'];
ordina_array_su_campo_sottoarray($daticrud['campi'], 7);
//Connessione al server SQL
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore connessione!");

//Esecuzione query
$query = "select * from " . $daticrud['tabella'] . " where " . $daticrud['campochiave'] . " = '" . $id . "'";
$risgen = eseguiQuery($con, $query);
$recgen = mysqli_fetch_array($risgen);
if ($id != 0)
    print "<form name='form1' action='CRUDmodregistra.php' method='POST'>";
else
    print "<form name='form1' action='CRUDinsregistra.php' method='POST'>";

print "<table border ='0' align='center'>";

$chiaveprincipale = $recgen[$daticrud['campochiave']];
$posarr = 0;
foreach ($daticrud['campi'] as $c)
{
    $posarr++;
    $disabilitato = "";
    if ($id == 0)
        $c[15] = 0;
    if ($c[15] == 1)
        $disabilitato = " disabled";
    if ($c[7] != 0)
    {

        print "<tr><td>" . $c[6];
        if ($c[9] != "")
            print "<br><small><small>" . $c[9] . "<big><big>";
        print "</td>";
        if ($c[10] == 1)
            $richiesto = " required";
        else
            $richiesto = "";
        if ($c[2] == '')
        {
            if ($c[8] == 'boolean')
            {
                $valore = $recgen[$c[0]];
                print "<td><select name='campo[]" . $posarr . "'$disabilitato>";

                if ($valore == 0)
                    print "<option value=0 selected>No</option><option value=1>S&igrave;</option>";
                else
                    print "<option value=0>No</option><option value=1 selected>S&igrave;</option>";
                if ($c[15] == 1)
                    print "<input type='hidden' name='campo[]" . $posarr . "' value='$valore'>";


                print "</select></td></tr>";
            } else if ($c[8] == 'testo')
            {
                $valore = $recgen[$c[0]];
                print "<td><textarea name='campo[]" . $posarr . "'$disabilitato>$valore</textarea>";
                if ($c[15] == 1)
                    print "<input type='hidden' name='campo[]" . $posarr . "' value='$valore'>";
                print "</td></tr>";
            } else
            {
                $valore = $recgen[$c[0]];
                print "<td>";
                print "<input type='" . $c[8] . "' value='" . $recgen[$c[0]] . "' name='campo[]" . $posarr . "' size='" . $c[5] . "' maxlength='" . $c[5] . "' min='" . $c[11] . "'  max='" . $c[12] . "'$richiesto$disabilitato>";
                if ($c[15] == 1)
                    print "<input type='hidden' name='campo[]" . $posarr . "' value='$valore'>";
                print "</td></tr>";
            }
        }
        else
        {
            $valore = $recgen[$c[0]];
            print "<td><select name='campo[]" . $posarr . "'$richiesto$disabilitato><option value='0'>&nbsp;</option>";
            // $query per selezione elementi della select
            if ($c[16] != '')
                $subquery1 = " and " . $c[3] . " in(" . $c[16] . "'$chiaveprincipale')";
            else
                $subquery1 = '';
            if ($c[17] != '')
                $subquery2 = " and " . $c[3] . " in(" . $c[17] . ")";
            else
                $subquery2 = '';
            if ($c[18]==1)
                $distinct="DISTINCT ";
            else
                $distinct="";
            $query = "select $distinct" . $c[3] . "," . $c[4] . " from " . $c[2] . " where true $subquery1 $subquery2 order by " . $c[4];
            print $query;
            $ris = eseguiQuery($con, $query);
            while ($rec = mysqli_fetch_array($ris))
            {
                $selected = "";
                if ($valore == $rec[$c[3]])
                    $selected = " selected";
                $elcampitabesterna = explode(",", $c[4]);
                $strvalori = "";
                foreach ($elcampitabesterna as $ctb)
                    $strvalori .= $rec[$ctb] . " ";
                print "<option value='" . $rec[$c[3]] . "'$selected>$strvalori</option>";
            }

            print "</select>";
            if ($c[15] == 1)
                print "<input type='hidden' name='campo[]" . $posarr . "' value='$valore'>";
            print "</td></tr>";
        }
    }
}

print "</table>";

print "<center><br><input type='hidden' name='id' value='$id'><input type='submit' name='registra' value='Registra'> </CENTER>";

print "</form>";

stampa_piede("");
mysqli_close($con);


