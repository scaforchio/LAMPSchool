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
@include '../php-ini'.$_SESSION['suffisso'].'.php';
@include '../lib/funzioni.php';

	// istruzioni per tornare alla pagina di login 
	   
	 $titolo="Credits";
     $script=""; 
     stampa_head($titolo,"",$script,"",false);
  //   stampa_testata("$titolo","","$nome_scuola","$comune_scuola");
 

?>

<html><head></head>

<body>
<p align="center" >
           <b> LAMPSchool<br/>REGISTRO ON LINE<br/></b>
            
        </p>
<center><img src='../immagini/logo.gif'><br/><br/>

<b>Hanno contribuito (in ordine di intervento):</b><br/><br/>
<table border=1>
	<tr><td><a href='mailto:pietro.tamburrano@gmail.com'>Pietro Tamburrano</a></td><td><i>(Analisi, Sviluppo, Debugging, Documentazione)</i></td><td>ISIS Luigi Di Maggio - San Giovanni Rotondo</td></tr>
    <tr><td><a href='mailto:gpgorgoglione@alice.it'>Giovanni Gorgoglione</a></td><td><i>(Analisi, Debugging)</i></td><td>ISIS Luigi Di Maggio - San Giovanni Rotondo</td></tr>
    <tr><td><a href='mailto:massimo.cunico@ic16verona.gov.it'>Massimo Cunico</a></td><td><i>(Analisi, Debugging, Documentazione)</i></td><td>IC 16 Verona</td></tr>
    <tr><td><a href='mailto:ubaldo@pernigo.com'>Ubaldo Pernigo</a></td><td><i>(Analisi, Debugging)</i></td><td>IC 04 Verona</td></tr>
    <tr><td><a href='mailto:renato.tamilio@gmail.com'>Renato Tamilio</a></td><td><i>(Analisi, Sviluppo, Debugging, Documentazione)</i></td><td>Consigliere c/o IC di Trofarello (TO)</td></tr>
    <tr><td><a href='mailto:ngpconte@alice.it'>Pasquale Fabio Conte</a></td><td><i>(Debugging)</i></td><td>&nbsp;</td></tr>
    <tr><td><a href='mailto:angelo.scarna1@codelinsoft.it'>Angelo Scarnà</a></td><td><i>(Sviluppo)</i></td><td>&nbsp;</td></tr>
    <tr><td><a href='mailto:andrea.manara@istruzione.it'>Andrea Manara</a></td><td><i>(Analisi, Debugging, Documentazione)</i></td><td>Istituto Comprensivo Verona 11</td></tr>
    <tr><td><a href='mailto:robert982@live.it'>Robert De Carli</a></td><td><i>(Analisi, Debugging)</i></td><td>&nbsp;</td></tr>
    <tr><td><a href='mailto:p.raguso@gmail.com'>Pierpaolo Raguso</a></td><td><i>(Analisi, Debugging)</i></td><td>&nbsp;</td></tr>
    <tr><td><a href='mailto:ollenotna2000@yahoo.it'>Antonello Facchetti</a></td><td><i>(Analisi, Sviluppo)</i></td><td>IC Rudiano (BS)</td></tr>
    <tr><td><a href='mailto:alberghiero@collegiorosmini.it'>Gabriele Taddei</a></td><td><i>(Analisi, Debugging)</i></td><td>Istituto Professionale Paritario<br>Servizi per l'Enogastronomia e l'Ospitalità Alberghiera<br>"Mellerio Rosmini" di Domodossola (VB)</td></tr>
    <tr><td><a href='mailto:bob@linux.it'>Roberto Guido</a></td><td><i>(Analisi, Sviluppo, Debugging)</i></td><td>Italian Linux Society</td></tr>
    <tr><td><a href='mailto:maxbonetti@gmail.com'>Massimo Bonetti</a></td><td><i>(Debugging, Documentazione)</i></td><td>Scuola Media R. Montecuccoli di Pavullo nel Frignano</td></tr>
    <tr><td><a href='mailto:simone.amati@istruzione.it'>Simone Amati</a></td><td><i>(Debugging, Analisi)</i></td><td>IIS “Carlo e Nello Rosselli” di Aprilia (LT)</td></tr>
    <tr><td><a href='mailto:marco.benaglia@gmail.com'>Marco Benaglia</a></td><td><i>(Debugging, Analisi, Sviluppo)</i></td><td>&nbsp;</td></tr>

</table>
<br/><br/>
<b>Patrocinato da:</b><br/><br/>
<table align='center'>
	<tr><td><a href='http://www.isdimaggio.it'><img src='../immagini/isisdimaggio.png'></a></td><td>ISIS Luigi Di Maggio - San Giovanni Rotondo</td></tr>
    <tr><td><a href='http://www.lampschool.it'><img src='../immagini/dematvr.png'></a></td><td>Rete delle scuole veronesi per la dematerializzazione</td></tr>
    
</table>

<br><br><small><a href='mailto:pietro.tamburrano@gmail.com'>Informazioni e segnalazioni</a></small><br/><br/>
<small><a href='http://sourceforge.net/p/lampschool/code/ci/master/tree/'>Codice</a></small><br/><br/>
</center></body>
</html>
<?

?>
