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
function dec_to_mod($voto)
{
    // La riga seguente serve a normalizzare il voto (Es. 4.18 -> 4.25)
    $voto = round($voto * 4) / 4;

    if ($voto == 0)
        return "0&nbsp;";
    if ($voto == 0.25)
        return "0+";
    if ($voto == 0.5)
        return "0&#189;";
    if ($voto == 0.75)
        return "1-";
    if ($voto == 1)
        return "1&nbsp;";
    if ($voto == 1.25)
        return "1+";
    if ($voto == 1.5)
        return "1&#189;";
    if ($voto == 1.75)
        return "2-";
    if ($voto == 2)
        return "2&nbsp;";
    if ($voto == 2.25)
        return "2+";
    if ($voto == 2.5)
        return "2&#189;";
    if ($voto == 2.75)
        return "3-";
    if ($voto == 3)
        return "3&nbsp;";
    if ($voto == 3.25)
        return "3+";
    if ($voto == 3.5)
        return "3&#189;";
    if ($voto == 3.75)
        return "4-";
    if ($voto == 4)
        return "4&nbsp;";
    if ($voto == 4.25)
        return "4+";
    if ($voto == 4.5)
        return "4&#189;";
    if ($voto == 4.75)
        return "5-";
    if ($voto == 5)
        return "5&nbsp;";
    if ($voto == 5.25)
        return "5+";
    if ($voto == 5.5)
        return "5&#189;";
    if ($voto == 5.75)
        return "6-";
    if ($voto == 6)
        return "6&nbsp;";
    if ($voto == 6.25)
        return "6+";
    if ($voto == 6.5)
        return "6&#189;";
    if ($voto == 6.75)
        return "7-";
    if ($voto == 7)
        return "7&nbsp;";
    if ($voto == 7.25)
        return "7+";
    if ($voto == 7.5)
        return "7&#189;";
    if ($voto == 7.75)
        return "8-";
    if ($voto == 8)
        return "8&nbsp;";
    if ($voto == 8.25)
        return "8+";
    if ($voto == 8.5)
        return "8&#189;";
    if ($voto == 8.75)
        return "9-";
    if ($voto == 9)
        return "9&nbsp;";
    if ($voto == 9.25)
        return "9+";
    if ($voto == 9.5)
        return "9&#189;";
    if ($voto == 9.75)
        return "10-";
    if ($voto == 10)
        return "10";
    if ($voto == 99)
        return "&nbsp;&nbsp;";
    if ($voto == 11)
        return "NC";
    if ($voto == 12)
        return $_SESSION['g02'];
    if ($voto == 13)
        return $_SESSION['g03'];
    if ($voto == 14)
        return $_SESSION['g04'];
    if ($voto == 15)
        return $_SESSION['g05'];
    if ($voto == 16)
        return $_SESSION['g06'];
    if ($voto == 17)
        return $_SESSION['g07'];
    if ($voto == 18)
        return $_SESSION['g08'];
    if ($voto == 19)
        return $_SESSION['g09'];
    if ($voto == 20)
        return $_SESSION['g10'];
    if ($voto == 21)
        return $_SESSION['gc01'];
    if ($voto == 22)
        return $_SESSION['gc02'];
    if ($voto == 23)
        return $_SESSION['gc03'];
    if ($voto == 24)
        return $_SESSION['gc04'];
    if ($voto == 25)
        return $_SESSION['gc05'];
    if ($voto == 26)
        return $_SESSION['gc06'];
    if ($voto == 27)
        return $_SESSION['gc07'];
    if ($voto == 28)
        return $_SESSION['gc08'];
    if ($voto == 29)
        return $_SESSION['gc09'];
    if ($voto == 30)
        return $_SESSION['gc10'];
}

/**
 *
 * @param string $voto
 * @return string
 */
function dec_to_csv($voto)
{

    // La riga seguente serve a normalizzare il voto (Es. 4.18 -> 4.25)
    $voto = round($voto * 4) / 4;
    if ($voto == 0)
        return "0";
    if ($voto == 0.25)
        return "0+";
    if ($voto == 0.5)
        return "0½";
    if ($voto == 0.75)
        return "1-";
    if ($voto == 1)
        return "1";
    if ($voto == 1.25)
        return "1+";
    if ($voto == 1.5)
        return "1½";
    if ($voto == 1.75)
        return "2-";
    if ($voto == 2)
        return "2";
    if ($voto == 2.25)
        return "2+";
    if ($voto == 2.5)
        return "2½";
    if ($voto == 2.75)
        return "3-";
    if ($voto == 3)
        return "3";
    if ($voto == 3.25)
        return "3+";
    if ($voto == 3.5)
        return "3½";
    if ($voto == 3.75)
        return "4-";
    if ($voto == 4)
        return "4";
    if ($voto == 4.25)
        return "4+";
    if ($voto == 4.5)
        return "4½";
    if ($voto == 4.75)
        return "5-";
    if ($voto == 5)
        return "5";
    if ($voto == 5.25)
        return "5+";
    if ($voto == 5.5)
        return "5½";
    if ($voto == 5.75)
        return "6-";
    if ($voto == 6)
        return "6";
    if ($voto == 6.25)
        return "6+";
    if ($voto == 6.5)
        return "6½";
    if ($voto == 6.75)
        return "7-";
    if ($voto == 7)
        return "7";
    if ($voto == 7.25)
        return "7+";
    if ($voto == 7.5)
        return "7½";
    if ($voto == 7.75)
        return "8-";
    if ($voto == 8)
        return "8";
    if ($voto == 8.25)
        return "8+";
    if ($voto == 8.5)
        return "8½";
    if ($voto == 8.75)
        return "9-";
    if ($voto == 9)
        return "9";
    if ($voto == 9.25)
        return "9+";
    if ($voto == 9.5)
        return "9½";
    if ($voto == 9.75)
        return "10-";
    if ($voto == 10)
        return "10";
    if ($voto == 99)
        return "Annotaz.";
    if ($voto == 11)
        return "N.C.";
    if ($voto == 12)
        return $_SESSION['g02'];
    if ($voto == 13)
        return $_SESSION['g03'];
    if ($voto == 14)
        return $_SESSION['g04'];
    if ($voto == 15)
        return $_SESSION['g05'];
    if ($voto == 16)
        return $_SESSION['g06'];
    if ($voto == 17)
        return $_SESSION['g07'];
    if ($voto == 18)
        return $_SESSION['g08'];
    if ($voto == 19)
        return $_SESSION['g09'];
    if ($voto == 20)
        return $_SESSION['g10'];
    if ($voto == 21)
        return $_SESSION['gc01'];
    if ($voto == 22)
        return $_SESSION['gc02'];
    if ($voto == 23)
        return $_SESSION['gc03'];
    if ($voto == 24)
        return $_SESSION['gc04'];
    if ($voto == 25)
        return $_SESSION['gc05'];
    if ($voto == 26)
        return $_SESSION['gc06'];
    if ($voto == 27)
        return $_SESSION['gc07'];
    if ($voto == 28)
        return $_SESSION['gc08'];
    if ($voto == 29)
        return $_SESSION['gc09'];
    if ($voto == 30)
        return $_SESSION['gc10'];
}

/**
 *
 * @param string $voto
 * @return string
 */
function dec_to_vot($voto)
{
    if ($voto == 0)
        return "";
    if ($voto == 1)
        return "1";
    if ($voto == 2)
        return "2";
    if ($voto == 3)
        return "3";
    if ($voto == 4)
        return "4";
    if ($voto == 5)
        return "5";
    if ($voto == 6)
        return "6";
    if ($voto == 7)
        return "7";
    if ($voto == 8)
        return "8";
    if ($voto == 9)
        return "9";
    if ($voto == 10)
        return "10";
    if ($voto == 11)
        return "N.C.";
    if ($voto == 12)
        return $_SESSION['g02'];
    if ($voto == 13)
        return $_SESSION['g03'];
    if ($voto == 14)
        return $_SESSION['g04'];
    if ($voto == 15)
        return $_SESSION['g05'];
    if ($voto == 16)
        return $_SESSION['g06'];
    if ($voto == 17)
        return $_SESSION['g07'];
    if ($voto == 18)
        return $_SESSION['g08'];
    if ($voto == 19)
        return $_SESSION['g09'];
    if ($voto == 20)
        return $_SESSION['g10'];
    if ($voto == 21)
        return $_SESSION['gc01'];
    if ($voto == 22)
        return $_SESSION['gc02'];
    if ($voto == 23)
        return $_SESSION['gc03'];
    if ($voto == 24)
        return $_SESSION['gc04'];
    if ($voto == 25)
        return $_SESSION['gc05'];
    if ($voto == 26)
        return $_SESSION['gc06'];
    if ($voto == 27)
        return $_SESSION['gc07'];
    if ($voto == 28)
        return $_SESSION['gc08'];
    if ($voto == 29)
        return $_SESSION['gc09'];
    if ($voto == 30)
        return $_SESSION['gc10'];
}

/**
 *
 * @param string $voto
 * @return string
 */
function dec_to_pag($voto)
{
    if ($voto == 0)
        return "";
    if ($voto == 1)
        return "Uno";
    if ($voto == 2)
        return "Due";
    if ($voto == 3)
        return "Tre";
    if ($voto == 4)
        return "Quattro";
    if ($voto == 5)
        return "Cinque";
    if ($voto == 6)
        return "Sei";
    if ($voto == 7)
        return "Sette";
    if ($voto == 8)
        return "Otto";
    if ($voto == 9)
        return "Nove";
    if ($voto == 10)
        return "Dieci";
    if ($voto == 11)
        return "Non classificato";
    if ($voto == 12)
        return $_SESSION['giud02'];
    if ($voto == 13)
        return $_SESSION['giud03'];
    if ($voto == 14)
        return $_SESSION['giud04'];
    if ($voto == 15)
        return $_SESSION['giud05'];
    if ($voto == 16)
        return $_SESSION['giud06'];
    if ($voto == 17)
        return $_SESSION['giud07'];
    if ($voto == 18)
        return $_SESSION['giud08'];
    if ($voto == 19)
        return $_SESSION['giud09'];
    if ($voto == 20)
        return $_SESSION['giud10'];
    if ($voto == 21)
        return $_SESSION['giudcomp01'];
    if ($voto == 22)
        return $_SESSION['giudcomp02'];
    if ($voto == 23)
        return $_SESSION['giudcomp03'];
    if ($voto == 24)
        return $_SESSION['giudcomp04'];
    if ($voto == 25)
        return $_SESSION['giudcomp05'];
    if ($voto == 26)
        return $_SESSION['giudcomp06'];
    if ($voto == 27)
        return $_SESSION['giudcomp07'];
    if ($voto == 28)
        return $_SESSION['giudcomp08'];
    if ($voto == 29)
        return $_SESSION['giudcomp09'];
    if ($voto == 30)
        return $_SESSION['giudcomp10'];
}

/**
 *
 * @param string $voto
 * @return boolean
 */
function insufficiente($voto)
{
    if (($voto > 0 and $voto < 6) | ($voto > 10 & $voto < 15))
    {
        return true;
    }
}

/**
 *
 * @param int $idalunno , $periodo
 * @param object $conn Connessione al db
 * @return int
 */
function calcola_media_condotta($idalunno, $periodo, $conn)
{
    $query = "SELECT condotta FROM tbl_proposte WHERE idalunno=$idalunno AND periodo='$periodo'";

    $riscond = mysqli_query($conn, inspref($query)) or die("Errore:" . inspref($query));
    $totcond = 0;
    $numcond = 0;
    $numgiud = 0;
    while ($reccond = mysqli_fetch_array($riscond))
    {
        $cond = $reccond['condotta'];


        if ($cond != 99)
        {

            if ($cond > 11)
            {
                if ($cond < 21)
                {
                    $numgiud++;
                    $cond = $cond - 10;
                    $numcond++;
                    $totcond += $cond;
                }
                else
                {
                    $numgiud++;
                    $cond = $cond - 20;
                    $numcond++;
                    $totcond += $cond;
                }
            }
            else
            {
                $numcond++;
                $totcond += $cond;
            }
        }
       // print "$idalunno $numcond $totcond";
    }
    // SE NON CI SONO VOTI DI CONDOTTA O SONO TUTTI NC RESTITUISCO NC
    if ($numcond == 0)
    {
        return 11;
    }
    
    $media = ceil($totcond / $numcond);
    
    //print " media $media <br>";
    // SE PREVALGONO I GIUDIZI RESTITUISCO UN GIUDIZIO ALTRIMENTI UN NUMERO
    if ($numgiud > ($numcond / 2))
    {
        return ($media + 20);
    }
    else
    {
        return $media;
    }
    
}

/**
 *
 * @param int $idalunno , $periodo
 * @param object $conn Connessione al db
 * @return int or string
 */
function estrai_voto_ammissione($idalunno, $conn)
{
    $query = "SELECT votoammissione FROM tbl_esiti WHERE idalunno=$idalunno";

    $risesito = mysqli_query($conn, inspref($query));
    $votoamm = "--";
    if ($recesito = mysqli_fetch_array($risesito))
    {
        $votoamm = $recesito['votoammissione'];
    }

    return $votoamm;
}

/**
 *
 * @param int $idalunno , $periodo
 * @param object $conn Connessione al db
 * @return string
 */
function estrai_giudizio($idalunno, $periodo, $conn)
{
    $query = "SELECT giudizio FROM tbl_giudizi WHERE idalunno=$idalunno and periodo=$periodo";

    $risgiudizio = mysqli_query($conn, inspref($query));
    $giud = "--";
    if ($recgiudizio = mysqli_fetch_array($risgiudizio))
    {
        $giud = $recgiudizio['giudizio'];
    }

    return $giud;
}

/**
 *
 * @param int $idalunno , $periodo
 * @param object $conn Connessione al db
 * @return int or string
 */
function estrai_esito($idalunno, $conn, $integrativo = false)
{
    $query = "SELECT validita,esito,integrativo FROM tbl_esiti WHERE idalunno=$idalunno";

    $risesito = mysqli_query($conn, inspref($query));
    $esito = "--";
    if ($recesito = mysqli_fetch_array($risesito))
    {
        /* if (!$integrativo)
          $esito = str_replace("|", " ", decodifica_esito($recesito['esito'], $conn));
          else
          $esito = str_replace("|", " ", decodifica_esito($recesito['integrativo'], $conn)); */
        $esito = str_replace("|", " ", decodifica_esito($recesito['esito'], $conn)) . "" . str_replace("|", " ", decodifica_esito($recesito['integrativo'], $conn));
    }


    return $esito;
}

/**
 *
 * @param int $idalunno , $periodo
 * @param object $conn Connessione al db
 * @return int
 */
function estrai_idtipoesito($idalunno, $conn, $integrativo = false)
{
    $query = "SELECT esito FROM tbl_esiti WHERE idalunno=$idalunno";

    $risesito = mysqli_query($conn, inspref($query));
    $esito = "-1";
    if ($recesito = mysqli_fetch_array($risesito))
    {

        $esito = $recesito['esito'];
    }


    return $esito;
}
