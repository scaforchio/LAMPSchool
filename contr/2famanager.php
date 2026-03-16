<?php

require_once '../lib/req_apertura_sessione.php';

/*
  Copyright (C) 2025 Vittorio Lo Mele
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

/* gestione 2fa per tutti gli utenti */

@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

use RobThree\Auth\TwoFactorAuth;

// istruzioni per tornare alla pagina di login

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "") {
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Gestione TOTP";

stampa_head_new($titolo, "", "", "PMSDATL"); 
stampa_testata_new("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);
if (!$con) {
    print("<h1> Connessione al server fallita </h1>");
    exit;
}

// Leggo lo stato corrente dal DB
$statoCorrenteQuery = "SELECT totpsecret FROM tbl_utenti WHERE idutente = " . (int)$_SESSION['idutente'];
$statoCorrente      = eseguiQuery($con, $statoCorrenteQuery);
$statoCorrenteRes   = mysqli_fetch_assoc($statoCorrente)['totpsecret'];
 
// TOTP è attivo se il campo NON è 'disabled' ed è non vuoto
$totpAttivo = ($statoCorrenteRes !== 'disabled' && !empty($statoCorrenteRes));
 
// Inizializzo la libreria
$issuer = !empty($_SESSION['nome_scuola']) ? $_SESSION['nome_scuola'] : 'LAMPSchool';
$tfa    = new TwoFactorAuth($issuer);
 
// Preparo un secret pendente in sessione (solo quando TOTP non attivo)
if (!$totpAttivo && empty($_SESSION['totp_pending_secret'])) {
    $_SESSION['totp_pending_secret'] = $tfa->createSecret(128);
}
 
$messaggio    = '';
$tipoMsgClass = '';
 
// ── Gestione POST ─────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $azione         = $_POST['azione'] ?? '';
    $codiceInserito = preg_replace('/\D/', '', $_POST['codice_otp'] ?? '');
 
    // Attivazione: verifica OTP e scrive il secret nel DB
    if ($azione === 'attiva') {
        $secretPending = $_SESSION['totp_pending_secret'] ?? '';
 
        if (empty($codiceInserito)) {
            $messaggio    = 'Inserisci il codice OTP dalla tua app.';
            $tipoMsgClass = 'warning';
        } elseif ($tfa->verifyCode($secretPending, $codiceInserito)) {
            $secretSafe = mysqli_real_escape_string($con, $secretPending);
            $q = "UPDATE tbl_utenti SET totpsecret = '$secretSafe' WHERE idutente = " . (int)$_SESSION['idutente'];
            eseguiQuery($con, $q);
            unset($_SESSION['totp_pending_secret']);
            $statoCorrenteRes = $secretPending;
            $totpAttivo       = true;
            $messaggio        = 'TOTP attivato con successo!';
            $tipoMsgClass     = 'success';
        } else {
            $messaggio    = 'Codice OTP non valido. Controlla l\'orario e riprova.';
            $tipoMsgClass = 'danger';
        }
 
    // Disattivazione: verifica OTP e rimette 'disabled'
    } elseif ($azione === 'disattiva') {
        if (empty($codiceInserito)) {
            $messaggio    = 'Inserisci il codice OTP per confermare la disattivazione.';
            $tipoMsgClass = 'warning';
        } elseif ($tfa->verifyCode($statoCorrenteRes, $codiceInserito)) {
            $q = "UPDATE tbl_utenti SET totpsecret = 'disabled' WHERE idutente = " . (int)$_SESSION['idutente'];
            eseguiQuery($con, $q);
            $statoCorrenteRes                = 'disabled';
            $totpAttivo                      = false;
            $_SESSION['totp_pending_secret'] = $tfa->createSecret(); // pronto per eventuale riattivazione
            $messaggio                       = 'TOTP disattivato correttamente.';
            $tipoMsgClass                    = 'success';
        } else {
            $messaggio    = 'Codice OTP non valido. Controlla l\'orario e riprova.';
            $tipoMsgClass = 'danger';
        }
    }
}
 
// QR code inline come data URI (solo quando TOTP non attivo)
$qrCodeDataUri = '';
if (!$totpAttivo) {
    $label         = rawurlencode($_SESSION['nome'] ?? 'utente');
    $secretPending = $_SESSION['totp_pending_secret'];
    $qrCodeDataUri = $tfa->getQRCodeImageAsDataUri($label, $secretPending);
}
?>
 
<style>
    .totp-wrap   { max-width: 500px; margin: 2rem auto; }
    .otp-input   { letter-spacing: .4em; font-size: 1.35rem;
                   font-family: 'Courier New', monospace; text-align: center; }
    .qr-box      { background: #fff; display: inline-block; padding: 10px;
                   border-radius: 8px; border: 1px solid #dee2e6; }
    .secret-code { font-family: 'Courier New', monospace; letter-spacing: .1em; }
</style>
 
<div class="totp-wrap px-3">
 
    <?php if (!empty($messaggio)): ?>
    <div class="alert alert-<?= $tipoMsgClass ?> alert-dismissible d-flex align-items-center gap-2" role="alert">
        <i class="bi bi-<?= $tipoMsgClass === 'success' ? 'check-circle-fill' : ($tipoMsgClass === 'danger' ? 'x-circle-fill' : 'exclamation-triangle-fill') ?>"></i>
        <?= htmlspecialchars($messaggio) ?>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
 
    <?php if (!$totpAttivo): ?>
    <!-- ── TOTP NON ATTIVO ─────────────────────────────────────── -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
 
            <h5 class="fw-semibold mb-1">
                <i class="bi bi-shield-slash text-secondary me-2"></i>TOTP non attivo
            </h5>
            <p class="text-muted small mb-4">
                Scansiona il QR con un'app authenticator (es. Google Authenticator, Authy),
                poi inserisci il codice a 6 cifre per attivare la verifica in due passaggi.
            </p>
 
            <div class="text-center mb-3">
                <div class="qr-box">
                    <img src="<?= $qrCodeDataUri ?>" alt="QR Code TOTP" width="180" height="180">
                </div>
            </div>
 
            <p class="text-center small text-muted mb-1">Inserimento manuale</p>
            <p class="text-center mb-4">
                <code class="secret-code bg-light px-2 py-1 rounded">
                    <?= htmlspecialchars($_SESSION['totp_pending_secret']) ?>
                </code>
            </p>
 
            <form method="post">
                <input type="hidden" name="azione" value="attiva">
                <label class="form-label fw-semibold" for="codice_otp">Codice OTP</label>
                <div class="input-group">
                    <input type="text"
                           id="codice_otp"
                           name="codice_otp"
                           class="form-control otp-input"
                           placeholder="000000"
                           maxlength="6"
                           inputmode="numeric"
                           pattern="\d{6}"
                           autocomplete="one-time-code"
                           required>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-shield-check me-1"></i>Attiva
                    </button>
                </div>
                <div class="form-text">Inserisci il codice mostrato dall'app prima che scada (30 s).</div>
            </form>
 
        </div>
    </div>
 
    <?php else: ?>
    <!-- ── TOTP ATTIVO ─────────────────────────────────────────── -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
 
            <h5 class="fw-semibold mb-1">
                <i class="bi bi-shield-fill-check text-success me-2"></i>TOTP attivo
            </h5>
            <p class="text-muted small mb-4">
                La verifica in due passaggi è abilitata sul tuo account.
                Per disattivarla, inserisci il codice OTP attuale generato dalla tua app authenticator.
            </p>
 
            <form method="post">
                <input type="hidden" name="azione" value="disattiva">
                <label class="form-label fw-semibold" for="codice_otp">Codice OTP</label>
                <div class="input-group">
                    <input type="text"
                           id="codice_otp"
                           name="codice_otp"
                           class="form-control otp-input"
                           placeholder="000000"
                           maxlength="6"
                           inputmode="numeric"
                           pattern="\d{6}"
                           autocomplete="one-time-code"
                           required>
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="bi bi-shield-slash me-1"></i>Disattiva
                    </button>
                </div>
                <div class="form-text">Inserisci il codice per confermare la disattivazione.</div>
            </form>
 
        </div>
    </div>
    <?php endif; ?>
 
</div>
 
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc4s9bIOgUxi8T/jzmF5REq8/2KoILgFo23T16OxGRyR"
        crossorigin="anonymous"></script>
 
<?php
stampa_piede_new("");
mysqli_close($con);
?>