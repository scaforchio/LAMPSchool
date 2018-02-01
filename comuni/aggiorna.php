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

// istruzioni per tornare alla pagina di login
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "") {
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

// Funzione che controlla la presenza di numeri nella stringa
function controlla_stringa($stringa)
{
    $l = strlen($stringa);
    for ($i = 0; $i <= $l - 1; $i++) {
        $car = substr($stringa, $i, 1);
        if (is_numeric($car)) {
            return 1;
            break;
        }
    }
}


$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);
$db = true;
print"<table border=0 width='100%'>
		<tr>
		   <td align ='center' ><strong><font size='+1'>CONFERMA MODIFICA COMUNE</font></strong></td>
		</tr>
		</table cellspacing='15'> <br/><br/>";
print"<center>";
$a = 0;
$de = $denominazioni;
if ($de == "") {
    $mess = $mess . "Inserire obbligatoriamente la denominazione <br/>";
    $a = 1;
} else {
    if (controlla_stringa($de) == 1) {
        $a = 1;
        $mess = $mess . "Inserire solo lettere per la denominazione<br/>";
    }
}
$cp = $cap;
if ($cp == "") {
    $mess = $mess . "Inserire obbligatoriamente il CAP della citt� <br/> ";
    $a = 1;
} else {
    if (!(is_numeric(trim($cp)) === true)) {
        $a = 1;
        $mess = $mess . "Inserire solo numeri per il CAP<br/>";
    }
}
$cod = $codice;
if ($cod == "") {
    $mess = $mess . "Inserire obbligatoriamente il codice istat della citt� <br/> ";
    $a = 1;
} else {
    if (!(is_numeric(trim($cod)) === true)) {
        $a = 1;
        $mess = $mess . "Inserire solo numeri per il codice istat<br/>";
    }
}
$provi = $prov;
if ($provi == "") {
    $mess = $mess . "Inserire obbligatoriamente la provincia <br/>";
    $a = 1;
} else {
    if (controlla_stringa($provi) == 1) {
        $a = 1;
        $mess = $mess . "Inserire solo lettere per la provincia<br/>";
    }
}
$sp = $sigla;
if ($sp == "") {
    $mess = $mess . "Inserire obbligatoriamente la sigla della provincia <br/>";
    $a = 1;
} else {
    if (controlla_stringa($sp) == 1) {
        $a = 1;
        $mess = $mess . "Inserire solo lettere per la sigla della provincia<br/>";
    }
}
$rg = $reg;
if ($rg == "") {
    $mess = $mess . "Inserire obbligatoriamente la regione <br/>";
    $a = 1;
} else {
    if (controlla_stringa($rg) == 1) {
        $a = 1;
        $mess = $mess . "Inserire solo lettere per la regione<br/>";
    }
}
$se = $stato;
if (controlla_stringa($se) == 1) {
    $a = 1;
    $mess = $mess . "Inserire solo lettere per lo stato estero<br/>";
}
if (!($a == 1)) {
    $sql = "UPDATE tbl_comuni SET denominazione='" . stringa_html('denominazioni') . "',";
    $sql .= "cap='" . stringa_html('cap') . "',";
    $sql .= "codistat='" . stringa_html('codice') . "',";
    $sql .= "provincia='" . stringa_html('prov') . "',";
    $sql .= "siglaprovincia='" . stringa_html('sigla') . "',";
    $sql .= "regione='" . stringa_html('reg') . "',";
    $sql .= "statoestero='" . stringa_html('stato') . "',";
    $sql .= " WHERE idcomune=" . stringa_html('idtbl_comuni');

    $res = mysqli_query($con, inspref($sql));
    if (!($res = mysqli_query($con, inspref($sql))))
        print("Query fallita");
    if (!($result = mysqli_query($con, inspref($sql))))
        print"AGGIORNAMENTO FALLITO";
    else {
        print"AGGIORNAMENTO RIUSCITO";
        print("<form name='form7' action='lis_com.php' method='POST'>");
        print("<input type='submit' value='<< Indietro'>");
        print("</form>");
    }
} else {
    print"$mess";
    print("<form name='form11' action='mod_com.php' method='POST'>");
    print("<input type ='hidden' name='idcom' value='$idtbl_comuni'>");
    print("<input type ='hidden' name='denominazioni' value='$denominazioni'>");
    print("<input type ='hidden' name='cap' value='$cap'>");
    print("<input type ='hidden' name='codistat' value='$codistat'>");
    print("<input type ='hidden' name='provincia' value='$provincia'>");
    print("<input type ='hidden' name='siglaprovincia' value='$siglaprovincia'>");
    print("<input type ='hidden' name='regione' value='$regione'>");
    print("<input type ='hidden' name='statoestero' value='$statoestero'>");
    print("<input type ='submit' value='Indietro'>");
    print("</form>");
}
print"</center>";
mysqli_close($con);

