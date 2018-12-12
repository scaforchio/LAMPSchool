<?php


function stampaTabella($con)
{

//TABELLA DOCENTI
    print "
	<div id=principale style='max-height: 500px'>	<p align=center><i>Trascinare i docenti nell'aula desiderata (la capienza è mostrata affianco al nome);<br>&egrave; possibile
	spostare i docenti anche da un'aula ad un'altra</i></p>
	<table width='100%' height='500' >
	<!-- TABELLA DOCENTI -->
	<tr>
		<td valign=top>
        <div valign='top' align=left id=docenti style='height:400px; overflow-y: scroll; border:1px solid black;'>
		<table align='left' height='100%' id='docenti' border='1'>";

    $qd = "SELECT cognome, nome, iddocente as iddoc FROM tbl_docenti
           where iddocente >1000000000
           order by cognome, nome";
    $rd = eseguiQuery($con,$qd);
    while ($dd = mysqli_fetch_array($rd))
    {
        $nome = $dd['nome'];
        $cognome = $dd['cognome'];
        $iddoc = $dd['iddoc'];
        $idcampodoc = "d_" . $iddoc;

        $qas = "SELECT * FROM tbl_assocauledoc WHERE iddocente=$iddoc";
        $ras = eseguiQuery($con,$qas);
        print "<tr>";
        if (mysqli_num_rows($ras) == 0)
        {
            print "<th
							ondragstart=startDrag(event)
							draggable=true
							height=20
							id=$idcampodoc
					   >$cognome $nome</th>";
        }
        // else
        // {
        //     print "<th DISABLED height=20><font color=red>$cognome $nome</font></th>";
        // }
        print "</tr>";
    }
    print "
		</table>
		</div>
		</td>";
//CASELLA ELIMINAZIONE
    print "<td valign=top align=center>
		<img id=cestino
		     src='./chiuso.jpg'
		     draggable=false
		     ondragenter=inDrop(event,'cestino')
			 ondragover=overDrop(event)
			 ondrop=eliminaDoc(event)
		>
	   </td><td valign='top'>";
//TABELLA AULE
    print "<!-- TABELLA AULE -->
        <div valign='top' align=right id=aule style='height:400px; overflow-y: scroll; border:1px solid black;'>
		<table align='right' id='aule' border=1 height=100%>";
    $qa = "SELECT * FROM tbl_aule WHERE capienza>0 ORDER BY denominazione ASC";
    $ra = eseguiQuery($con,$qa);

    while ($da = mysqli_fetch_array($ra))
    {
        $den = $da['denominazione'];
        $capienza = $da['capienza'];
        $idaula = $da['idaula'];
        $idcampoaula = "a_" . $idaula;
        print "<tr>";
        print "<th bgcolor=#FF0000>$den ($capienza)</th>";
        print "</tr><tr>";
        print "<td valign=top
                   id=$idcampoaula
					   height=20
					   width=200
					   ondragenter=inDrop(event,'$idcampoaula')
					   ondragover=overDrop(event)
					   ondrop=startDrop(event,'$idcampoaula')>";
        $qas = "SELECT * FROM tbl_assocauledoc WHERE idaula=$idaula";
        $ras = eseguiQuery($con,$qas);
        while ($das = mysqli_fetch_array($ras))
        {
            $iddocente = $das['iddocente'];
            $idcampodoc = "d_" . $iddocente;
            $qd = "SELECT * FROM tbl_docenti WHERE iddocente = $iddocente";
            $rd = eseguiQuery($con,$qd);
            $dd = mysqli_fetch_array($rd);
            print "<hr><b><p align=center id=$idcampodoc
						  draggable=true
						  ondragstart=startDrag(event)
						  style='height:20px;'
						  >";
            print $dd['cognome'] . " " . $dd['nome'];
            print "</p></b><hr>";
        }
        print "</td>";
        print "</tr>";
    }
    print "</table></div></td></tr>";
    print "</table></div>";
}
