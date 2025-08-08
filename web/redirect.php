<?php
	session_start();
	include 'phpincludes/bd.php';
	bd_connect();

if (isset($_POST['connexion']))
{

//Ensuite on v�rifie que les variables existent et contiennent quelque chose :)
if (isset($_POST['pseudo'], $_POST['mdp']) && !empty($_POST['pseudo']) && !empty($_POST['mdp']))
{
   //Mesure de s�curit�, notamment pour �viter les injections sql.
   //Le htmlentities �vitera de le passer par la suite.
   $pseudo = htmlentities(addslashes($_POST['pseudo']));
   $mdp = htmlentities(addslashes($_POST['mdp']));
   //Hashage du mot de passe.
   $mdp = md5($mdp);

   
   //La requ�te qui compte le nombre de pseudos
   $sql = mysql_query("SELECT COUNT(*) AS nb_pseudo FROM membres WHERE pseudo='".$pseudo."'");
   
   //La on v�rifie si le nombre est diff�rent que z�ro
   if (mysql_result($sql,0,'nb_pseudo') != 0)
   {
      //S�lection des informations.
      $sql_info = mysql_query("SELECT id, confirmation, mdp, nuage FROM membres WHERE pseudo='".$pseudo."'");
      $donnees_info = mysql_fetch_array($sql_info);

      //Si le mot de passe est le m�me.
      if ($donnees_info['mdp'] == $mdp)
      {
         //Si le compte est confirm�.
         if ($donnees_info['confirmation'] == 1)
         {
            //On modifie la variable qui nous indique que le membre est connect�.
            $_SESSION['logged'] = true;
           
            //On cr�� les variables contenant des informations sur le membre.
            $_SESSION['id'] = $donnees_info['id'];
            $_SESSION['pseudo'] = $pseudo;
			$_SESSION['nuage'] = $donnees_info['nuage'];
			
			if (isset($_POST['auto']))
			{
				$timestamp_expire = time() + 30*24*3600;
				setcookie('pseudo', $pseudo, $timestamp_expire);
				setcookie('mdp', $mdp, $timestamp_expire);
			}
			
			//On supprime le membre non connect� du nombre de visiteurs :
			mysql_query("DELETE FROM connectbisous WHERE ip='".$_SERVER['REMOTE_ADDR']."'");
			
			//On redirige le membre.
            header("location: cerveau.html");
			
         }
         else
         {
            $_SESSION['errCon']='Erreur : le compte n\'est pas confirm� !';
			$_SESSION['logged'] = false;
			header("location: connexion.html");
         }
      }
      else
      {
         $_SESSION['errCon']= 'Erreur : le mot de passe est incorrect !';
		 $_SESSION['logged'] = false;
		 header("location: connexion.html");
      }
   }
   else
   {
      $_SESSION['errCon']= 'Erreur : le pseudo n\'existe pas !';
	  $_SESSION['logged'] = false;
	  header("location: connexion.html");
   }

}
else
{
   $_SESSION['errCon']= 'Erreur : vous avez oubli� de remplir un ou plusieurs champs !';
   $_SESSION['logged'] = false;
   header("location: connexion.html");
}
}
else
{
   $_SESSION['errCon']= 'Erreur : Vous n\'avez pas acces � cette page !';
   $_SESSION['logged'] = false;
   header("location: connexion.html");
}
?>
