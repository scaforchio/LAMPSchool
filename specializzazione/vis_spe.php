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
    
   $titolo="Elenco $plesso_specializzazione";
    $script=""; 
    stampa_head($titolo,"",$script,"SDMAP");
    stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo","","$nome_scuola","$comune_scuola");
 


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
    $query="SELECT * FROM tbl_specializzazioni ORDER BY denominazione";
    if (!($ris=mysqli_query($con,inspref($query)))) 
    {
        print "\nQuery fallita";
    }   
    else
    {
        print "\n\t<CENTER><TABLE BORDER='1'>"; 
        print "\n\t\t<TR class='prima'><TD ALIGN='CENTER'><B>$plesso_specializzazione</B></TD><TD COLSPAN='2' ALIGN='CENTER'><B>Azioni</B></TD></TR>";
        while($dati=mysqli_fetch_array($ris)) 
        {
            print "\n\t\t<TR BGCOLOR='#cccccc'><TD>".$dati['denominazione']."</TD>";
            print "<TD><A HREF='mod_spe.php?idspe=". 
                    $dati['idspecializzazione']. "'><img src='../immagini/edit.png' title='Modifica'></A>&nbsp;
                       <A HREF='eli_spe.php?idspe=". 
                    $dati['idspecializzazione']."'><img src='../immagini/delete.png' title='Elimina'></A>";
            print "</TD></TR>";
        } 
        print "\n\t</CENTER></TABLE>";
    };  
    print "\n</BODY>";
    print "\n\n</HTML>";
    
    print "<CENTER><br/><form name='form2' action='nuo_spe.php' method='POST'>";
    print "<input type='submit' name='Nuovo' value='Nuovo'>";
    print "</form></CENTER>";   
    
       
    mysqli_close($con);
    
    stampa_piede("");

