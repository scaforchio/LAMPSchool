<?php

require_once '../lib/req_apertura_sessione.php';

/*
  Copyright (C) 2015 Pietro Tamburrano
  Copyright (C) 2023 Pietro Tamburrano, Vittorio Lo Mele
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

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione

$idutentealunno = $_SESSION["idutente"];

if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Collegamenti videolezioni docenti";
$script = "
<style>
.lscontainer {
    margin-left: 10px;
    margin-right: 10px;
}
</style>";
stampa_head_new($titolo, "", $script, "L");
stampa_testata_new("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);


$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));


$idclasse = estrai_classe_alunno($idutentealunno-2100000000, $con);

$nomeclasse = decodifica_classe($idclasse, $con);


print "<b><center>Docenti classe $nomeclasse </b></center> <br>" ;


$query = "select distinct cognome, nome,tbl_docenti.iddocente as iddoc, collegamentowebex from tbl_cattnosupp,tbl_docenti
               where tbl_cattnosupp.iddocente=tbl_docenti.iddocente
               and tbl_cattnosupp.idclasse=$idclasse
               
               and tbl_cattnosupp.iddocente!=1000000000
               order by cognome, nome";


$ris= eseguiQuery($con, $query);

print "<center>";
print "<table class='table table-striped table-bordered'>";
print "<tr><td>Docente</td><td>Coll. WebEx</td></tr>";
while ($rec= mysqli_fetch_array($ris))
{
    print "<tr>";
    $iddocente=$rec['iddoc'];
    $query="select idmateria from tbl_cattnosupp where idclasse=$idclasse and iddocente=$iddocente";
    $ris2= eseguiQuery($con,$query);
    print "<td>".$rec['cognome']." ".$rec['nome'];
    while($rec2=mysqli_fetch_array($ris2))
    {       
        print"<br><small><small>". decodifica_materia($rec2['idmateria'], $con)."<big><big>";
    }
    print "</td>";
    print "<td align='center'>";
    
    if ($rec['collegamentowebex']!='')
        print "<a href='".$rec['collegamentowebex']."' target='_BLANK'><img src='../immagini/webex.ico'></a>";
    else
        print "&nbsp;";
    print "</td>";
    print "</tr>";
}
print "</table>";

mysqli_close($con);
stampa_piede_new("",false); 


