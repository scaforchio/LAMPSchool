<?php session_start();
/*
Copyright (C) 2013 Pietro Tamburrano
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


/*Programma per il cambiamento password.*/

@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

// istruzioni per tornare alla pagina di login se non c'è una sessione valida
////session_start();
$tipoutente = $_SESSION["tipoutente"];
$userid = $_SESSION["userid"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}
if ($tipoutente == 'T')
{
    $lunghezzapassword = 8;
}   // Maggiore è il livello minore sarà la sicurezza della password
else
{
    $lunghezzapassword = 12;
}
$titolo = "Cambiamento propria password";
$script = "<script src='../lib/js/crypto.js'></script>
	           <!-- <script type=\"text/javascript\" src=\"jquery.min.js\">
               </script> -->
               <script type=\"text/javascript\">
                   $(document).ready(function(){
                        //qui scriveremo il codice jQuery
                        var width = $('#result').width();
                        var actRate = 0;
                        $('#bar').css({'height':'100%','width':0,'background-color':'red'});
                        var lowerCase = /[a-z]+/;  //minuscole
						var upperCase =  /[A-Z]+/; //maiuscole
						var numbers = /[0-9]+/; //numeri
								var specialChars = /[\040\041\042\043\044\045\046\047\050\051\052\053\054\055\056\057\072\073\074\075\076\077\100\133\135\137\173\174\175]+/;  //caratteri speciali
								// var specialCharsBonus = /[\040\041\042\043\044\045\046\047\050\051\052\053\054\055\056\057\072\073\074\075\076\077\100\133\135\137\173\174\175]{4,}/; // almeno quattro caratteri speciali
								$('#npas').keyup(function(){
									 var pwd = $('#npas').val();
									 var rate = 0;
									 if(pwd.length >= 1)
									 {
										 rate+=68*(pwd.length/" . $lunghezzapassword . ");
										 if (rate>68)
										    rate=68;
										 if(lowerCase.test(pwd))
										 {
											 rate *= 1.1;
										 }
										 if(upperCase.test(pwd))
										 {
											 rate *= 1.1;
										 }
										 if(numbers.test(pwd))
										 {
											 rate *= 1.1;
										 }
										 if(specialChars.test(pwd))
										 {
											 rate *= 1.1;
										 }
										// if(specialCharsBonus.test(pwd))
										// {
										//	 rate += 30;
										// }
										// if(pwd.length > 12)
										// {
										//	 rate += 20;
										// }

									 }
							 
										 var barWidth = rate * (width / 100);
										 $('#bar').animate({
											 width: barWidth
										 },50, function(){
							 
											 if(rate < 40)
											 {
												 $('#bar').css('background-color', 'red');
												// //$('input[type=\"submit\"]').attr('disabled','disabled');
											//	 $('#rnpas').attr('disabled','disabled');
											 }
											 if(rate >= 40 && rate < 60)
											 {
												 $('#bar').css('background-color', '#FF7F2A');
												// // $('input[type=\"submit\"]').attr('disabled','disabled');
											//	 $('#rnpas').attr('disabled','disabled');
											 }
											 if(rate >= 60 && rate < 80)
											 {
											   // // $('input[type=\"submit\"]').removeAttr('disabled');
												 $('#bar').css('background-color', '#AAFF55');
												// $('#rnpas').removeAttr('disabled');
											 }
											 if(rate >= 80)
											 {
											   // // $('input[type=\"submit\"]').removeAttr('disabled');
												 $('#bar').css('background-color', 'green');
												// $('#rnpas').removeAttr('disabled');
											 }
											 if ($('#rnpas').val() == $('#npas').val() && pwd.length>5)
											    $('input[type=\"submit\"]').removeAttr('disabled');
											 else
											    $('input[type=\"submit\"]').attr('disabled','disabled');
										 });
							 
								 });
								 $('#rnpas').keyup(function(){
								     var pwd = $('#npas').val();
								 if ($('#rnpas').val() == $('#npas').val() && pwd.length>5)
											    $('input[type=\"submit\"]').removeAttr('disabled');
											 else
											    $('input[type=\"submit\"]').attr('disabled','disabled');   
											 });
							 });
								
                 
                </script>";
stampa_head($titolo, "", $script,"TDSAPML");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");


//Connessione al server SQL
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);
if (!$con)
{
    print("\n<h1> Connessione al server fallita </h1>");
    exit;
};


print("<center>");
// print "<form name='form1' action='ch_pwd.php' method='POST'>";

print "<table border='0'>";
print "<tr> <td> Utente </td> <td> <input type='text' name='ut' id='ut' value='$userid' disabled> <input type='hidden' name='utente' value='$userid'></td> </tr>";
print "<tr> <td> Vecchia password </td> <td> <input type='password' name='passwor' id='passwor'> </td> </tr>";
print "<tr> <td> Nuova password </td> <td> <input type='password' name='npas' id='npas'>
	             <div id='result'><div id='bar'></div></div>
                
                 </td> </tr>";
print "<tr> <td> Ripeti nuova password&nbsp;</td> <td> <input type='password' name='rnpas' id='rnpas'> </td> </tr>";
print "</table>";

print "<form name='form1' action='ch_pwd.php' method='POST'>";
print "<input type='hidden' name='ute' id='ute' value='$userid'>";
print "<input type='hidden' name='password' id='password'>";
print "<input type='hidden' name='npass' id='npass'>";
print "<input type='hidden' name='rnpass' id='rnpass'>";
//print '<center><br/><input type="submit" name="OK" id="OK" value="OK" disabled onclick="document.getElementById(\'password\').value=hex_md5(document.getElementById(\'passwor\').value);document.getElementById(\'ute\').value=document.getElementById(\'ut\').value;document.getElementById(\'npass\').value=hex_md5(document.getElementById(\'npas\').value);document.getElementById(\'rnpass\').value=hex_md5(document.getElementById(\'rnpas\').value)" >';
print '<center><br/><input type="submit" name="OK" id="OK" value="OK" disabled onclick="document.getElementById(\'password\').value=hex_md5(document.getElementById(\'passwor\').value);document.getElementById(\'ute\').value=document.getElementById(\'ut\').value;document.getElementById(\'npass\').value=document.getElementById(\'npas\').value;document.getElementById(\'rnpass\').value=document.getElementById(\'rnpas\').value" >';
print "</form>";
print "<br/>";

//tasto indietro

mysqli_close($con);
stampa_piede("");

?>
