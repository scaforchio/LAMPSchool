<?php
  session_start();
  $suffisso=$_GET['suffisso'];
@require_once("../php-ini" . $suffisso . ".php");
@require_once("../lib/funzioni.php");
  //$token = "987901422:AAG-T0WEzGDy_jYqfe5e2xNqEh0PPXUcv3g";
$token = $tokenbototp;
  $tokendiconferma = elimina_apici($_GET["tokendiconferma"]);
  $chat_id = elimina_apici($_GET["chatid"]);

  $con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore connessione");
  require '../lib/req_assegna_parametri_a_sessione.php';
  $indirizzoip = IndirizzoIpReale();
  $_SESSION['indirizzoip'] = $indirizzoip;
  $ultimoaccesso = "";
  //  $_SESSION['versione']=$versione;
  //Connessione al server SQL
  if (!$con) {
      die("<h1> Connessione al server fallita </h1>");
  }

  $query = "select idutente from tbl_confermatelegram where tokendiconferma = '".$tokendiconferma."'";

  $risultato = eseguiQuery($con, $query);
  if (mysqli_num_rows($risultato) > 0)
  {
      $tmp = mysqli_fetch_array($risultato);
      $idutente = $tmp[0];
      $sql = "UPDATE tbl_utenti SET idtelegram = ".$chat_id." WHERE idutente = '" . $idutente ."'";
      $result = eseguiQuery($con, $sql);
      //Lo registro automaticamente per il token
      $sql = "UPDATE tbl_utenti SET modoinviotoken = 'G' WHERE idutente = '" . $idutente ."'";
      $result = eseguiQuery($con, $sql);
      $sql = "DELETE FROM tbl_confermatelegram WHERE idutente = '" . $idutente ."'";
      $result = eseguiQuery($con, $sql);
      sendTelegramMessage($chat_id, "Registrazione effettuata!", $token); //va registrato nei log
      echo "<h1>Registrazione effettuata!</h1>";
  }else{
    echo "<h1>Questo token non è più valido! Controlla se hai ricevuto altre email per la conferma!</h1>";
  }

?>
