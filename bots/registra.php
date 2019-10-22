<?php

session_start();
$suffisso = $_GET['suffisso'];
@require_once("../php-ini" . $suffisso . ".php");
@require_once("../lib/funzioni.php");

inserisci_log("TELEGRAM§" . date('m-d|H:i:s') . "§" . IndirizzoIpReale() . "§Invio dati", $nomefilelog . "", $suffisso);


$token = $tokenbototp;
$email = "";

// Passaggio dei parametri nella sessione
//		require "../../lib/funzioni.php";
//		require '../../php-ini.php';


//  Funziona che invia un messaggio specificato in $testo alla chat con id
//  $chat_id.

//  Restituisce la risposta del server in formato JSON
 

//Fai la richiesta HTTP al bot Telegram per vedere se è tutto okay
if (!isBotOnline($token))
{
    exit();
}
//Interrogo il bot per vedere se ho dei messaggi arrivati:
$json = file_get_contents('php://input');
if ($json == "")
{
    echo "<br />Nessuna richiesta arrivata!<br />";
    exit();
}
// Converts it into a PHP object
$messaggio = json_decode($json);
$text = $messaggio->{"message"}->{"text"};

$chat_id = $messaggio->{"message"}->{'chat'}->{'id'};
//Ricavo le credenziali dal messaggio, che sarà tipo "nomeutente password"
$credenziali = explode(" ", $text);
if (count($credenziali) != 2)
{ //Se il formato del messaggio è sbagliato
    $testo = "Credenziali non scritte correttamente.";
    sendTelegramMessage($chat_id, $testo, $token);
} else
{
    
    $user = elimina_apici($credenziali[0]);
    $pass = elimina_apici($credenziali[1]);
    $pass = md5(md5($pass));
    //Controllo user e pass
    //Ovviamente questo andrà fatto dal database

    $con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore connessione");
  
    $sql = "SELECT * from tbl_utenti WHERE userid='".$user."' AND  password='".$pass."'";
    
    
    $result = eseguiQuery($con, $sql);
    if (mysqli_num_rows($result) > 0) // VERIFICO SE C'E' L'UTENTE
    {
        $utente = mysqli_fetch_array($result, MYSQLI_ASSOC);
        if ($utente['tipo'] == 'D' | $utente['tipo'] == 'S' | $utente['tipo'] == 'P')
        {
            if ($chat_id == $utente["idtelegram"])
            { //L'utente si è già registrato con quell'account telegram
                sendTelegramMessage($chat_id, "Registrazione già avvenuta!", $token);
            } else
            { //Se il chat id è diverso
                //conferma della registrazione tramite e-mail
                $sql = "select email FROM tbl_docenti WHERE iddocente = '" . $utente["idutente"] . "'"; //to do: aggiungere anche le altre tabelle
                $risultato = eseguiQuery($con, $sql);
                $tmp = mysqli_fetch_array($risultato);
                $email = $tmp[0];
                if ($email == "")
                {
                    $testo = "Registare la propria email su lampschool per poter usare questo servizio.";
                    sendTelegramMessage($chat_id, $testo, $token);
                } else
                {
                    //mi genero il token di conferma
                    $tokendiconferma = "";
                    do
                    {
                        $tokendiconferma = generaStringaRandom(20);
                        //controllo se è stato già generato un token uguale o no
                        $query = "SELECT * FROM tbl_confermatelegram WHERE tokendiconferma = '" . $tokendiconferma . "'";
                        $ris = eseguiQuery($con, $query);
                    } while (mysqli_num_rows($ris) > 0);

                    $link = $urlbottelegram."confermaregistrazione.php?tokendiconferma=" . $tokendiconferma . "&chatid=" . $chat_id."&suffisso=$suffisso";
                    $testo = "Apri il link per completare la registazione: " . $link;
                    $subjectMail = "Conferma registrazione OTP Telegram";
                    //if (mail($email, $subjectMail, $testo))
                    if (invia_mail($email, $subjectMail, $testo, $indirizzomailfrom))
                    {
                        //Sicurezza mail - sostituzione con asterischi
                        $i = 2;
                        $emailSicura = $email[0] . $email[1];
                        do
                        {
                            $emailSicura .= '*';
                            $i++;
                        } while ($email[$i] != '@');
                        $email = explode("@", $email);
                        $emailSicura .= "@" . $email[1];
                        $testo = "Ti abbiamo mandato una mail a " . $emailSicura . " per confermare la tua registrazione";
                        sendTelegramMessage($chat_id, $testo, $token);
                        $sql = "INSERT INTO tbl_confermatelegram (idutente, tokendiconferma) VALUES (" . $utente['idutente'] . ", '$tokendiconferma');";
                        eseguiQuery($con, $sql);
                    } else
                    {
                        $testo = "<b>Operazione fallita!</b>\nCi sono stati problemi con l'invio dell'email!";
                        sendTelegramMessage($chat_id, $testo, $token);
                    }
                }
            }
        } else
        {
            $testo = "A questo utente non è concesso l'uso dell'otp via Telegram.";
            sendTelegramMessage($chat_id, $testo, $token);
        }
    } else
    {
        sendTelegramMessage($chat_id, "Accesso fallito!", $token); //va registrato nei log
    }
}
$testo = "<b>ATTENZIONE!</b>\nPer motivi di sicurezza cancellare il messaggio in cui si inviano le proprie credenziali. Grazie!";



sendTelegramMessage($chat_id, $testo, $token);
?>
