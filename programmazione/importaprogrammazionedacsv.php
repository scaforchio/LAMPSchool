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
require_once '../php-ini'.$_SESSION['suffisso'].'.php';
require_once '../lib/funzioni.php';


// istruzioni per tornare alla pagina di login
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
$iddocente= $_SESSION['idutente'];


if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=".$_SESSION['suffisso']);
    die;
}
stampa_head("Carica programmazione da CSV","","");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - Importazione programmazione classe","","$nome_scuola","$comune_scuola");

$arrpar=array('1','1');
$stringaparametri = implode("!",$arrpar);

if (is_stringa_html('par'))
{
    $stringaparametri = stringa_html('par');

    $arrpar=explode("!", $stringaparametri);
}

print("
<center>

    <form enctype='multipart/form-data' method='post' action='importaprogrammazionedacsvins.php'>
        <input type='hidden' name='MAX_FILE_SIZE' value='1000000'>
        <table>
            <tr>
                <td>Carica il file (formato .csv o .txt) :&nbsp;</td>
                <td><input type='file' name='filenomi' enctype='multipart/form-data'></td>
            </tr>
            <tr>
                <td>Scegli il separatore dei dati :&nbsp;</td>
                <td>");
print ("<select name='separatore'>");
if ($arrpar[0]=='1')                
   print("<option value=';' selected> ; (punto e virgola)");
else 
   print("<option value=';'> ; (punto e virgola)");   
if ($arrpar[0]=='2')                
   print("<option value=',' selected> , (virgola)");
else 
   print("<option value=','> , (virgola)");   
if ($arrpar[0]=='3')                
   print("<option value='|' selected> | (pipe)");
else 
   print("<option value='|'> | (pipe)");   
if ($arrpar[0]=='4')                
   print("<option value='/' selected> / (barra)");
else 
   print("<option value='/'> / (barra)");         
if ($arrpar[0]=='5')                
   print("<option value='t' selected> TAB (tabulazione)");
else 
   print("<option value='t'> TAB (tabulazione)");         
print("</select>");                  

print ("<tr>
                <td>Scegli il delimitatore di testo:&nbsp;</td>
                <td>");
print ("<select name='deli'>");
if ($arrpar[1]=='0')                
   print("<option value='' selected> &nbsp; (nessuno)");
else 
   print("<option value=''> &nbsp; (nessuno)");   
if ($arrpar[1]=='1')                
   print("<option value='v' selected> \" (virgolette)");
else 
   print("<option value='v'> \" (virgolette)");   
if ($arrpar[1]=='2')                
   print("<option value='a' selected> ' (apice)");
else 
   print("<option value='a'> ' (apice)");   
print("</select>");                  

print("</td></tr>");

print ("<tr>
      <td width='50%'><p align='center'><b>Cattedra</b></p></td>
      <td width='50%'>
      <SELECT ID='idcattedra' NAME='idcattedra' ONCHANGE='comp.submit()'> <option value=''>&nbsp ");


$con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));

$query="select idcattedra,tbl_classi.idclasse, anno, sezione, specializzazione, denominazione,tbl_materie.idmateria from tbl_cattnosupp, tbl_classi, tbl_materie where iddocente=$iddocente and tbl_cattnosupp.idclasse=tbl_classi.idclasse and tbl_cattnosupp.idmateria = tbl_materie.idmateria and idalunno=0 order by anno, sezione, specializzazione, denominazione";

$ris=mysqli_query($con,inspref($query));
while($nom=mysqli_fetch_array($ris))
{
    print "<option value='";
    print ($nom["idcattedra"]);
    print "'";

    print ">";

    print ($nom["anno"]);

    print "&nbsp;";
    print($nom["sezione"]);
    print "&nbsp;";
    print($nom["specializzazione"]);
    print "&nbsp;-&nbsp;";
    print($nom["denominazione"]);

}

print("
      </SELECT>");



print("    <tr>
                <td><br>Sovrascrivere eventuale programmazione presente?</td>
                <td><br>");


print("<input type='checkbox' name='sovrascrittura' checked>");
print("                   <br>
                </td>
            </tr>
      </td></tr>
            <tr>
                <td colspan='2' align='center' ><br/><input type='submit' name='upload' value='CARICA'></td>
            </tr>
        </table>

    </form>
    ");


stampa_piede("");

