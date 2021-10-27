<?php

require_once '../lib/req_apertura_sessione.php';


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

$titolo = "Cancellazione uscita anticipata classe";
$script = "";
stampa_head($titolo, "", $script, "MSP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);


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
$query = "SELECT * FROM tbl_usciteclassi,tbl_classi WHERE
            tbl_usciteclassi.idclasse=tbl_classi.idclasse AND iduscitaclasse=" . stringa_html('idusccla');
if (!($ris = eseguiQuery($con, $query)))
{
    print "\nQuery fallita";
} else
{
    print "\n\t<CENTER><TABLE BORDER='0'>";
    if ($dati = mysqli_fetch_array($ris))
    {
        //Costruzione tabella di riepilogo
        print "<TR><TD COLSPAN='2' ALIGN='CENTER'><B>VUOI ELIMINARE I SEGUENTI DATI?</B></TD></TR>";
        print "<TR><TD ALIGN='CENTER'>ENTRATA:</TD><TD ALIGN='CENTER'>" . $dati['data'] . " " . $dati['ora'] . "</TD></TR>";

        print "<TR><TD ALIGN='CENTER'>CLASSE:</TD><TD ALIGN='CENTER'>" . $dati['anno'] . "</TD></TR>";
        print "<TR><TD ALIGN='CENTER'>SEZIONE:</TD><TD ALIGN='CENTER'>" . $dati['sezione'] . "</TD></TR>";
        print "<TR><TD ALIGN='CENTER'>SPECIALIZZAZIONE:</TD><TD ALIGN='CENTER'>" . $dati['specializzazione'] . "</TD></TR>";
        print "<TR><TD COLSPAN='2'>";

        print "<TR><TD COLSPAN='2'>&nbsp;</TD></TR>";
        print "<TR><TD ALIGN='CENTER'><FORM ACTION='del_usccla.php' method='POST'>";
        print "<input type='hidden' name='idusccla' value='" . stringa_html('idusccla') . "'>";
        print "<INPUT TYPE='SUBMIT' VALUE='SI'>";
        print "</FORM></TD>";

        print "<TD ALIGN='CENTER'><FORM ACTION='vis_usccla.php' method='POST'>";
        print "<INPUT TYPE='SUBMIT' VALUE='NO'>";
        print "</FORM>";
        print "</TD></TR>";
    } else
    {
        //Errore e Link per tornare indietro alla pagina precedente
    }
    print "\n\t</TABLE></CENTER>";
};

stampa_piede("");
mysqli_close($con);


