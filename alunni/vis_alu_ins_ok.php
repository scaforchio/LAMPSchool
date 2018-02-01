<?php

session_start();

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

@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

// istruzioni per tornare alla pagina di login

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

/* Programma per la gestione del controllo dell'input di un alunno * */
$titolo = "Conferma inserimento alunno";
$script = "";
stampa_head($titolo, "", $script, "MASP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='vis_alu_cla.php'>Elenco classi</a> - $titolo", "", "$nome_scuola", "$comune_scuola");

$cognome = stringa_html('cognome');
$nome = stringa_html('nome');
$codfiscale = stringa_html('codfiscale');
$certificato = stringa_html('certificato');
$aa = stringa_html('aa');
$mm = stringa_html('mm');
$gg = stringa_html('gg');
$datc = stringa_html('datc');
$idcomn = stringa_html('idcomn');
$idcomr = stringa_html('idcomr');
$sidi = stringa_html('sidi');
$tel = stringa_html('tel');
$cel = stringa_html('cel');
$mail = stringa_html('mail');
$note = stringa_html('note');
$autentrata = stringa_html('autentrata');
$autuscita = stringa_html('autuscita');
$firmapropria = stringa_html('firmapropria');
$indirizzo = stringa_html('indirizzo');
$idtut = stringa_html('idtut');
$numeroregistro = stringa_html('numeroregistro');
$provenienza = stringa_html('provenienza');
$titoloammissione = stringa_html('titoloammissione');
$sequenzaiscrizione = stringa_html('sequenzaiscrizione');
$datacambio = stringa_html('datacambioclasse');
$datacambio = $datacambio != "" ? data_to_db($datacambio) : "";

//connessione al server
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);
if (!$con)
{
    print("<h1> Connessione al server fallita </h1>");
}

//connessione al database
$DB = true;
if (!$DB)
{
    print("<h1> Connessione al database fallita </h1>");
}
else
{
    print "<center>";
    //print "<html>";
    //print " <head> ";
    //print (" <title> Controllo dati dell'inserimento</title> </head>");
    $query = "insert into tbl_alunni (cognome,nome,datanascita,codfiscale,certificato,idcomnasc,indirizzo,idcomres,codmeccanografico,telefono,telcel,email,autentrata,autuscita,idclasse,note,numeroregistro,provenienza,titoloammissione,sequenzaiscrizione,firmapropria)values('$cognome','$nome','$aa-$mm-$gg','$codfiscale','$certificato','$idcomn','$indirizzo','$idcomr','$sidi','$tel','$cel','$mail','$autentrata','$autuscita','$datc','$note','$numeroregistro','$provenienza','$titoloammissione',$sequenzaiscrizione,$firmapropria)";
    $err = 0;
    $mes = "";
    if (!$cognome)
    {
        $err = 1;
        $mes = "Il cognome non &egrave; stato inserito<br/> ";
    }
    else
    {
        $errore = controlla_stringa($cognome);
        if ($errore == 1)
        {
            $err = 1;
            $mes = "Il cognome pu&ograve; contenere solo caratteri<br/>";
        }
    }
    if (!$nome)
    {
        $err = 1;
        $mes = $mes . "Il nome non &egrave; stato inserito<br/> ";
    }
    else
    {
        $errore = controlla_stringa($nome);
        if ($errore == 1)
        {
            $err = 1;
            $mes = $mes . "Il nome puo contenere solo caratteri<br/>";
        }
    }
    if (!$aa)
    {
        $err = 1;
        $mes = $mes . " L'anno di nascita non &egrave; stato inserito<br/> ";
    }
    else
    {
        if (is_numeric($aa) === false)
        {
            $err = 1;
            $mes = $mes . "L'anno di nascita pu&ograve; contenere solo valori numerici <br/>";
        }
    }

    if (!$gg)
    {
        $err = 1;
        $mes = $mes . " Il giorno di nascita non &egrave; stato inserito<br/> ";
    }
    else
    {
        if (is_numeric($gg) === false)
        {
            $err = 1;
            $mes = $mes . " Il giorno di nascita pu&ograve; contenere solo valori numerici <br/>";
        }
    }
    if (!$mm)
    {
        $err = 1;
        $mes = $mes . "Il mese di nascita non &egrave; stato inserito<br/> ";
    }
    else
    {
        switch ($mm)
        {
            case 1:
            case 3:
            case 5:
            case 7:
            case 8:
                {
                    if ($gg > 31)
                    {
                        $err = 1;
                        $mes = $mes . "Il giorno di nascita non &egrave; corretto <br/>";
                    }
                    break;
                }
            case 10:
                {
                    if ($gg > 31)
                    {
                        $err = 1;
                        $mes = $mes . "Il giorno di nascita non &egrave; corretto <br/>";
                    }
                    break;
                }
            case 12:
                {
                    if ($gg > 31)
                    {
                        $err = 1;
                        $mes = $mes . "Il giorno di nascita non &egrave; corretto <br/>";
                    }
                    break;
                }
            case 4:
            case 6:
            case 9:
            case 11:
                {
                    if ($gg > 30)
                    {
                        $err = 1;
                        $mes = $mes . "Il giorno di nascita non &egrave; corretto <br/>";
                    }
                    break;
                }
            case 2:
                {
                    if ($gg > 29)
                    {
                        $err = 1;
                        $mes = $mes . "Il giorno di nascita non &egrave; corretto <br/>";
                    }
                    break;
                }
            default:
                $mes = $mes . "Il mese di nascita non &egrave; corretto<br/>";
        }
    }
    if (!$idcomn)
    {
        $err = 1;
        $mes = $mes . " Il comune di nascita non &egrave; stato selezionato<br/> ";
    }

    if ($err == 0)
    { // print inspref($query);
        $res = mysqli_query($con, inspref($query)) or die(mysqli_error($con) . " - " . inspref($query, false));
        if (!$res)
        {
            print("Il nuovo alunno non &egrave; stato inserito<br/>");
        }
        else
        {
            $idalunnoinserito = mysqli_insert_id($con);

            // INSERISCO ANCHE IL RECORD NELLA TABELLA DEI tbl_tutori;
            //   $sqlt = "insert into tbl_tutori(idtutore,cognome,nome,idalunno,idutente) values ('$idalunnoinserito','$cognome','$nome','$idalunnoinserito','$idalunnoinserito')";
            //   $res = mysqli_query($con, inspref($sqlt));
            // INSERISCO ANCHE IL RECORD NELLA TABELLA DEI tbl_tutori;

            if ($datacambio != "")
            {
                $datafine = aggiungi_giorni($datacambio, -1);
                // print "ttt $datafine";
                $querycambioclasse = "insert into tbl_cambiamenticlasse(idalunno,idclasse,datafine) values ($idalunnoinserito,0,'$datafine')";
                mysqli_query($con, inspref($querycambioclasse)) or die("Errore:" . inspref($querycambioclasse, false));
            }

            $utente = "gen" . $idalunnoinserito;
            $password = creapassword();
            $sqlt = "insert into tbl_utenti(idutente,userid,password,tipo) values ('$idalunnoinserito','$utente',md5('" . md5($password) . "'),'T')";
            $res = mysqli_query($con, inspref($sqlt));
            // AGGIORNO IL RECORD DELL'ALUNNO CON l'ID DEL TUTORE
            $sqlt = "update tbl_alunni set idtutore=$idalunnoinserito,idutente=$idalunnoinserito where idalunno=$idalunnoinserito";

            $res = mysqli_query($con, inspref($sqlt));

            // print "risultato inserimento $idalunnoinserito<br/>";
            print("Il nuovo alunno &egrave; stato inserito<br/><br/>Utente: $utente<br/><br/>Password:$password<br/>");
        }
        print(" <form action='vis_alu.php' method='POST'>");
        print ("<input type='hidden'  name='idcla' value='$datc'>");
        print ("<input type='submit' value=' << Indietro '></form> ");
        print "<br><br><center>";
        print "<form target='_blank' name='stampa' action='stampa_pass_alu.php' method='POST'>
                   <input type='hidden' name='arrid' value='$idalunnoinserito'> 
                   <input type='hidden' name='arrut' value='$utente'> 
                   <input type='hidden' name='arrpw' value='$password'> 
                   <input type='hidden' name='numpass' value='1'> 
                   
                   <input type='submit' value='STAMPA COMUNICAZIONE PASSWORD'>
                   </form>";
        print "</center>";
    }
    else
    {
        print (" <form action='vis_alu_ins.php' method='POST'>");
        print ("<input type='hidden'  name='cognome' value='$cognome'>");
        print ("<input type='hidden' name='nome'  value='$nome'> ");
        print ("<input type='hidden' name='codfiscale'  value='$codfiscale'> ");
        print ("<input type='hidden' name='certificato'  value='$certificato'> ");
        print (" <input type='hidden'  name='gg' value='$gg'><input type='hidden'  name='mm'  value='$mm'><input type='hidden' name='aa'  value='$aa' > ");
        print ("<input type='hidden' name='idcomn'  value='$idcomn'> ");
        print ("<input type='hidden' name='indirizzo'  value='$indirizzo'> ");
        print ("<input type='hidden' name='idcomr'  value='$idcomr'> ");
        print ("<input type='hidden' name='sidi'  value='$sidi'> ");
        print ("<input type='hidden' name='tel' value='$tel'>");
        print ("<input type='hidden' name='cel'  value='$cel'> ");
        print ("<input type='hidden' name='mail' value='$mail'> ");
        print ("<input type='hidden' name='note' value='$note'> ");
        print ("<input type='hidden' name='autentrata' value='$autentrata'> ");
        print ("<input type='hidden' name='autuscita' value='$autuscita'> ");
        print ("<input type='hidden' name='datc'  value='$datc'> ");
        print ("<input type='hidden' name='idtut'  value='$idtut'> ");
        print ("<input type='hidden' name='numeroregistro'  value='$numeroregistro'> ");
        print ("<input type='hidden' name='provenienza'  value='$provenienza'> ");
        print ("<input type='hidden' name='titoloammissione'  value='$titoloammissione'> ");
        print ("<input type='hidden' name='sequenzaiscrizione'  value='$sequenzaiscrizione'> ");
        print ("<input type='hidden' name='firmapropria'  value='$firmapropria'> ");
        print ("<h3> Correzioni:</h3>");
        print $mes;
        print ("<br/><input type='submit' value=' << Indietro '> ");
        print ("</form>");
    }
    print "</center>";
    print "<script>document.title='Controllo dati dell\'inserimento';</script>";

    stampa_piede("");

    mysqli_close($con);
}

