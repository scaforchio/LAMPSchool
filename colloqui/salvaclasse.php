<?php

session_start();

@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione

if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$idgiornata = $_GET['idgiornata'];
$idclassi = $_GET['idclassi'];

//print $idgiornata;

//print "jcids $idgiornata";
//foreach ($_GET['idclassi'] as $id)
//  print "$id <br>";

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));

$query = "SELECT * FROM tbl_colloquiclasse WHERE idgiornatacolloqui=$idgiornata";
$result = eseguiQuery($con, $query);

if (mysqli_num_rows($result)!=0)
{
  while ($row=mysqli_fetch_array($result))
  {
    $idclassecorrente = $row['idclasse'];

    if (!in_array($idclassecorrente, $idclassi))
    {
      $query = "DELETE FROM tbl_colloquiclasse WHERE idclasse=$idclassecorrente AND idgiornatacolloqui=$idgiornata";
      eseguiQuery($con, $query);
    }
  }
}

for ($i=0; $i<count($idclassi); $i++)
{
  $querycontrollo = "SELECT * FROM tbl_colloquiclasse WHERE idclasse=$idclassi[$i] AND idgiornatacolloqui=$idgiornata";
  $resultcontrollo = eseguiQuery($con, $querycontrollo);

  if (mysqli_num_rows($resultcontrollo)==0)
  {
    $query = "INSERT INTO tbl_colloquiclasse (idgiornatacolloqui, idclasse)
              VALUES ('$idgiornata', '$idclassi[$i]')";
    eseguiQuery($con, $query);
  }
}

header("location: ./insgiornatecoll.php");

mysqli_close($con);
