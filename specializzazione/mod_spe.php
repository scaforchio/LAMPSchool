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

    /*Programma per la modifica delle specializzazione.*/

    @require_once("../php-ini".$_SESSION['suffisso'].".php");
    @require_once("../lib/funzioni.php");
    // istruzioni per tornare alla pagina di login 
    ////session_start();
    $tipoutente=$_SESSION["tipoutente"]; //prende la variabile presente nella sessione
    if ($tipoutente=="")
    {
       header("location: ../login/login.php?suffisso=".$_SESSION['suffisso']); 
       die;
    }    
    
    $titolo="Modifica $plesso_specializzazione";
    $script=""; 
    stampa_head($titolo,"",$script,"SDMAP");
    stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='vis_spe.php'>Elenco</a> - $titolo","","$nome_scuola","$comune_scuola");
 
    print("<br/><br/>"); 
    //Connessione al server SQL
    $con=mysqli_connect($db_server,$db_user,$db_password,$db_nome);
    if(!$con)
    {
        print("\n<h1> Connessione al server fallita </h1>");
        exit;
    };
    
    //Connessione al database
    $DB=true;
    if(!$DB)
    {
        print("\n<h1> Connessione al database fallita </h1>");
        exit;
    };  

    //Esecuzione query
    $idsp = stringa_html('idspe');
    $sql="select * from tbl_specializzazioni where idspecializzazione=$idsp";
    if (!($ris=mysqli_query($con,inspref($sql))))
    {  
        print("\n<h1> Query fallita </h1>");
        exit;
    }
    else 
    {
       $dati=mysqli_fetch_array($ris) ;    
       print "<form action='agg_spe.php' method='POST'>";
       print "<input type='hidden' name='idspecializzazione' value='".$dati['idspecializzazione']."'>";
       print "<CENTER><table border='0'>";
       print "<tr><td ALIGN='CENTER'> $plesso_specializzazione&nbsp;</td> <td ALIGN='CENTER'> ";
       print "<input type='text' name='denomin' value='".$dati['denominazione']."'>";
       print "</td></tr>";   

       print "<tr>"; 
       print "<td COLSPAN='2' ALIGN='CENTER'><br/><input type='submit' value='Aggiorna'></td> ";
       print "</form>";
       print "<TR><TD COLSPAN='2'>&nbsp;</TD></TR>";
       print "</table></CENTER>";   
    }
    
    mysqli_close($con);
    stampa_piede("");

