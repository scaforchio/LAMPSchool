<?php

require_once '../lib/req_apertura_sessione.php';

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
@require_once("../lib/funzioni.php");

// istruzioni per tornare alla pagina di login se non c'� una sessione valida

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Inserimento proposte di voto";
$script = "";
stampa_head($titolo, "", $script, "SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='proposte.php'>PROPOSTE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);


$periodo = stringa_html('periodo');
$materia = stringa_html('materia');
$iddocente = stringa_html('iddocente');
$idclasse = stringa_html('idclasse');
$idgruppo = stringa_html('idgruppo');


$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));


//$query="select idalunno as al from tbl_alunni where idclasse=".$idclasse."";
if ($idgruppo == "")
{
    $query = "SELECT idalunno AS al FROM tbl_alunni WHERE idclasse=" . $idclasse . "";
} else
{

    $query = "select tbl_gruppialunni.idalunno as al from tbl_gruppialunni,tbl_alunni
           where tbl_gruppialunni.idalunno=tbl_alunni.idalunno
           and idgruppo in ($idgruppo)
           and idclasse=$idclasse";
}

$ris = eseguiQuery($con, $query);


while ($id = mysqli_fetch_array($ris))            //    <-----------  ttttttt
{
    //  $idal = stringa_html($id['al');
    $vs = "scritto" . $id['al'];
    $vo = "orale" . $id['al'];
    $vp = "pratico" . $id['al'];
    $vu = "unico" . $id['al'];
    $vc = "condotta" . $id['al'];
    $as = "ass" . $id['al'];
    $no = "note" . $id['al'];

    $scritto = stringa_html($vs);
    $orale = stringa_html($vo);
    $pratico = stringa_html($vp);
    $unico = stringa_html($vu);
    $condotta = stringa_html($vc);
    $assenze = stringa_html($as)!=''?stringa_html($as):0;
    $note = stringa_html($no);

    $query = 'DELETE FROM tbl_proposte WHERE idalunno=' . $id['al'] . ' AND periodo="' . $periodo . '" AND idmateria="' . $materia . '"';
    // print $query;
    $ris2 = eseguiQuery($con, $query);
    if ($scritto != 99 | $orale != 99 | $pratico != 99 | $unico != 99 | $condotta != 99)
    {
        if ($periodo < $_SESSION['numeroperiodi'])
        {
            $query = "INSERT INTO tbl_proposte(idalunno,idmateria,periodo,scritto,orale,pratico,unico,condotta,assenze,note)
                  VALUES(" . $id["al"] . "," . $materia . ",'" . $periodo . "','" . $scritto . "','" . $orale . "','" . $pratico . "','" . $unico . "','" . $condotta . "','" . $assenze . "','" . $note . "')";
        } else
        {
            $query = "INSERT INTO tbl_proposte(idalunno,idmateria,periodo,unico,condotta,assenze,note)
                  VALUES(" . $id["al"] . "," . $materia . ",'" . $periodo . "','" . $unico . "','" . $condotta . "','" . $assenze . "','" . $note . "')";
        }

        $ris2 = eseguiQuery($con, $query);
    }
}

echo '
           <p align="center">
           <font size=4 color="black">I dati sono stati inseriti correttamente</font>
         ';

mysqli_close($con);
stampa_piede("");

