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
    
    $titolo="Revoca ruolo staff";
    $script=""; 
    stampa_head($titolo,"",$script,"SDMAP");
    stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo","","$nome_scuola","$comune_scuola");
    
    
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
    print "<form name='form1' action='rev_ruolo_s.php' method='POST'>";
    print "<CENTER><table border ='0'>"; 
    print "<tr> <td>
           <input type='text' name ='userid' size=30>";
    

    print "</td> </tr>";
    print "<tr><td COLSPAN='2'><CENTER>";
    print "<input type='submit' name='registra' value='Registra'> </CENTER>";
    print "</CENTER></td></TR><TR><TD COLSPAN='2'>&nbsp;</TD></TR>";
    print "</form>"; 
    
    print "</table></CENTER>";
    
    stampa_piede("");            
    mysqli_close($con);


