<?php

/**
 * Created by PhpStorm.
 * User: pietro
 * Date: 16/06/15
 * Time: 18.20
 */

/**
 * Funzione che trasforma i decimali del voto in modificatori
 *
 * @param string $voto
 * @return string
 */
function generaSchemaToken()  // OTP per conferma accesso
{
    $token = "";
    $numeri = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
    for ($i = 0; $i < 5; $i++)
    {
        //Mischia l'array
        shuffle($numeri);
        for ($j = 0; $j < 10; $j++)
        {
            $token .= $numeri[$j];
        }
    }
    return $token;
}

function generaToken($lunghezza)  // OTP per conferma accesso
{
    $token = "";
    $numeri = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
    for ($i = 0; $i < $lunghezza; $i++)
    {
        $casuale = rand(0, 9);
        $token .= $numeri[$casuale];
    }
    return $token;
}

/*
 * Mdodoinvio
 * sms
 * email
 * telegram
 */

function inserisciToken($con, $lunghezza, $idutente, $tipo, $modoinvio, $parametroinvio, $massimiutilizzi = 3, $duratamassima = 30)  // OTP per conferma accesso
{
    $token = generaToken($lunghezza);
    $date = new DateTime();
    $ts = $date->getTimestamp();
    $tsscad = $ts + $duratamassima * 60;
    $query = "delete from tbl_otp where numutilizzi>nummaxutilizzi";
    eseguiQuery($con, $query);
    $query = "insert into tbl_otp(valore,idutente,funzione,nummaxutilizzi,timecreazione,timeultimoutilizzo)"
            . "values ('$token',$idutente,'$tipo',$massimiutilizzi,$ts,$tsscad)";
    eseguiQuery($con, $query);
    if ($modoinvio == 'sms')
        $esito = inviaSMS($parametroinvio, "O.T.P. per conferma operazione $token", $con, "otp",$idutente);
    if ($modoinvio == 'email')
        $esito = invia_mail($parametroinvio, "O.T.P. per conferma operazione $token", "La password momentanea per conferma operazione: $token");
    if ($modoinvio == 'telegram')
        $esito = sendTelegramMessage($parametroinvio, "O.T.P. per conferma operazione $token", $_SESSION['tokenbototp']);
   // print "Esito invio $esito";
    return $esito;
}

/*
 * 1 - OTP OK
 * 2 - OTP NON ESISTENTE
 * 3 - OTP SCADUTO
 * 4 - TENTATIVI OTP OLTRE QUELLI PERMESSI
 * 5 - OTP ERRATO
 */

function verificaToken($con, $idutente, $tipo, $valore)  // OTP per conferma accesso
{
    $date = new DateTime();
    $ts = $date->getTimestamp();
    $query = "select * from tbl_otp where idutente='$idutente' and funzione='$tipo'";
    $ris = eseguiQuery($con, $query);
    if (mysqli_num_rows($ris) == 0)
        return 2;
    else
    {
        $rec = mysqli_fetch_array($ris);
        if ($valore == $rec['valore'] & $ts < $rec['timeultimoutilizzo'])
        {
            $query = "delete from tbl_otp where idutente='$idutente' and funzione='$tipo'";
            eseguiQuery($con, $query);
            return 1;
        } else
        {
            if ($ts > $rec['timeultimoutilizzo'])
            {
                $query = "delete from tbl_otp where idutente='$idutente' and funzione='$tipo'";
                eseguiQuery($con, $query);
                return 3;
            } else
            {
                if ($valore != $rec['valore'])
                {
                    
                        $query = "update tbl_otp set numutilizzi=numutilizzi+1";
                        eseguiQuery($con, $query);
                        if ($rec['numutilizzi']==$rec['nummaxutilizzi']-1)
                        {
                            $query = "delete from tbl_otp where idutente='$idutente' and funzione='$tipo'";
                            eseguiQuery($con, $query);
                            return 4;
                        }
                        return 5;
                    
                }
            }
        }
    }
}
