<?php

// Script de réduction du nombre de nuages.

// Décommenter la ligne ci dessous pour désactiver le script
/*

include 'phpincludes/fctIndex.php';
include 'phpincludes/bd.php';

$pdo = bd_connect();

$retour = $pdo->query("SELECT COUNT(*) AS nb FROM membres WHERE confirmation=1");
$nbMembres = $retour->fetchColumn();

$nbMembresParNuage = 4;

$espacement = 16 / $nbMembresParNuage;

$nbNuagesFinal = ceil($nbMembres/$nbMembresParNuage);

$sql = $pdo->query("SELECT id FROM membres WHERE confirmation=1");
$membre = 0;
$nuage = 1;
while($donnees = $sql->fetch())
{
  $membre++;
  if ($membre>$nbMembresParNuage)
  {
    $membre = 1;
    $nuage++;
  }

  $position = (($membre-1)*$espacement)+1;

  $stmt = $pdo->prepare("UPDATE membres SET nuage = :nuage, position = :position WHERE id = :id");
  $stmt->execute(['nuage' => $nuage, 'position' => $position, 'id' => $donnees['id']]);

}

$stmt = $pdo->prepare("UPDATE nuage SET nombre = :nombre WHERE id = 1");
$stmt->execute(['nombre' => $nbNuagesFinal]);
// */
