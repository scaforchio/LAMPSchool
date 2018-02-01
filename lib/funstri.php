<?php
/**
 * Created by PhpStorm.
 * User: pietro
 * Date: 16/06/15
 * Time: 18.01
 */


/**
 *
 * @param string $haystack
 * @param string $needle
 * @return string
 */
function startsWith($haystack, $needle)
{
    return !strncmp($haystack, $needle, strlen($needle));
}

/**
 * Controlla nella stringa se c'è un numero
 *
 * @param string $stringa
 * @param boolean $decode valore predefinito true per essere codificata come al momento dell'inserimento
 * @return int ritorna 1 se è stato trovato
 */
function controlla_stringa($stringa = '', $decode = true)
{

    if ($decode)
    {
        $stringa = html_entity_decode($stringa, ENT_QUOTES, 'UTF-8');
    }
    $l = strlen($stringa);
    for ($i = 0; $i <= $l - 1; $i++)
    {
        $car = substr($stringa, $i, 1);

        if (is_numeric($car))
        {
            return 1;

        }
    }
}

function elimina_spazi($comando)
{
    $comando = str_replace(array("\n", "\r", "\t"), " ", $comando);
    $comando_pulito = "";
    for ($i = 0; $i < strlen($comando); $i++)
    {
        if (substr($comando, $i, 1) == " ")
        {
            if (substr($comando, $i + 1, 1) != ' ')
            {
                $comando_pulito .= substr($comando, $i, 1);
            }
        }

        else
        {
            $comando_pulito .= $comando[$i];
        }

    }
    return $comando_pulito;
}

/**
 * Converte in utf 8 una stringa
 *
 * @param string $stringa
 * @return string $stringa in utf8
 */

function converti_utf8($stringa)
{
    $strpul = "";
    $strpul = mb_convert_encoding($stringa, 'windows-1252', 'UTF-8');
    /*
    $iconv = extension_loaded('iconv');
    if($iconv)
      {
           $strpul = iconv('UTF-8', 'windows-1252', $stringa);

           $numcicli=floor(strlen($stringa)/5000);
           for ($i=0;$i<=$numcicli;$i++)
           {
               $strpul.=iconv('UTF-8', 'windows-1252', substr($stringa,5000*$i,5000));
           }

        }
   else
       $strpul=utf8_decode($stringa); */
    return $strpul;
}


