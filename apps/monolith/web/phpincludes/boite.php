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
if ($_SESSION['logged'] == true) {

    if (isset($_POST['supprimer'])) {
        $idmsg = htmlentities(addslashes($_POST['supprimer']));
        mysql_query("DELETE FROM messages WHERE id=".$idmsg." AND destin=".$id);
    } else {
        if (isset($_POST['supboite'])) {
            foreach ($_POST['supboite'] as $key=>$value) {
                $key=htmlentities(addslashes($key));
                mysql_query('DELETE FROM messages WHERE id='.$key." AND destin=".$id);
            }
        }
    }
    
    $sql = mysql_query("SELECT COUNT(*) AS nbmsg FROM messages WHERE destin=".$id);
    $nbmsg=mysql_result($sql, 0, 'nbmsg');
    if ($nbmsg>20) {
        $nbmsg=20;
    }
    
    $retour = mysql_query("SELECT id, posteur, timestamp, statut, titre FROM messages WHERE destin='".$id."' ORDER BY timestamp DESC LIMIT 20");

    ?>
<h1>Messages</h1>
<form name="main" method="post" action="boite.html" onSubmit="return verifselection()">
	<center>
		<h2>Vous avez <?php echo $nbmsg,'/20 message',pluriel($nbmsg);?></h2>
		
		<a href="envoi.html">Nouveau Message</a><br />
		<br />
		<table>
			<tr>
				<th style="width:5%;"><input type="checkbox" name="supboite[0]" title="Selectionner tous les messages" alt="Selectionner tous les messages" onclick="checkall()"/></th>
				<th style="width:5%;"><a class="bulle" style="cursor: default;" onclick="return false;" href=""><img src="images/newmess.png" alt="Messages non lus" title="" /><span>Messages non lus</span></a></th>
				<th style="width:20%;">Exp&eacute;diteur</th>
				<th style="width:35%;">Date</th>
				<th style="width:35%;">Objet</th>
			</tr>
<?php
        $i=0;
    while (($donnees = mysql_fetch_assoc($retour)) && $i<20) {
        $i++;
        //Suppression : bouton supprimer en bas, et checkbox //Ajouter bouton lu/non lu  //Max messages
        $retour2 = mysql_query("SELECT pseudo FROM membres WHERE id='".$donnees['posteur']."'");
        if (!$donnees2 = mysql_fetch_assoc($retour2)) {
            $donnees2['pseudo']="Supprim&eacute;";
        }
        ?>
			<tr>
				<td><input type="checkbox" name="supboite[<?php echo $donnees['id']; ?>]" onclick="checkone()" /></td>
				<td><?php if ($donnees['statut']==0) {
                    echo '<a class="bulle" style="cursor: default;" onclick="return false;" href=""><img src="images/newmess.png" alt="Message non lu" title="" /><span>Message non lu</span></a>';
                }?></td>
				<td> <?php echo  stripslashes($donnees2['pseudo']);?> </td>
				<td>le <?php echo date('d/m/Y à H\hi', $donnees['timestamp']);?></td>
				<td><a href="<?php echo $donnees['id'];?>.lire.html"><?php echo stripslashes($donnees['titre']);?></a></td>
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
    echo 'Tu n\'es pas connect&eacute; !!';
}
?>
