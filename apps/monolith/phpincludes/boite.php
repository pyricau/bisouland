<SCRIPT language="javascript" type="text/javascript">

//////////////////////////////////////////////////////////////////////////////////////
// function checkall()
// cette fonction s'execute lorsqu'on clique sur la checkbox principale
// elle passe en revue les checkbox et les coche si necessaire
// la checkbox d'indice 0 est la checkbox principale
// rem si ovus ajouter des element de formulaire, il faudra mofifier le script
// car l'instruction : temp = document.main.elements.length; comptabilise
// tous les elements et pas uniquement les checkbox...
//////////////////////////////////////////////////////////////////////////////////////
function checkall()
{
   // compte tous les éléments du formulaire en numérotant chronologiquement
  temp = document.main.elements.length;
  if (document.main.elements[0].checked)
  { // si la case est cochée
    for (i=1; i < temp; i++)
     { // on coche toutes les autres
          document.main.elements[i].checked=1;
      }
    }
    else
    {
       for (i=1; i < temp; i++)
      { // on décoche tout
          document.main.elements[i].checked=0;
      }
    }
 }
 //////////////////////////////////////////////////////////////////////////////////////
 // function checkone()
 // cette fonction s'execute lorsqu'on coche ou décoche une checkbox qcq
 // elle fait le compte des checkbox cochée pour savoir s'il faut décocher
 // ou cocher la checkbox principale...
 //////////////////////////////////////////////////////////////////////////////////////
 function checkone()
 {
    m=0; // initialisation du nombre de cases cochées
    temp = document.main.elements.length;
    for (i=1; i < temp; i++)
    { // on commence à 1 pour ne pas prendre en compte la checkbox principale
       if (document.main.elements[i].checked)
      { // si la checkbox courante est cochée, on comptabilise
      m++;
      }
   }
    if (document.main.elements[0].checked)
    { // si la checkbox principale est cochée, on la décoche
       document.main.elements[0].checked=0;
    }
    else
    { // dans le cas contraire, on vérifie que toutes les checkbox sont cochées
        if (m == (temp-1)) document.main.elements[0].checked=1;
   }
 }
 //////////////////////////////////////////////////////////////////////////////////////
 // function verifselection()
 // cette fonction s'execute qd on clique sur le bouton supprimer
 // elle vérifie que l'on a bien selectionné un objet au moins...
 //////////////////////////////////////////////////////////////////////////////////////
 function verifselection()
 {
    n=0;
    temp = document.main.elements.length;
    for (i=0; i< temp;i++)
    {
       if (document.main.elements[i].checked)
      {
      n=n+1;
      }
    }
    if (n != 0)
    {
       if (confirm("Êtes-vous sûr de vouloir supprimer ce(s) messages(s)?"))
       {
			return true;
       }
	   else
	   {
			return false;
	   }
    }
    else
   {
       alert("Veuillez sélectionner au moins un message !");
	  return false;
    }
 }

</SCRIPT>

<?php
if (true === $_SESSION['logged']) {
    $pdo = bd_connect();

    if (isset($_POST['supprimer'])) {
        $idmsg = htmlentities((string) $_POST['supprimer']);
        $stmt = $pdo->prepare('DELETE FROM messages WHERE id = :id AND destin = :destin');
        $stmt->execute(['id' => $idmsg, 'destin' => $id]);
    } elseif (isset($_POST['supboite'])) {
        foreach ($_POST['supboite'] as $key => $value) {
            $key = htmlentities((string) $key);
            $stmt = $pdo->prepare('DELETE FROM messages WHERE id = :id AND destin = :destin');
            $stmt->execute(['id' => $key, 'destin' => $id]);
        }
    }

    $stmt = $pdo->prepare('SELECT COUNT(*) AS nbmsg FROM messages WHERE destin = :destin');
    $stmt->execute(['destin' => $id]);
    $nbmsg = $stmt->fetchColumn();
    if ($nbmsg > 20) {
        $nbmsg = 20;
    }

    $stmt = $pdo->prepare('SELECT id, posteur, timestamp, statut, titre FROM messages WHERE destin = :destin ORDER BY timestamp DESC LIMIT 20');
    $stmt->execute(['destin' => $id]);

    ?>
<h1>Messages</h1>
<form name="main" method="post" action="boite.html" onSubmit="return verifselection()">
	<center>
		<h2>Vous avez <?php echo $nbmsg,'/20 message',pluriel($nbmsg); ?></h2>

		<table>
			<tr>
				<th style="width:5%;"><input type="checkbox" name="supboite[0]" title="Selectionner tous les messages" alt="Selectionner tous les messages" onclick="checkall()"/></th>
				<th style="width:5%;"><a class="bulle" style="cursor: default;" onclick="return false;" href=""><img src="images/newmess.png" alt="Messages non lus" title="" /><span>Messages non lus</span></a></th>
				<th style="width:20%;">Exp&eacute;diteur</th>
				<th style="width:35%;">Date</th>
				<th style="width:35%;">Objet</th>
			</tr>
<?php
        $i = 0;
    while (($donnees = $stmt->fetch()) && $i < 20) {
        ++$i;
        // Suppression : bouton supprimer en bas, et checkbox //Ajouter bouton lu/non lu  //Max messages
        $stmt2 = $pdo->prepare('SELECT pseudo FROM membres WHERE id = :id');
        $stmt2->execute(['id' => $donnees['posteur']]);
        if (!$donnees2 = $stmt2->fetch()) {
            $donnees2['pseudo'] = 'Supprim&eacute;';
        }
        ?>
			<tr>
				<td><input type="checkbox" name="supboite[<?php echo $donnees['id']; ?>]" onclick="checkone()" /></td>
				<td><?php if (false === $donnees['statut']) {
                    echo '<a class="bulle" style="cursor: default;" onclick="return false;" href=""><img src="images/newmess.png" alt="Message non lu" title="" /><span>Message non lu</span></a>';
                }?></td>
				<td> <?php echo stripslashes((string) $donnees2['pseudo']); ?> </td>
				<td>le <?php echo date('d/m/Y à H\hi', $donnees['timestamp']); ?></td>
				<td><a href="<?php echo $donnees['id']; ?>.lire.html"><?php echo stripslashes((string) $donnees['titre']); ?></a></td>
			</tr>
<?php
    }
    ?>
		</table>
		<input type="submit" tabindex="20" value="Supprimer" />
	<center>
</form>

<?php
} else {
    echo "Tu n'es pas connect&eacute; !!";
}
?>
