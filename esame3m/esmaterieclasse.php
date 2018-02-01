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

//Visualizzazione classi
@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");
// istruzioni per tornare alla pagina di login
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$idclasse = stringa_html('idclasse');


$titolo = "Elenco materie scritti esame per classe";
$script = "";
stampa_head($titolo, "", $script, "E");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - Elenco materie scritti esame per classe", "", "$nome_scuola", "$comune_scuola");

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);
if (!$con)
{
    print("<h1> Connessione server fallita </h1>");
}
$db = true;
if (!$db)
{
    print("<h1> Connessione db fallita </h1>");
}
if ($livello_scuola == '2')
{
    $sql = "SELECT * FROM tbl_classi WHERE anno = '3' ORDER BY specializzazione, sezione, anno ";
}
else
{
    if ($livello_scuola == '3')
    {
        $sql = "SELECT * FROM tbl_classi WHERE anno = '8' ORDER BY specializzazione, sezione, anno ";
    }
}
if (!($res = mysqli_query($con, inspref($sql))))
{
    print ("Query fallita");
}
else
{


    print "<form method='POST' action='esmaterieclasse.php' name='materieesame'>";
    print "<center>";
    print " <select name='idclasse' ONCHANGE='materieesame.submit()'><option value=''>&nbsp;</option>";
    while ($dati = mysqli_fetch_array($res))
    {
        if ($idclasse==$dati['idclasse'])
            $sel='selected';
        else
            $sel='';
        //print("<tr> <td> <font size='3'> <a href='vis_alu.php?idcla=".$dati['idclasse']."'> ".$dati['anno']." ".$dati['sezione']." ".$dati['specializzazione']." </a> </font> </td> </tr>");
        print("<option value='" . $dati['idclasse'] . "' $sel> " . $dati['anno'] . " " . $dati['sezione'] . " " . $dati['specializzazione'] . "  </option>");
    }

    print "</select>";
}
print "</form>";


$registrazione='I';
if ($idclasse != '')
{
    if (stringa_html('reg')=='1')
        print "<br><br><font color='green'>Registrazione effettuata!</font><br>";

    print "<form action='insesmaterieclasse.php' method='POST'>";
    print "<input type='hidden' name='idclasse' value='$idclasse'>";
    print "<br><br><table border=1><tr class='prima'><td>Sigla</td><td>Denominazione</td><td>Media</td><td>2^ Lingua</td><td>P.N.I.</td></tr>";
    // Leggo i dati della classe se già inseriti
    $query = "SELECT * FROM tbl_esmaterie where idclasse='$idclasse'";
    $ris = mysqli_query($con, inspref($query));

    if (mysqli_num_rows($ris) != 0)
    {
        $registrazione='U';

        $rec=mysqli_fetch_array($ris);
        $secondalingua=$rec['num2lin'];
        $invalsi=$rec['numpni'];
        for ($i=1;$i<=9;$i++)
        {

            $n1= 'm'.$i.'s';
            $n2= 'm'.$i.'e';
            $n3= 'm'.$i.'m';
            $sigla=$rec[$n1];
            $denom=$rec[$n2];
            $media=$rec[$n3];
            $sel2l="";
            $selinv="";
            if ($secondalingua==$i) $sel2l=' checked';
            if ($invalsi==$i) $selinv=' checked';
            print "<tr><td><input type='text' name='$n1' value='$sigla' maxlength=5 size=5></td>
                       <td><input type='text' name='$n2' value='$denom' maxlength=30 size=30></td>";
            if ($media)
                print "<td><select name='$n3'><option value='0'>N</option><option value='1' selected>S</option></select></td>";
            else
                print "<td><select name='$n3'><option value='0' selected>N</option><option value='1'>S</option></select></td>";
            print "<td><input type='radio' name='secondalingua' value='$i' $sel2l></td>";
            print "<td><input type='radio' name='invalsi' value='$i' $selinv></td>";
            print "</tr>";
        }

    }
    else
    {
        $secondalingua=5;
        $invalsi=4;
        for ($i=1;$i<=9;$i++)
        {
            $n1= 'm'.$i.'s';
            $n2= 'm'.$i.'e';
            $n3= 'm'.$i.'m';
            $sigla=$rec[$n1];
            $denom=$rec[$n2];
            $media=$rec[$n3];
            $sel2l="";
            $selinv="";
            switch($i)
            {
                case 1: $sigla='ITA';$denom='Italiano';$media='1';break;
                case 2: $sigla='MAT';$denom='Matematica';$media='1';break;
                case 3: $sigla='ING';$denom='Inglese';$media='1';break;
                case 4: $sigla='P.I.';$denom='Invalsi';$media='1';break;
                case 5: $sigla='FRA';$denom='Francese';$media=true;break;


            }
            if ($secondalingua==$i) $sel2l=' checked';
            if ($invalsi==$i) $selinv=' checked';
            print "<tr><td><input type='text' name='$n1' value='$sigla' maxlength=5 size=5></td>
                       <td><input type='text' name='$n2' value='$denom' maxlength=30 size=30></td>";
            if ($media)
                print "<td><select name='$n3'><option value='0'>N</option><option value='1' selected>S</option></select><input type='hidden' name='$n3' value='1'></td>";
            else
                print "<td><select name='$n3'><option value='0' selected>N</option><option value='1'>S</option></select><input type='hidden' name='$n3' value='0'></td>";
            print "<td><input type='radio' name='secondalingua' value='$i' $sel2l></td>";
            print "<td><input type='radio' name='invalsi' value='$i' $selinv></td>";
            print "</tr>";
        }

    }
    print "</table>";
    if ($registrazione=='I')
        print "<br><br><input type='submit' value='Registra materie'>";
    else
    {
        // Verifico se c'è già un esame per un alunno della classe
        $query="select * from tbl_esesiti,tbl_alunni
                where tbl_esesiti.idalunno=tbl_alunni.idalunno
                and idclasse=$idclasse";
        $ris=mysqli_query($con,inspref($query)) or die ("Errore: ". inspref($query,false));
        if (mysqli_num_rows($ris)>0)
            print "<br><b><font color='red'>ATTENZIONE! Non modificare l'ordine delle materie: ci sono valutazioni già registrate.</font></b>";
        print "<br><br><input type='submit' value='Aggiorna materie'>";
    }
    print "</form>";
}


mysqli_close($con);
stampa_piede("");


