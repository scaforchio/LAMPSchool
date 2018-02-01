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

require_once '../php-ini'.$_SESSION['suffisso'].'.php';
require_once '../lib/funzioni.php';
//require_once '../lib/ db / query.php';

//$lQuery = LQuery::getIstanza();

// istruzioni per tornare alla pagina di login se non c'è una sessione valida
////session_start();

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
$iddocente = $_SESSION["idutente"];

if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=".$_SESSION['suffisso']);
    die;
}

$titolo = "Aggiunta nuovi utenti Moodle";
$script = "";
stampa_head($titolo, "", $script,"SMP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");

$con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));

print "<br><br><b>Alunni aggiunti:</b><br>";
$query="select * from tbl_alunni where idclasse<>0 order by idalunno";
$ris=mysqli_query($con,inspref($query)) or die("Errore ".inspref($query,false));

while ($rec=mysqli_fetch_array($ris))
{
    $usernamealunno=costruisciUsernameMoodle($rec['idalunno']);
    $idalunnomoodle=getIdMoodle($tokenservizimoodle,$urlmoodle,$usernamealunno);
    if ($idalunnomoodle=="")
    {
        $cognome=$rec['cognome'];
        $nome=$rec['nome'];
        $email=$usernamealunno."@dominioemailfittizio.it";
        $password=creapassword();
        $esito=creaUtenteMoodle($tokenservizimoodle,$urlmoodle,$usernamealunno,$password,$cognome,$nome,$email);
        print "<br>Esito: $esito";
        if ((strstr($esito,$usernamealunno)>-1))
        {
            print "<br>$cognome $nome $usernamealunno $password ".decodifica_classe($rec['idalunno'],$con);
        }
    }
    else
    {
        print "<br>$usernamealunno già esistente. ";
    }

}


print "<br><br><b>Docenti aggiunti:</b><br>";
$query="select * from tbl_docenti where iddocente>1000000000 order by iddocente";
$ris=mysqli_query($con,inspref($query)) or die("Errore ".inspref($query,false));

while ($rec=mysqli_fetch_array($ris))
{
    $usernamedocente=costruisciUsernameMoodle($rec['iddocente']);

    $iddocentemoodle=getIdMoodle($tokenservizimoodle,$urlmoodle,$usernamedocente);

    if ($iddocentemoodle=="")
    {

        $cognome=$rec['cognome'];
        $nome=$rec['nome'];
        if ($rec['email']!="")
            $email=$rec['email'];
        else
            $email=$usernamedocente."@dominioemailfittizio.it";
        $password=creapassword();

        $esito=creaUtenteMoodle($tokenservizimoodle,$urlmoodle,$usernamedocente,$password,$cognome,$nome,$email);
        print "<br>Esito: $esito";
        if ((strstr($esito,$usernamedocente)>-1))
        {
            print "<br>$cognome $nome $usernamedocente $password $email";
        }
    }
    else
    {
        print "<br>$usernamedocente già esistente. ";
    }

}



mysqli_close($con);
stampa_piede("");