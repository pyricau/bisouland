<?php

function GiveNewPosition($idJoueur)
{
	$sql_info = mysql_query("SELECT nombre FROM nuage WHERE id=1");
	$donnees_info = mysql_fetch_assoc($sql_info);
	$NbNuages = $donnees_info['nombre'];
	
	$sql = mysql_query("SELECT COUNT(*) AS nb_pos FROM membres WHERE nuage=$NbNuages");

	$nbPos=mysql_result($sql,0,'nb_pos');
	
	//Neuf personnes par nuage max, lors de l'attribution.
	if ( $nbPos > 8)
	{
		$NbNuages++;
		mysql_query("UPDATE nuage SET nombre=$NbNuages WHERE id=1");
		$nbPos=0;
	}
	
	if ($nbPos > 0)
	{
	
		$OccPos = array();
	
		$sql_info = mysql_query("SELECT position FROM membres WHERE nuage=$NbNuages");
		$i=0;
		//On r�cup�re les positions occup�es.
		while ($donnees_info = mysql_fetch_assoc($sql_info))
		{
			$OccPos[$i]=$donnees_info['position'];
			$i++;
		}

		$FreePos = array();
		
		$nbLibre=16-$nbPos;
		
		$j=0;
		
		//Rempli FreePos avec les positions libres
		for ($i=1;$i<=16;$i++)
		{
			if (!in_array($i, $OccPos))
			{
				$FreePos[$j]=$i;
				$j++;
			}
		}
		
		//On choisi une valeur au hasard.
		
		$FinalPos=$FreePos[mt_rand(0,($nbLibre-1))];
		
	}
	else
	{
		$FinalPos=mt_rand(1,16);
	}
	//On enregistre.
	mysql_query("UPDATE membres SET nuage=$NbNuages, position=$FinalPos WHERE id=$idJoueur");
	
}

if ($_SESSION['logged'] == false)
{
if (isset($_GET['id']) && !empty($_GET['id']))
{
   //Mesure de s�curit�, notamment pour �viter les injections sql.
   $id = htmlentities(addslashes($_GET['id']));
   
   //La requ�te qui compte le nombre de pseudos
   $sql = mysql_query("SELECT COUNT(*) AS nb_id FROM membres WHERE id='".$id."'");
   
   if (mysql_result($sql,0,'nb_id') != 0)
   {
      //On cherche la valeur du champ confirmation.
      $sql_info = mysql_query("SELECT confirmation FROM membres WHERE id='".$id."'");
      $donnees_info = mysql_fetch_assoc($sql_info);
     
      //Si la valeur est �gal � 0.
      if ($donnees_info['confirmation'] == 0)
      {
         //Requ�te sql modifiant la valeur du champ confirmation.
         mysql_query("UPDATE membres SET confirmation='1' WHERE id='".$id."'");
		 
		 //Pour connaitre le pseudo
		 $sql_info = mysql_query("SELECT pseudo FROM membres WHERE id='".$id."'");
		 $donnees_info = mysql_fetch_assoc($sql_info);
         $pseudo = stripslashes($donnees_info['pseudo']);
		 
		mysql_query("UPDATE membres SET timestamp='".time()."' WHERE id='".$id."'");
		mysql_query("UPDATE membres SET amour='300' WHERE id='".$id."'");
		 
		GiveNewPosition($id);
		
		AdminMP($id,"Bienvenue sur BisouLand","Merci pour l'int�r�t que tu portes � BisouLand.
		Il est probable que certaines choses te paraissent obscures pour le moment.
		Pense � faire un tour sur la page Aide, puis sur la page Encyclop�die, pour d�couvrir comment fonctionne BisouLand.
		En haut � droite se trouve le menu de jeu, c'est ici que tu pourras g�rer ton compte BisouLand.
		Si tu as des questions, n'h�site pas � employer le tchat, le forum ou envoyer un message priv� � l'admin.
		
		Amicalement, et avec plein de Bisous
		L'�quipe BisouLand
		");		
		 
        //Le petit message.
        echo '<p>Le compte ayant pour pseudo <strong>'.$pseudo.'</strong> a bien �t� valid� !<br />Vous pouvez maintenant vous connecter</p>';
      }
      else
      {
         echo 'Erreur : ce compte est d�j� confirm� !';
      }
   }
   else
   {
      echo 'Erreur : le compte n\'existe pas !';
   }

}
else
{
  echo 'Erreur : aucun compte n\'est indiqu� !';
}
}
else
{
echo 'Erreur : Vous devez vous d�connecter avant de confirmer un compte. !';
}
?>