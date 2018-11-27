<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Classe per CRUD su tabelle generiche
 *
 * @author pietro
 */
class CRUD
{

    protected $tabella;
    protected $campochiave;
    protected $elencocampi;
    protected $con;
    protected $daticrud;

    function __construct($con)
    {
        print "f";
        $this->daticrud = $_SESSION['daticrud'];
        //$this->tabella=$daticrud['tabella'];
        // $this->campochiave=$daticrud['campochiave'];
        // $this->elencocampi=$daticrud['elencocampi'];
        $this->con = $con;
    }

    function visualizza()
    {
        print "GESTIONE TABELLA $this->daticrud['tabella']";




        $strcampi = implode(",", $this->daticrud['elencocampi']);
        $strcampiordinamento = implode(",", $this->daticrud['campiordinamento']);
        $query = "select " . $this->daticrud['campochiave'] . ", $strcampi from " . $this->daticrud['tabella'] . " where true and ".$this->daticrud['condizione']." order by $strcampiordinamento";
        $ris = mysqli_query($this->con, $query) or die("Errore " . $query . " ERR " . mysqli_error($this->con));
        print "<table align='center' border='1'><tr class='prima'>";
        foreach ($this->daticrud['elencocampi'] as $campo)
            print "<td><b>$campo</b></td>";
        print "<td>Azioni</td>";
        print "</tr>";
        while ($rec = mysqli_fetch_array($ris))
        {
            print "<tr>";
            foreach ($this->daticrud['elencocampi'] as $campo)
            {
                $chiaveesterna = "fk" . $campo;
                $strvis="";
                if (isset($this->daticrud[$chiaveesterna]))
                {
                    $queryfk = "select " . implode(",", $this->daticrud[$chiaveesterna][2]) . " from ".$this->daticrud[$chiaveesterna][0]." 
                            where ".$this->daticrud[$chiaveesterna][1]." = '" . $rec[$campo] . "'";

                    
                    $risfk = mysqli_query($this->con, $queryfk) or die("Errore $queryfk");
                    $recfk = mysqli_fetch_array($risfk);
                    
                    foreach($this->daticrud[$chiaveesterna][2] as $ce)
                       $strvis .= $recfk[$ce]." ";
                    
                } else
                    $strvis = $rec[$campo];
                print "<td>$strvis</td>";
            }

            print "<td>";
            print "<a href='modifica.php?tabella=" . $this->daticrud['tabella'] . "&id=" . $rec[$this->daticrud['campochiave']] . "'><img src='../immagini/modifica.png'></a>&nbsp;";
            print "<a href='elimina.php?tabella=" . $this->daticrud['tabella'] . "&id=" . $rec[$this->daticrud['campochiave']] . "'><img src='../immagini/delete.png'></a>";

            print "</td>";
            print "</tr>";
        }
        print "</table><br>";
    }

}
