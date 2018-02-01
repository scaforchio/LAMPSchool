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

    /*Programma per la visualizzazione dell'elenco delle tbl_classi.*/

    require_once '../php-ini'.$_SESSION['suffisso'].'.php';
    require_once '../lib/funzioni.php';
    
    // istruzioni per tornare alla pagina di login
    ////session_start();
    $tipoutente=$_SESSION["tipoutente"]; //prende la variabile presente nella sessione
    if ($tipoutente=="")
    {
        header("location: ../login/login.php?suffisso=".$_SESSION['suffisso']);
        die;
    }
    
    $titolo="Nuova materia";
    $script = "<script>\n";
    $script = $script. "function setAction(url) {\n";
    $script = $script. "document.getElementById('form1').action=url;\n";
    $script = $script. "}\n";
    $script = $script. "</script>\n";
    stampa_head($titolo,"",$script,"SDMAP");
    stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='vis_mat.php'>ELENCO MATERIE</a> - $titolo","","$nome_scuola","$comune_scuola");
 
    //Connessione al server SQL
    $con=mysqli_connect($db_server,$db_user,$db_password,$db_nome);
    if(!$con)
    {
        die("\n<h1> Connessione al server fallita </h1>");
    }
    
    //Connessione al database
    $DB=true;
    if(!$DB)
    {
        die("\n<h1> Connessione al database fallita </h1>");
    }

    //Esecuzione query
    print "<form id='form1' action='reg_mat.php' method='POST'>";
    print "<CENTER><table border='0'>";
    print "<tr> <td>Denominazione&nbsp;</td><td> 
           <input type='text' name ='denomin' size=50 maxlength=50>";

    print "</td> </tr>";
    print "<tr> <td>Sigla</td><td>
           <input type='text' name ='sigla' size=5 maxlength=5>";

    print "</td> </tr>";
    print "<tr> <td>Tipo valutazioni</td><td>
           <select name ='tipovalutazione'>
           <option value='N'>Numeriche</option>
           <option value='G'>Giudizi</option>
           <option value='T'>Tutte</option>
           </select>";

    print "</td> </tr>";
    print "<tr> <td>Valutazioni intermedie</td><td>
           <select name ='valint'>
           <option value='U'>Unica</option>
           <option value='S'>Scritto</option>
           <option value='O'>Orale</option>
           <option value='P'>Pratico</option>
           <option value='SO'>S - O</option>
           <option value='SP'>S - P</option>
           <option value='OP'>O - P</option>
           <option value='SOP'>S - O - P</option>
           </select>";

    print "</td> </tr>";
    print "<tr><td COLSPAN='2' align='center'>";
    print "<input type='submit' name='registra' value='Registra'><br><br>";
    print "</td></TR>";
    print "<TR><TD COLSPAN='2' align='center'>";
    print "<INPUT TYPE='submit' VALUE='<<Indietro' onclick='setAction(\"vis_mat.php\")'>";
    print "</TD>";
    print "</tr>";
    print "</table></CENTER>";
    print "</form>";
    
    stampa_piede("");
    mysqli_close($con);


