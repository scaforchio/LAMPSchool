
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

if ($tipoutente == "")
{

    header("location: ../login/login.php?suffisso=".$_SESSION['suffisso']);
}
stampa_head("Carica Archivio Docenti da CSV","","");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - Carica Archivio Docenti da CSV","","$nome_scuola","$comune_scuola");
$arrpar=array('1','0','1','1','2','3','4','5','6','7','8','9');
$stringaparametri = implode("!",$arrpar);

if (is_stringa_html('par'))
{
	$stringaparametri = stringa_html('par');
	
	$arrpar=explode("!", $stringaparametri);
}

print("
<center>

    <form enctype='multipart/form-data' method='post' action='carica_docenti_da_csv_ins.php'>
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

print("    <tr>
                <td><br>Presente intestazione? (prima riga con nome campi)</td>
                <td><br>");

              
if ($arrpar[2]=='1')                
   print("<input type='checkbox' name='intestazione' checked>");
else   
   print("<input type='checkbox' name='intestazione'>");
print("                   <br>
                </td>
            </tr>
            <tr><td>&nbsp;</td></tr>");
/*
if ($arrpar[1]=='0')
   print "<tr><td>Delimitatore</td><td><input type=text name= deli size=1 value='\"'</td><td>&nbsp;</td></tr>";
*/            
print("            <tr>
			<table border=1>
				<tr class=prima>
					<td>Dato </td><td>Posizione</td><td>Commenti</td></tr>");
					
					
print("<tr><td><b>Cognome</b></td><td><select name='poscogn'>");
for ($i=1;$i<=30;$i++)
if ($i==$arrpar[3])
   print "<option selected>$i</option>";
else
   print "<option>$i</option>";
print ("</select></td><td>&nbsp;</td></tr>");

print("<tr><td><b>Nome</b></td><td><select name='posnome'>");
for ($i=1;$i<=30;$i++)
if ($i==$arrpar[4])
   print "<option selected>$i</option>";
else
   print "<option>$i</option>";
print ("</select></td><td>&nbsp;</td></tr>");

print("<tr><td>Data nascita</td><td><select name='posdata'>");
for ($i=1;$i<=30;$i++)
if ($i==$arrpar[5])
   print "<option selected>$i</option>";
else
   print "<option>$i</option>";
if ($arrpar[5]==99)   
   print "<option selected value=99>NP</option>";
else
   print "<option value=99>NP</option>";
print ("</select></td><td>Formato gg/mm/aaaa</td></tr>");



print("<tr><td>Com. nasc.</td><td><select name='poscomnasc'>");
for ($i=1;$i<=30;$i++)
if ($i==$arrpar[6])
   print "<option selected>$i</option>";
else
   print "<option>$i</option>";
if ($arrpar[6]==99)   
   print "<option selected value=99>NP</option>";
else
   print "<option value=99>NP</option>";
print ("</select></td><td>Codice min. finanze (Es. A123)</td></tr>");

print("<tr><td>Indirizzo</td><td><select name='posindi'>");
for ($i=1;$i<=30;$i++)
if ($i==$arrpar[7])
   print "<option selected>$i</option>";
else
   print "<option>$i</option>";
if ($arrpar[7]==99)   
   print "<option selected value=99>NP</option>";
else
   print "<option value=99>NP</option>";
print ("</select></td><td>&nbsp;</td></tr>");


print("<tr><td>Com. res.</td><td><select name='poscomres'>");
for ($i=1;$i<=30;$i++)
if ($i==$arrpar[8])
   print "<option selected>$i</option>";
else
   print "<option>$i</option>";
if ($arrpar[8]==99)   
   print "<option selected value=99>NP</option>";
else
   print "<option value=99>NP</option>";
print ("</select></td><td>Codice min. finanze (Es. A123)</td></tr>");

print("<tr><td>Telef.</td><td><select name='postele'>");
for ($i=1;$i<=30;$i++)
if ($i==$arrpar[9])
   print "<option selected>$i</option>";
else
   print "<option>$i</option>";
if ($arrpar[9]==99)   
   print "<option selected value=99>NP</option>";
else
   print "<option value=99>NP</option>";
print ("</select></td><td>&nbsp;</td></tr>");

print("<tr><td>Cellul.</td><td><select name='poscell'>");
for ($i=1;$i<=30;$i++)
if ($i==$arrpar[10])
   print "<option selected>$i</option>";
else
   print "<option>$i</option>";
if ($arrpar[10]==99)   
   print "<option selected value=99>NP</option>";
else
   print "<option value=99>NP</option>";
print ("</select></td><td>&nbsp;</td></tr>");

print("<tr><td>Email</td><td><select name='posemail'>");
for ($i=1;$i<=30;$i++)
if ($i==$arrpar[11])
   print "<option selected>$i</option>";
else
   print "<option>$i</option>";
if ($arrpar[11]==99)   
   print "<option selected value=99>NP</option>";
else
   print "<option value=99>NP</option>";
print ("</select></td><td>&nbsp;</td></tr>");

print("								</table>
            </tr>
            <tr>
                <td colspan='2' align='center' ><br/><input type='submit' name='upload' value='CARICA'></td>
            </tr>
        </table>

    </form>
    <br>I campi in <b>grassetto</b> sono obbligatori.<br>Selezionare NP per tutti i dati non presenti.</center>");

stampa_piede("");

