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
    
    $titolo="Conferma eliminazione materia";
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
    $idmateria = stringa_get_html('idmat');
    $query="SELECT * FROM tbl_materie WHERE idmateria=$idmateria";
    if (!($ris=mysqli_query($con,inspref($query))))
    {
        print "\nQuery fallita";
    }
    else
    {
        print "\n\t<CENTER><FORM id='form1' ACTION='del_mat.php' method='POST'><TABLE BORDER='0'>";
        if ($dati=mysqli_fetch_array($ris))
        {
            //Costruzione tabella di riepilogo
            print "\n\t<TR><TD COLSPAN='2' ALIGN='CENTER'><B>VUOI ELIMINARE I SEGUENTI DATI?</B></TD></TR>";    
            
            print "<TR><TD ALIGN='CENTER'>materia:</TD><TD>".$dati['denominazione']."</TD></TR>";
            print "<TR><TD ALIGN='CENTER'><br>";
            print "<input type='hidden' name='idmat' value='$idmateria'>";
            print "<INPUT TYPE='SUBMIT' VALUE='SI'>";
            print "</TD>";
            
            print "<TD ALIGN='CENTER'><br>";
            print "<INPUT TYPE='SUBMIT' VALUE='NO' onclick='setAction(\"vis_mat.php\")'>";
            print "</TD>\n\t</TR>";
        }
        else
        {
            //Errore e Link per tornare indietro alla pagina precedente
        }
        print "\n\t</TABLE></FORM></CENTER>";
    }
    
    stampa_piede("");
    mysqli_close($con);


