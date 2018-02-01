<?php
    session_start();
    @require_once("../php-ini".$_SESSION['suffisso'].".php");
    @require_once("../lib/funzioni.php");
	require_once("pianifunz.php");
	$idcampoaula = $_POST['idaula'];
	$idcampodoc = $_POST['iddoc'];
	
	$idaula = substr($idcampoaula,2,(strlen($idcampoaula)-1));
	$iddoc = substr($idcampodoc,2,(strlen($idcampodoc)-1));
	//CONTROLLO NUMERO MASSIMO DOCENTI PER AULA
    $con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));
    $query = "SELECT capienza from tbl_aule where idaula=$idaula";
    $ris=mysqli_query($con,inspref($query));
    $rec=mysqli_fetch_array($ris);
    $capienza=$rec['capienza'];
	$qas = "SELECT * FROM tbl_assocauledoc WHERE idaula=$idaula";
	$ras = mysqli_query($con,inspref($qas)) or die("Errore nella connessione" - mysqli_error($ras) -$qas);
	if(mysqli_num_rows($ras)<$capienza)
	{
		$qas2 = "SELECT * FROM tbl_assocauledoc WHERE iddocente=$iddoc";
		$ras2 = mysqli_query($con,inspref($qas2)) or die("Errore nella connessione" - mysqli_error($ras2) - $qas2);
		if(mysqli_num_rows($ras2)==0)
		{
			$qasIN = "INSERT INTO tbl_assocauledoc
					(iddocente,idaula) VALUES ($iddoc,$idaula)";
			$rasIN = mysqli_query($con,inspref($qasIN)) or die("Errore nella connessione $qasIN");
		}
		else
		{
			$qasUP = "UPDATE tbl_assocauledoc
					  SET idaula=$idaula
					  WHERE iddocente=$iddoc";
			$rasUP = mysqli_query($con,inspref($qasUP)) or die("Errore nella connessione" - mysqli_error($rasUP) - $qasUP);
		}
	}
	stampaTabella($con);

