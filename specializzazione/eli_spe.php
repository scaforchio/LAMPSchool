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
    
    $titolo="Cancellazione $plesso_specializzazione";
    $script=""; 
    stampa_head($titolo,"",$script,"SDMAP");
    stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='vis_spe.php'>Elenco</a> - $titolo","","$nome_scuola","$comune_scuola");
 
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
    $query="SELECT * FROM tbl_specializzazioni WHERE idspecializzazione=$idsp";
    if (!($ris=mysqli_query($con,inspref($query)))) 
    {
        print "\nQuery fallita";
    }   
    else
    {
        print "\n\t<CENTER><TABLE BORDER='0'>"; 
        if ($dati=mysqli_fetch_array($ris)) 
        {
            //Costruzione tabella di riepilogo
            print "\n\t<TR><TD COLSPAN='2' ALIGN='CENTER'><B>VUOI ELIMINARE I SEGUENTI DATI?</B></TD></TR>";    
            
            print "<TD ALIGN='CENTER'>$plesso_specializzazione:</TD><TD ALIGN='CENTER'>".$dati['denominazione']."</TD></TR>";
            print "<TR><TD ALIGN='CENTER'><FORM ACTION='del_spe.php' method='POST'>";
            print "<input type='hidden' name='idspe' value='$idsp'>";
            print "<INPUT TYPE='SUBMIT' VALUE='SI'>";
            print "</FORM></TD>";
            
            print "<TD ALIGN='CENTER'><FORM ACTION='vis_spe.php' method='POST'>";
            print "<INPUT TYPE='SUBMIT' VALUE='NO'>";
            print "</FORM>";
            print "\n\t\t</TD></TR>";
        } 
        else
        {
            //Errore e Link per tornare indietro alla pagina precedente
        }
        print "\n\t</TABLE></CENTER>";
    }; 
   
    mysqli_close($con);
    stampa_piede("");

