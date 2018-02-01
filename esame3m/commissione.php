<?php session_start();


/*
Copyright (C) 2015 Pietro Tamburrano
Questo programma è un software libero; potete redistribuirlo e/o modificarlo secondo i termini della 
GNU Affero General Public License come pubblicata 
dalla Free Software Foundation; sia la versione 3, 
sia (a vostra scelta) ogni versione successiva.

Questo programma é distribuito nella speranza che sia utile 
ma SENZA ALCUNA GARANZIA; senza anche l'implicita garanzia di 
POTER ESSERE VENDUTO o di IDONEITA' A UN PROPOSITO PARTICOLARE. 
Vedere la GNU Affero General Public License per ulteriori dettagli.

Dovreste aver ricevuto una copia della GNU Affero General Public License
in questo programma; se non l'avete ricevuta, vedete http://www.gnu.org/licenses/
*/


@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

// istruzioni per tornare alla pagina di login se non c'� una sessione valida
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione


if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));

$idescommissione = stringa_html('idescommissione');

$registrata = stringa_html('registrata');
$titolo = "Gestione commissione";

$script = "";
stampa_head($titolo, "", $script, "E");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");

if ($registrata == '1')
{
    print "<center><font color='green'><b>Registrazione effettuata!</b></font><br>";
}
print ("
   <form method='post' action='commissione.php' name='commissione'>
   
   <table align='center'>");


print("   <tr>
      <td width='50%'><b>Commissione</b></td>
      <td width='50%'><SELECT NAME='idescommissione' ONCHANGE='commissione.submit()'><option value=''>&nbsp;</option>");

$query = "SELECT * FROM tbl_escommissioni ORDER BY denominazione";
$ris = mysqli_query($con, inspref($query)) or die ("Errore: " . inspref($query));
while ($rec = mysqli_fetch_array($ris))
{
    print "<option value='" . $rec['idescommissione'] . "' ";
    if ($idescommissione == $rec['idescommissione'])
    {
        print "selected";
    }
    print ">" . $rec['denominazione'] . "</option>";
}
if ($idescommissione == '1000000000')
{
    print "<option value='1000000000' selected>NUOVA COMMISSIONE</option></SELECT></td>";
}
else
{
    print "<option value='1000000000'>NUOVA COMMISSIONE</option></SELECT></td>";
}

print "</form>";
print "</table>";
print "<br>";

if ($idescommissione != '')
{

    print "<center><form action='inscommissione.php' method='post'>";
    print "<input type='hidden' name='idcommissione' value='$idescommissione'>";
    print "<table>";
    $denominazione = "";
    $nomepresidente = "";
    $cognomepresidente = "";
    $idsegretario ="";
    if ($idescommissione != '1000000000')
    {
        $query = "select * from tbl_escommissioni where idescommissione=$idescommissione";
        $ris = mysqli_query($con, inspref($query)) or die ("Errore: " . inspref($query));
        $rec = mysqli_fetch_array($ris);

        $denominazione = $rec['denominazione'];
        $nomepresidente = $rec['nomepresidente'];
        $cognomepresidente = $rec['cognomepresidente'];
        $idsegretario = $rec['idsegretario'];
    }


    print("   <tr>
      <td width='50%'><b>Denominazione</b></td>
      <td width='50%'><INPUT TYPE='text' name='denominazione' required size='30' maxlength='30' value='$denominazione'><br>");
    if ($idescommissione != '1000000000') print "<font color='red'>(inserire 'del' per cancellare)</font>";
    print("</td></tr>");
    print("   <tr>
      <td width='50%'><b>Npme presidente</b></td>
      <td width='50%'><INPUT TYPE='text' name='nomepresidente' required size='30' maxlength='30' value='$nomepresidente'></td></tr>");
    print("   <tr>
      <td width='50%'><b>Cognome presidente</b></td>
      <td width='50%'><INPUT TYPE='text' name='cognomepresidente' required size='30' maxlength='30' value='$cognomepresidente'></td></tr>");
    print("   <tr>
      <td width='50%'><b>Docenti</b></td>");
    print ("<td width='50%'>");

    if ($livello_scuola == '2')
    {
        $ultimoanno = '3';
    }
    else
    {
        $ultimoanno = '8';
    }

    $querydc = "select * from tbl_docenti where iddocente IN
                                    (SELECT iddocente from tbl_cattnosupp,tbl_classi WHERE tbl_cattnosupp.idclasse=tbl_classi.idclasse AND anno='$ultimoanno' AND iddocente<>1000000000)
                order by cognome, nome";

    $risdc = mysqli_query($con, inspref($querydc)) or die ("Errore: " . inspref($querydc, false));

    print ("<select multiple size=15 name='docenti[]'>");
    while ($recdc = mysqli_fetch_array($risdc))
    {
        $iddocente = $recdc['iddocente'];
        $nominativo = $recdc['cognome'] . " " . $recdc['nome'];
        $queryriccomm = "select * from tbl_escompcommissioni where iddocente=$iddocente and idcommissione=$idescommissione";
        $risriccomm = mysqli_query($con, inspref($queryriccomm));
        if (mysqli_num_rows($risriccomm) != 0)
        {
            $sele = ' selected';
        }
        else
        {
            $sele = '';
        }
        print "<option value='$iddocente'" . "$sele>$nominativo";

    }
    print "</select>";
    print ("</td></tr>");

    print("<tr>
      <td width='50%'><b>Segretario</b></td>");
    print ("<td width='50%'>");

    if ($livello_scuola == '2')
    {
        $ultimoanno = '3';
    }
    else
    {
        $ultimoanno = '8';
    }

    //
    //   SEGERETARIO
    //


    $querydc = "select * from tbl_docenti where iddocente IN
                                    (SELECT iddocente from tbl_cattnosupp,tbl_classi WHERE tbl_cattnosupp.idclasse=tbl_classi.idclasse AND anno='$ultimoanno' AND iddocente<>1000000000)
                order by cognome, nome";


    $risdc = mysqli_query($con, inspref($querydc)) or die ("Errore: " . inspref($querydc, false));

    print ("<select name='idsegretario'><option value=0>&nbsp;");
    while ($recdc = mysqli_fetch_array($risdc))
    {
        $iddocente = $recdc['iddocente'];
        $nominativo = $recdc['cognome'] . " " . $recdc['nome'];

        if ($idsegretario == $iddocente)
        {
            $sele = ' selected';
        }
        else
        {
            $sele = '';
        }
        print "<option value='$iddocente'" . "$sele>$nominativo";

    }
    print "</select>";
    print ("</td></tr>");

    print "</table >";
    if ($idescommissione == '1000000000')
    {
        print "<input type='submit' value='Registra commissione'>";
    }
    else
    {
        print "<input type='submit' value='Modifica commissione'>";
    }

    print "</center></form >";

}


mysqli_close($con);
stampa_piede(""); 

