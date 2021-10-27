<?php

session_start();
if (!isset($_SESSION['prefisso']))
{
    print "<br><br><b><big><center>Sessione scaduta!</center></big></b>";
    print "<br><b><big><center>Rieffettuare il <a href='../'>login</a>.</center></big></b>";
    die;
}