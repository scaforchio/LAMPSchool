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
    
    $titolo="Modifica tipologia esiti";
     $script="";
    stampa_head($titolo,"",$script,"SDMAP");
    stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='tabesiti.php'>ELENCO ESITI</a> - $titolo","","$nome_scuola","$comune_scuola");

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
    $idtipoesito = stringa_html('idtipoesito');

    $sql="select * from tbl_tipiesiti where idtipoesito=$idtipoesito";
    if (!($ris=mysqli_query($con,inspref($sql))))
    {
        die("\n<h1> Query fallita </h1>");
    }
    else
    {
        $dati=mysqli_fetch_array($ris);
        print "<form action='agg_tipoesito.php' method='POST'>";
        print "<input type='hidden' name='idtipoesito' value='".$dati['idtipoesito']."'>";
        print "<CENTER><table border='0'>";
        print "<tr><td> Materia&nbsp;</td> <td> ";
        print "<input type='text' name='descrizione' value='".$dati['descrizione']."' size=50 maxlength=50>";
        print "</td></tr>";
        print "<tr><td> Passaggio</td> <td> ";
        print "<select name='passaggio'>";
        for ($i=0;$i<=2;$i++)
        {
            if ($i==$dati['passaggio'])
                print "<option value='$i' selected>".decod_passaggio($i);
            else
                print "<option value='$i'>".decod_passaggio($i);
        }

        print "</td></tr>";
        

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


