<?php

session_start();

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
$tipoutente = $_SESSION['tipoutente']; //prende la variabile presente nella sessione
$idutente = $_SESSION['idutente'];
$idgiornatacolloquisas = "";

$titolo = "Elenco appuntamenti colloqui";
$script = "";
stampa_head($titolo, "", $script, "TSDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> -  $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));

$query = "select *
          from tbl_docenti
          where iddocente = $idutente";

$ris = eseguiQuery($con, $query);
while ($nom = mysqli_fetch_array($ris))
{
  $iddocente = $nom['iddocente'];
  $cognome = $nom['cognome'];
  $nome = $nom['nome'];
}

print "<center>
       <p><b> Elenco appuntamenti colloqui </b></p>
       <p><b> $cognome&nbsp;$nome </b></p>";
/*
print "       <form action = 'riepilogocolloqui.php' method = 'GET'>
        <p> Data: <select name = 'sceltaData' onchange = 'this.form.submit()'>
          <option value = 'null' selected></option>";

$query = "select distinct data, g.idgiornatacolloqui
          FROM tbl_slotcolloqui AS s, tbl_giornatacolloqui AS g
          WHERE s.idgiornatacolloqui = g.idgiornatacolloqui
          and s.idalunno = $idalunno
          order by data";

$ris = eseguiQuery($con, $query);
while ($nom = mysqli_fetch_array($ris))
{
  if($_GET['sceltaData'] == $nom[idgiornatacolloqui])
    print "<option value = '$nom[idgiornatacolloqui]' selected> $nom[data] </option>";
  else
    print "<option value = '$nom[idgiornatacolloqui]'> $nom[data] </option>";
}
print "</select></form></p>"; 

if(!empty($_GET['sceltaData']) && $_GET['sceltaData'] != 'null')
{
  $idgiornatacolloqui = $_GET['sceltaData'];
*/
  print "<table border = 1 align = center>
          <tr class = 'prima'>
            <td align='center'> Data </td>
            <td align='center'> Ora </td>
            <td align='center'> Alunno </td>
           
          </tr>";

  $query = "select distinct cognome, nome, s.orainizio, data, d.idalunno
            from tbl_alunni AS d, tbl_slotcolloqui AS s, tbl_giornatacolloqui as g
            where s.iddocente = $iddocente
		           and s.idalunno = d.idalunno
                           and s.idgiornatacolloqui = g.idgiornatacolloqui
                           and data>'".date('Y-m-d')."'
                     order by data, s.orainizio";

  
  $ris = eseguiQuery($con, $query);
  while ($nom = mysqli_fetch_array($ris))
  {
    $cognome = $nom["cognome"];
    $nome = $nom["nome"];
    $orainizio = $nom["orainizio"];
    $collegamentowebex = $nom["collegamentowebex"];
    $idalunno = $nom["idalunno"];
    $data= $nom["data"];

    print "<tr>";
    
    
    
    print  "<td> ".data_italiana($data)."</td>";
    print  "<td> ".substr($orainizio,0,5). "</td>";
    print "<td> ".estrai_dati_alunno($idalunno, $con)."";
    print "<br><small>". decodifica_classe(estrai_classe_alunno($idalunno, $con),$con)."<big>
            </td>";
    
    print "
          </tr>";
  }
  print "</table>"; /*

}
else
  print "";
*/
/*
print "<form action = 'prenotazionecolloqui.php' method = 'GET'>";
print "<br><input type='submit' color=black value='Torna alle prenotazioni'>";
print "</form>";
*/
print "</center>";
stampa_piede("");
mysqli_close($con);