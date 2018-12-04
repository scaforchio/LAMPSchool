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

//Programma per la modifica dell'elenco delle tbl_classi

@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

// istruzioni per tornare alla pagina di login 
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Modifica classe";
$script = "";
stampa_head($titolo, "", $script, "MAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='vis_cla.php'>ELENCO CLASSI</a> - $titolo", "", "$nome_scuola", "$comune_scuola");

$idclasse=stringa_html('idcla');
//Connessione al server SQL
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);
if (!$con)
{
    print("\n<h1> Connessione al server fallita </h1>");
    exit;
};

//Connessione al database
$DB = true;
if (!$DB)
{
    print("\n<h1> Connessione al database fallita </h1>");
    exit;
};

//Esecuzione query
$sql = "select * from tbl_classi where idclasse=" . stringa_html('idcla');
if (!($ris = mysqli_query($con, inspref($sql))))
{
    print("\n<h1> Query fallita </h1>");
    exit;
}
else
{
    $dati = mysqli_fetch_array($ris);
    print "<form action='agg_cla.php' method='POST'>";
    print "<input type='hidden' name='idclasse' value='" . $dati['idclasse'] . "'>";
    print "<CENTER><table border='0'>";
    print "<tr><td> Anno </td> <td> <SELECT name='anno'>";
    for ($i = 1; $i <= $numeroanni; $i++)
        if ($dati['anno'] == $i)
            print "<option value='$i' selected>$i";
        else
            print "<option value='$i'>$i";

    print "</td></tr>";

    //TABELLA SEZIONE nome=tbl_sezioni		  
    print "<tr><td> Sezione </td>";
    $q1 = "select * from tbl_sezioni order by denominazione";
    if (!($reply = mysqli_query($con, inspref($q1))))
    {
        print "<td>Query fallita nelle tbl_sezioni</td>";
    }
    else
    {
        print "<td> <SELECT NAME='tbl_sezioni'>";
        //Controlla se esiste le tbl_sezioni e stampa l'elenco altrimenti da l'errore
        if (mysqli_num_rows($reply) > 0)
        {
            while ($d1 = mysqli_fetch_array($reply))
            {
                if ($dati['sezione'] == $d1['denominazione'])
                    print "<option  value='" . $d1['idsezione'] . "' selected> " . $d1['denominazione'] . "";
                else
                    print "<option  value='" . $d1['idsezione'] . "'> " . $d1['denominazione'] . "";
            }
        }
        else
        {
            print "<option  value=0> Nessuna classe trovata";
        }
        print "</SELECT>";
    }
    print "</td></tr>";


    //TABELLA SPECIALIZZAZIONE nome=spec		  
    print "<tr><td> $plesso_specializzazione </td>";
    $q2 = "select * from tbl_specializzazioni order by denominazione";
    if (!($reply1 = mysqli_query($con, inspref($q2))))
    {
        print "<td>Query fallita nelle specializzazioni</td>";
    }
    else
    {
        print "<td> <SELECT NAME='spec'>";
        //Controlla se esiste le tbl_sezioni e stampa l'elenco altrimenti da l'errore
        if (mysqli_num_rows($reply1) > 0)
        {
            while ($d2 = mysqli_fetch_array($reply1))
            {
                if ($dati['specializzazione'] == $d2['denominazione'])
                    print "<option  value='" . $d2['idspecializzazione'] . "' selected> " . $d2['denominazione'] . "";
                else
                    print "<option  value='" . $d2['idspecializzazione'] . "'> " . $d2['denominazione'] . "";
            }
        }
        else
        {
            print "<option  value=0> Nessuna classe trovata";
        }
        print "</SELECT>";
    }
    print "</td></tr>";
    print "<tr><td>Ore settimanali</td><td><input type='text' name='ore' maxlength='2' size='2' value='" . $dati['oresett'] . "'></td></tr>";

    //INSERIMENTO COORDINATORE		  
    print "<tr><td>Coordinatore </td>";
    $q2 = "select * from tbl_docenti 
	          where iddocente in
	          (select distinct iddocente from tbl_cattnosupp where idclasse=" . stringa_html('idcla') . ") 
	          and iddocente<>1000000000 order by cognome,nome";
    if (!($reply1 = mysqli_query($con, inspref($q2))))
    {
        print "<td>" . inspref($q2) . "</td>";
    }
    else
    {
        print "<td> <SELECT NAME='coord'>";
        //Controlla se esiste le tbl_sezioni e stampa l'elenco altrimenti da l'errore
        print "<option  value='0'>&nbsp;</option>";
        if (mysqli_num_rows($reply1) > 0)
        {
            while ($d2 = mysqli_fetch_array($reply1))
            {
                if ($dati['idcoordinatore'] == $d2['iddocente'])
                    print "<option  value='" . $d2['iddocente'] . "' selected>" . $d2['cognome'] . " " . $d2['nome'] . "";
                else
                    print "<option  value='" . $d2['iddocente'] . "'>" . $d2['cognome'] . " " . $d2['nome'] . "";
            }
        }

        print "</SELECT>";
    }
    print "</td></tr>";

//INSERIMENTO RAPPRESENTANTE1		  
    print "<tr><td>Rappresentante 1</td>";
    $q2 = "select * from tbl_alunni 
	          where idclasse=$idclasse
            order by cognome, nome, datanascita"; 
	          
    if (!($reply1 = mysqli_query($con, inspref($q2))))
    {
        print "<td>" . inspref($q2) . "</td>";
    }
    else
    {
        print "<td> <SELECT NAME='rapp1'>";
        //Controlla se esiste le tbl_sezioni e stampa l'elenco altrimenti da l'errore
        print "<option  value='0'>&nbsp;</option>";
        if (mysqli_num_rows($reply1) > 0)
        {
            while ($d2 = mysqli_fetch_array($reply1))
            {
                if ($dati['rappresentante1'] == $d2['idalunno'])
                    print "<option  value='" . $d2['idalunno'] . "' selected>" . $d2['cognome'] . " " . $d2['nome'] . "";
                else
                    print "<option  value='" . $d2['idalunno'] . "'>" . $d2['cognome'] . " " . $d2['nome'] . "";
            }
        }

        print "</SELECT>";
    }
    print "</td></tr>";
    
    
    //INSERIMENTO RAPPRESENTANTE2	  
    print "<tr><td>Rappresentante 2</td>";
    $q2 = "select * from tbl_alunni 
	          where idclasse=$idclasse
            order by cognome, nome, datanascita"; 
    if (!($reply1 = mysqli_query($con, inspref($q2))))
    {
        print "<td>" . inspref($q2) . "</td>";
    }
    else
    {
        print "<td> <SELECT NAME='rapp2'>";
        //Controlla se esiste le tbl_sezioni e stampa l'elenco altrimenti da l'errore
        print "<option  value='0'>&nbsp;</option>";
        if (mysqli_num_rows($reply1) > 0)
        {
            while ($d2 = mysqli_fetch_array($reply1))
            {
                if ($dati['rappresentante2'] == $d2['idalunno'])
                    print "<option  value='" . $d2['idalunno'] . "' selected>" . $d2['cognome'] . " " . $d2['nome'] . "";
                else
                    print "<option  value='" . $d2['idalunno'] . "'>" . $d2['cognome'] . " " . $d2['nome'] . "";
            }
        }

        print "</SELECT>";
    }
    print "</td></tr>";
    
    
    print "<tr>";
    print "<br/>";
    print "<td COLSPAN='2' ALIGN='CENTER'><input type='submit' value='Aggiorna'></td> ";
    print "</form></tr>";

    print "</table></CENTER>";
}
stampa_piede("");
mysqli_close($con);

