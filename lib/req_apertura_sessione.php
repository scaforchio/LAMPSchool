<?php

session_start();
if (!isset($_SESSION['prefisso']))
{
    stampa_sessionescaduta();
    /* OLD 
    print "<br><br><b><big><center>Sessione scaduta!</center></big></b>";
    print "<br><b><big><center>Rieffettuare il <a href='../'>login</a>.</center></big></b>";
    */
    die;
}

function stampa_sessionescaduta(){
    ?>
        <head>
            <link rel='stylesheet' href='../vendor/twbs/bootstrap/dist/css/bootstrap.min.css' />
            <link rel='stylesheet' href='../vendor/twbs/bootstrap-icons/font/bootstrap-icons.min.css' />
        </head>
        <body>
            <div class="alert alert-danger text-center" style="margin: 80px;"role="alert">
                <b> Sessione Scaduta! <br> Rieffettuare il <a href="../">LOGIN</a> </b>
            </div>
        </body>
    <?php
}