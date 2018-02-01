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

$titolo = "Visualizzazione Cattedre";
$script = "";
stampa_head($titolo, "", $script,"MPAS");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");

$ordinamento = stringa_html('ordinamento');

$con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));
 

print "<form name='viscatt' action='vis_cattedre.php' method='post'>";
print "<center>Ordinamento:"; 
print "<select name='ordinamento'  ONCHANGE='viscatt.submit()'>";
if ($ordinamento=="doc" | $ordinamento=="") $seledoc=' selected';else $seledoc='';
if ($ordinamento=="mat") $selemat=' selected';else $selemat='';
if ($ordinamento=="cla") $selecla=' selected';else $selecla='';
print "<option value='doc'$seledoc>Docenti</option>
       <option value='mat'$selemat>Materie</option>
       <option value='cla'$selecla>Classi</option>";
print "</select></center>";
print "</form>";


print "<center><br><b>Cattedre inserite</b></center><br>";

if ($ordinamento=='doc' | $ordinamento=='')

$query = "SELECT 
idcattedra,tbl_cattnosupp.idalunno,tbl_docenti.iddocente,cognome,nome,tbl_cattnosupp.idmateria,tbl_classi.idclasse,tbl_classi.anno,tbl_classi.sezione,tbl_classi.specializzazione,tbl_materie.denominazione
 FROM 
tbl_cattnosupp,tbl_classi,tbl_materie,tbl_docenti
 WHERE 
tbl_cattnosupp.idclasse=tbl_classi.idclasse 
and tbl_cattnosupp.idmateria=tbl_materie.idmateria 
and tbl_cattnosupp.iddocente=tbl_docenti.iddocente 
and tbl_cattnosupp.iddocente<>1000000000
 ORDER BY 
cognome,nome,denominazione,anno,specializzazione,sezione";
else if ($ordinamento=='mat')
$query = "SELECT 
idcattedra,tbl_cattnosupp.idalunno,tbl_docenti.iddocente,cognome,nome,tbl_cattnosupp.idmateria,tbl_classi.idclasse,tbl_classi.anno,tbl_classi.sezione,tbl_classi.specializzazione,tbl_materie.denominazione
 FROM 
tbl_cattnosupp,tbl_classi,tbl_materie,tbl_docenti
 WHERE 
tbl_cattnosupp.idclasse=tbl_classi.idclasse 
and tbl_cattnosupp.idmateria=tbl_materie.idmateria 
and tbl_cattnosupp.iddocente=tbl_docenti.iddocente 
and tbl_cattnosupp.iddocente<>1000000000
 ORDER BY 
denominazione,cognome,nome,anno,specializzazione,sezione";
else
$query = "SELECT 
idcattedra,tbl_cattnosupp.idalunno,tbl_docenti.iddocente,cognome,nome,tbl_cattnosupp.idmateria,tbl_classi.idclasse,tbl_classi.anno,tbl_classi.sezione,tbl_classi.specializzazione,tbl_materie.denominazione
 FROM 
tbl_cattnosupp,tbl_classi,tbl_materie,tbl_docenti
 WHERE 
tbl_cattnosupp.idclasse=tbl_classi.idclasse 
and tbl_cattnosupp.idmateria=tbl_materie.idmateria 
and tbl_cattnosupp.iddocente=tbl_docenti.iddocente 
and tbl_cattnosupp.iddocente<>1000000000
 ORDER BY 
anno,specializzazione,sezione,denominazione,cognome,nome";

$ris = mysqli_query($con,inspref($query)) or die("Errore: ".inspref($query));

if (mysqli_num_rows($ris)>0) 
{    
    print "<table border=1 align=center>";
    print "<tr class='prima'><td>Docente</td><td>Classe</td><td>Materia</td><td>Alunno/Gruppo</td></tr>";

    while ($lez=mysqli_fetch_array($ris)){
        $ann=$lez['anno'];
        $sez=$lez['sezione'];
        $spe=$lez['specializzazione'];
        $mat=$lez['denominazione'];
        $cat=$lez['idcattedra'];
        $cog=$lez['cognome'];
        $nom=$lez['nome'];
        if ($lez['idalunno']!=0)
           $alu=estrai_dati_alunno($lez['idalunno'],$con);
        else
           $alu=""; 
       // print "<tr class='oddeven'><td>$cog $nom</td><td>$ann $sez $spe</td><td>$mat</td><td>$alu</td></tr>";   
        // VERIFICO SE LA CATTEDRA E' LEGATA AD UN GRUPPO 
        // PER LEZIONI 'SPECIALI'
        $querygru="select distinct descrizione from tbl_gruppialunni,tbl_gruppi,tbl_alunni where
                 tbl_gruppialunni.idalunno=tbl_alunni.idalunno and tbl_gruppialunni.idgruppo=tbl_gruppi.idgruppo
                 and iddocente=".$lez['iddocente'].
                 " and idmateria=".$lez['idmateria'].
                 " and idclasse=".$lez['idclasse']; 
        $risgru=mysqli_query($con,inspref($querygru)) or die("Errore: ".inspref($querygru));
        if (mysqli_num_rows($risgru)>0)  // Si tratta di una cattedra speciale
        {
			  while($rec=mysqli_fetch_array($risgru))
			  {     
				  $alu=$rec['descrizione'];       
				  print "<tr class='oddeven'><td>$cog $nom</td><td>$ann $sez $spe</td><td>$mat</td><td>$alu</td><td></td></tr>";
			  }
		  }
		  else // Si tratta di una cattedra normale o di sostegno
		  	  print "<tr class='oddeven'><td>$cog $nom</td><td>$ann $sez $spe</td><td>$mat</td><td>$alu</td><td><a href='eli_cat.php?idcattedra=$cat'><img src='../immagini/delete.png'></a></td></tr>";    
    }
    print "</table>";
}
else
{
    print '<p>Non ci sono cattedre.</p>';
}
mysqli_close($con);
stampa_piede(""); 

