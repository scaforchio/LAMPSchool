<?php

require_once '../lib/req_apertura_sessione.php';

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

/* programma per la visualizzazione di un componente scelto di una classe con parametro in 
  ingresso "idcla" e parametro in uscita "idal" */
//connessione al server

@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

// istruzioni per tornare alla pagina di login

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "") {
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
}

$titolo = "Elenco alunni con filtro su cognome e nome";
$script = "";
stampa_head_new($titolo, "", $script, "MASP");
stampa_testata_new("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> -  $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);

$strcogn = trim(stringa_html('strcogn'));
$strnome = trim(stringa_html('strnome'));
$codice = trim(stringa_html('codice'));

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);
if (!$con) {
    print("<h1>Connessione al server fallita</h1>");
}

function ottieni_classe_ell($idclasse){
    global $con;
    $daticlasse = mysqli_fetch_array(eseguiQuery($con, "SELECT anno, sezione, specializzazione FROM tbl_classi WHERE idclasse = $idclasse"));

    if (!$daticlasse) {
        return "Nessuna";
    }

    $anno = $daticlasse['anno'];
    $sezione = $daticlasse['sezione'];
    $specializzazione = $daticlasse['specializzazione'];

    if (strlen($specializzazione) > 3) {
        $specializzazione = substr($specializzazione, 0, 3);
    }

    return $anno . $sezione . " " . $specializzazione;
}

?>

<form action="" method="post">
    <div class="row g-2 mb-2">
        <div class="col">
            <div class="form-floating">
                <input type="text" class="form-control" id="cognome" name="strcogn" value="<?= $strcogn ?>">
                <label for="cognome">Cognome</label>
            </div>
        </div>
        <div class="col">
            <div class="form-floating">
                <input type="text" class="form-control" id="nome" name="strnome" value="<?= $strnome ?>">
                <label for="nome">Nome</label>
            </div>
        </div>
        <div class="col">
            <div class="form-floating">
                <input type="text" class="form-control" id="codice" name="codice" value='<?= $codice ?>'>
                <label for="durata">Codice alunno</label>
            </div>
        </div>
        <div class="col col-auto">
            <button type="submit" class="btn btn-outline-secondary h-100">
                <i class="bi bi-search"></i>
                Ricerca
            </button>
        </div>
    </div>
</form>

<table class="table table-striped table-bordered">
    <thead class="">
        <tr>
            <th colspan="13" class="text-center">
                <div class="row">
                    <div class="col" style="text-align: left;">
                        Alunnni trovati
                    </div>
                    <div class="col col-auto">
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="inserisci_alunno()">
                            <i class="bi bi-person-fill-add"></i>
                            Inserisci Alunno (Senza Classe)
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="torna_elenco_classi()">
                            <i class="bi bi-arrow-left"></i>
                            Torna all'elenco classi
                        </button>
                    </div>
                </div>  
            </th>
        </tr>
        <tr>
            <th>Cognome</th>
            <th>Nome</th>
            <th>Data di Nascita</th>
            <th>Id. Utente</th>
            <th>Classe</th>
            <th>Telefono</th>
            <th>E-mail</th>
            <th>Cert.</th>
            <th>Foto</th>
            <th>Magg.</th>
            <th>Cens.</th>
            <th>Note</th>
            <th>Azioni</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $sql = ""; 
        $result = false;

        if ($codice == ''){
            $sql = "SELECT * FROM tbl_alunni,tbl_utenti
            WHERE tbl_alunni.idalunno=tbl_utenti.idutente
            AND cognome LIKE '%$strcogn%' AND nome LIKE '%$strnome%'
            ORDER BY cognome,nome";
        }else {
            $sql = "SELECT * FROM tbl_alunni,tbl_utenti
            WHERE tbl_alunni.idalunno=tbl_utenti.idutente
            AND idalunno=$codice
            ORDER BY cognome,nome";
        }

        if($strcogn != '' || $strnome != '' || $codice != ''){
            $result = eseguiQuery($con, $sql);
        }
        
        if ($result == false || !(mysqli_num_rows($result) > 0))
        {
            print("<tr><td colspan='13'><center><b>Nessun alunno trovato</b></td></tr>");
        } else
        {
            while ($dati = mysqli_fetch_array($result)) { ?>                
                <tr>
                    <td><?php echo $dati['cognome']; ?></td>
                    <td><?php echo $dati['nome']; ?></td>
                    <td><?php echo data_italiana($dati['datanascita']); ?></td>
                    <td><?php echo $dati['userid']; ?></td>
                    <td><?php echo ottieni_classe_ell($dati['idclasse']); ?></td>
                    <td><?php echo ($dati['telefono']) ? $dati['telefono'] : $dati['telcel']; ?></td>
                    <td><a href='mailto:<?php echo $dati['email']; ?>'><?php echo $dati['email']; ?></a> <a href='mailto:<?php echo $dati['email2']; ?>'><?php echo $dati['email2']; ?></a></td>
                    <td><?php echo ($dati['certificato']) ? "<i style='color: #198753;' class='bi bi-check2-all'></i>" : "<i class='bi bi-x'>"; ?></td>
                    <td><?php echo ($dati['liberatoria'] == 0) ? "<i class='bi bi-x'>" : "<i style='color: #198753;' class='bi bi-check2-all'></i>"; ?></td>
                    <?php echo maggiorenne_new($dati['datanascita']); ?>
                    <?php echo censito_new($dati['datanascita'], $dati['censito'], $dati['cognome'], $dati['nome']); ?>
                    <?php echo visualizza_note_new($dati['note'], $dati['nome'], $dati['cognome']) ?>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-list"></i>
                                Azioni
                            </button>
                            <ul class="dropdown-menu">

                                <li>
                                    <a class="dropdown-item" href='vis_alu_mod.php?idal=<?=$dati['idalunno']?>'>
                                        <i class="bi spa bi-pencil"></i>
                                        Modifica
                                    </a>
                                </li>

                                <?php if(poss_canc_alu($dati['idalunno'], $con)) { ?>
                                <li>
                                    <a class="dropdown-item" href='alu_conf.php?idal=<?= $dati['idalunno'] ?>&idcla=<?= $dati['idclasse'] ?>'>
                                        <i class="bi spa bi-trash"></i>
                                        Elimina
                                    </a>
                                </li>
                                <?php } ?>

                                <li>
                                    <a class="dropdown-item" href='../alunni/genassotp.php?idalu=<?= $dati['idalunno']?>'>
                                        <i class="bi spa bi-shield-check"></i>
                                        Rigenera OTP Tutor
                                    </a>
                                </li>

                                <li>
                                    <a class="dropdown-item" href='../password/rigenera_password_ins_sta.php?idalu=<?= $dati['idalunno'] ?>'>
                                        <i class="bi spa bi-key"></i>
                                        Rigenera Password Tutor
                                    </a>
                                </li>

                                <li>
                                    <a class="dropdown-item" href='../password/alu_rigenera_password_ins_sta.php?idalu=<?= $dati['idalunno'] ?>'>
                                        <i class="bi spa bi-key"></i>
                                        Rigenera Rassword Alunno
                                    </a>
                                </li>

                                <li>
                                    <a class="dropdown-item" target='_blank' href='../alunni/prefcens.php?idalu=<?= $dati['idalunno']?>'>
                                        <i class="bi spa bi-person-gear"></i>
                                        Preferenze censimento
                                    </a>
                                </li>

                                <li>
                                    <a class="dropdown-item" 
                                        href="javascript:barcode_new(`<?= strtoupper($_SESSION['suffisso']) . $dati['idalunno'] ?>`, `<?= $dati['cognome'] . " " . $dati['nome'] ?>`)">
                                        <i class="bi spa bi-upc-scan"></i>
                                        Codice a barre
                                    </a>
                                </li>

                                <li>
                                    <a class="dropdown-item" href="javascript:foto_alunno(<?= $dati['idalunno'] ?>, `<?= $dati['cognome'] . " " . $dati['nome'] ?>`)">
                                        <i class="bi spa bi-camera"></i>
                                        Gestisci foto annuario
                                    </a>
                                </li>

                                <?php if($tipoutente == 'M') { ?>
                                <li>
                                    <a class="dropdown-item" href='../contr/cambiautenteok.php?nuovoutente=<?= $dati['userid'] ?>'>
                                        <i class="bi spa bi-person-lock"></i>
                                        Assumi identità tutor
                                    </a>
                                </li>
                                <?php } ?>
                            </ul>
                        </div>
                    </td>
                </tr>
            <?php } 
        } ?>
    </tbody>
</table>

<script>
    function inserisci_alunno() {
        window.location.href = 'vis_alu_ins.php';
    }

    function torna_elenco_classi() {
        window.location.href = 'vis_alu_cla.php';
    }
</script>

<?php

insCodiceClientModalAnnuario();
mysqli_close($con);
stampa_piede_new("");
