<?php

//
// Codice per ricerca voti pratici già inseriti nella giornata
//
$tipivoti = ['S', 'O', 'P'];
foreach ($tipivoti as $tv)
{
    $va = "voto" . strtolower($tv) . $idal;
    $ga = "giudizio" . strtolower($tv) . $idal;
    $arrvoto = ricerca_voto($val['idalunno'], $tv, $idaluvoti, $tipi, $valutazioni, $giudizi, $idvoti, $docenti);
    if ($arrvoto[0] != 0)
    {
        $esiste_voto = true;
        $voto = $arrvoto[1];
        $giudizio = $arrvoto[2];
        $docente = $arrvoto[3];
        $voto_medio = voto_combinato($arrvoto[0], $con);
        $altro_docente = ($docente != $id_ut_doc);
    } else
    {
        $esiste_voto = false;
    }

    if ($esiste_voto)
    {
        echo '<td>';
        if ($_SESSION['valutazionedecimale'] == 'yes')
        {
            if (!$voto_medio & !$altro_docente)
            {
                echo "<select class='smallchar' name='$va" . $val["idalunno"] . "'><option value=99>&nbsp;";
            } else
            {
                echo "<select class='smallchar' name='$va" . $val["idalunno"] . "' disabled><option value=99>&nbsp;";
            }
            if ($_SESSION['ordinevalutazioni'] == 'C')
            {
                for ($v = $_SESSION['votominimoattribuibile']; $v <= 10; $v = $v + $incrementovoto)
                {
                    if ($voto == $v)
                    {
                        echo '<option value=' . $v . ' selected>' . dec_to_mod($v);
                    } else
                    {
                        echo '<option value=' . $v . '>' . dec_to_mod($v);
                    }
                }
            } else
            {
                for ($v = 10; $v >= $_SESSION['votominimoattribuibile']; $v = $v - $incrementovoto)
                {
                    if ($voto == $v)
                    {
                        echo '<option value=' . $v . ' selected>' . dec_to_mod($v);
                    } else
                    {
                        echo '<option value=' . $v . '>' . dec_to_mod($v);
                    }
                }
            }

            echo '</select>&nbsp';
        } else
        {
            echo "<input type='hidden' name=$va" . $val["idalunno"] . "' value=99>";
        }
        echo "<input class='smallchar' type='text' size=15 maxlength=1000 name='$ga" . $val["idalunno"] . "' value='" . $giudizio . "'>
                          </td>";
    } else
    {
        echo '<td>';
        if ($_SESSION['valutazionedecimale'] == 'yes')
        {
            echo "<select class='smallchar' name='$va" . $val["idalunno"] . "'><option value=99>&nbsp;";
            if ($_SESSION['ordinevalutazioni'] == 'C')
            {
                for ($v = $_SESSION['votominimoattribuibile']; $v <= 10; $v = $v + $incrementovoto)
                {
                    echo '<option value=' . $v . '>' . dec_to_mod($v);
                }
            } else
            {
                for ($v = 10; $v >= $_SESSION['votominimoattribuibile']; $v = $v - $incrementovoto)
                {
                    echo '<option value=' . $v . '>' . dec_to_mod($v);
                }
            }

            echo '</select>&nbsp;';
        } else
        {
            echo "<input type='hidden' name='$va" . $val["idalunno"] . "' value=99>";
        }
        echo "<input class='smallchar' type='text' size=15 maxlength=1000 name='$ga" . $val["idalunno"] . "'>
                           </td>";
    }
}
            