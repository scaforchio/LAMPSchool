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

    /*Programma per la modifica delle tbl_materie.*/

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
    
    $titolo="Modifica materia";
     $script="";
    stampa_head($titolo,"",$script,"SDMAP");
    stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='vis_mat.php'>ELENCO MATERIE</a> - $titolo","","$nome_scuola","$comune_scuola");

    print("<br/><br/>");
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
    $idmateria = stringa_get_html('idmat');

    $sql="select * from tbl_materie where idmateria=$idmateria";
    if (!($ris=mysqli_query($con,inspref($sql))))
    {
        die("\n<h1> Query fallita </h1>");
    }
    else
    {
        $dati=mysqli_fetch_array($ris);
        print "<form action='agg_mat.php' method='POST'>";
        print "<input type='hidden' name='idmateria' value='".$dati['idmateria']."'>";
        print "<CENTER><table border='0'>";
        print "<tr><td> Materia&nbsp;</td> <td> ";
        print "<input type='text' name='denomin' value='".$dati['denominazione']."' size=50 maxlength=50>";
        print "</td></tr>";
        print "<tr><td> Sigla </td> <td> ";
        print "<input type='text' name='sigla' value='".$dati['sigla']."' size=5 maxlength=5>";
        print "</td></tr>";
        
        $valint=substr($dati['tipovalutazione'],1);
        print "<tr> <td>Tipo valutazioni</td><td>
           <select name ='tipovalutazione'>";
        if ($dati['tipovalutazione'][0]=='N')   
           print "<option value='N' selected>Numeriche</option>";
        else
           print "<option value='N'>Numeriche</option>";   
        if ($dati['tipovalutazione'][0]=='G')   
           print "<option value='G' selected>Giudizi</option>";
        else
           print "<option value='G'>Giudizi</option>"; 
        
        if ($dati['tipovalutazione'][0]=='T')   
           print "<option value='T' selected>Tutte</option>";
        else
           print "<option value='T'>Tutte</option>";
        print "</select>";
        print "</td> </tr>";
        
        print "<tr> <td>Valutazioni intermedie</td><td>
           <select name ='valint'>";
        if ($valint=='U')
           echo "<option value='U' selected>Unica</option>";
        else
           echo "<option value='U'>Unica</option>"; 
        if ($valint=='S')
           echo "<option value='S' selected>Scritto</option>";
        else
           echo "<option value='S'>Scritto</option>"; 
        if ($valint=='O')
           echo "<option value='O' selected>Orale</option>";
        else
           echo "<option value='O'>Orale</option>"; 
        if ($valint=='P')
           echo "<option value='P' selected>Pratico</option>";
        else
           echo "<option value='P'>Pratico</option>"; 
        if ($valint=='SO')
           echo "<option value='SO' selected>S - O</option>";
        else
           echo "<option value='SO'>S - O</option>"; 
        if ($valint=='SP')
           echo "<option value='SP' selected>S - P</option>";
        else
           echo "<option value='SP'>S - P</option>"; 
        if ($valint=='OP')
           echo "<option value='OP' selected>O - P</option>";
        else
           echo "<option value='OP'>O - P</option>";      
        if ($valint=='SOP')
           echo "<option value='SOP' selected>S - O - P</option>";
        else
           echo "<option value='SOP'>S - O - P</option>";      
        
       echo "
           </select>";

    print "</td> </tr>";
        
        
        print "<tr>";
        print "<td COLSPAN='2' ALIGN='CENTER'><br/><input type='submit' value='Aggiorna'></td> ";
        print "</form>";
        print "<TR><TD COLSPAN='2'>&nbsp;</TD></TR>";

        print "</table></CENTER>";
    }
    mysqli_close($con);
    stampa_piede("");

