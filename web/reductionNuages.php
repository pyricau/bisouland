<?php

//Script de réduction du nombre de nuages.

//Décommenter la ligne ci dessous pour désactiver le script
/*

include 'phpincludes/fctIndex.php';
include 'phpincludes/bd.php';

bd_connect();

$retour = mysql_query("SELECT COUNT(*) AS nb FROM membres WHERE confirmation=1");
$nbMembres=mysql_result($retour,0,'nb');

$nbMembresParNuage = 4;

$espacement = 16 / $nbMembresParNuage;

$nbNuagesFinal = ceil($nbMembres/$nbMembresParNuage);

$sql= mysql_query("SELECT id FROM membres WHERE confirmation=1");
$membre = 0;
$nuage = 1;
while($donnees = mysql_fetch_assoc($sql))
{
  $membre++;
  if ($membre>$nbMembresParNuage)
  {
    $membre = 1;
    $nuage++;
  }

  $position = (($membre-1)*$espacement)+1;

  mysql_query("UPDATE membres SET nuage=$nuage, position=$position WHERE id=".$donnees['id']);

}

mysql_query("UPDATE nuage SET nombre=$nbNuagesFinal WHERE id=1");
// */
