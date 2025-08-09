<?php
if ($_SESSION['logged'] == false)
{
$send=0;
$pseudo='';
$mdp='';
if (isset($_POST['inscription']))
{
	//Mesure de securite.
    $pseudo = htmlentities(addslashes($_POST['Ipseudo']));
    $mdp = htmlentities(addslashes($_POST['Imdp']));
	$mdp2 = htmlentities(addslashes($_POST['Imdp2']));
		//Prevoir empecher de prendre un pseudo deje existant
        //Si les variables contenant le pseudo, le mot de passe existent et contiennent quelque chose.
        if (isset($_POST['Ipseudo'], $_POST['Imdp'], $_POST['Imdp2']) && !empty($_POST['Ipseudo']) && !empty($_POST['Imdp']) && !empty($_POST['Imdp2']))
        {
			if ($mdp==$mdp2)
			{
            //Si le pseudo est superieur e 3 caracteres et inferieur e 35 caracteres.
			$taille = strlen(trim($_POST['Ipseudo']));
            if ( $taille >= 4 && $taille <= 15 )
            {
			
			    /* //Mesure de securite.
                $pseudo = htmlentities(addslashes($_POST['pseudo']));
                $mdp = htmlentities(addslashes($_POST['mdp']));*/
				
				//La requete qui compte le nombre de pseudos
				$sql = mysql_query("SELECT COUNT(*) AS nb_pseudo FROM membres WHERE pseudo='".$pseudo."'");
   
				//Verifie si le pseudo n'est pas deje pris.
				if (mysql_result($sql,0,'nb_pseudo') == 0 && $pseudo!="BisouLand")
				{
						//Verifie que le pseudo est correct.
						if (preg_match("!^\w+$!", $pseudo))
						{
						if (preg_match("!^\w+$!", $mdp))
						{
						
                        //Si le mot de passe est superieur e 4 caracteres.
						$taille = strlen(trim($_POST['Imdp']));
                        if ( $taille >= 5 && $taille <= 15 )
                        {
						
							//On execute la requete qui enregistre un nouveau membre.
							
							//Hashage du mot de passe avec md5().
                            $hmdp = md5($mdp);
							
                            mysql_query("INSERT INTO membres (id, pseudo, mdp, confirmation, lastconnect) VALUES ('', '".$pseudo."', '".$hmdp."', '1', ".time().")");

							echo 'Ton inscription est confirmée ! Tu peux maintenant te connecter.<br />';
							$send=1;
                        }
                        else
                        {
                                echo 'Erreur : le mot de passe est soit trop court, soit trop long !';
                        }
						}
						else
						{
							echo 'Erreur : le mot de passe n\'est pas valide !';
						}
						}
						else
                        {
                                echo 'Erreur : le pseudo n\'est pas valide !';
                        }
				}
				else
				{
					echo 'Erreur : pseudo deje pris !';
				}
			}
                else
                {
                        echo 'Erreur : le pseudo est soit trop court, soit trop long !';
                }
			}
			else
			{
				echo 'Erreur : Tu n\'as pas rentre deux fois le meme mot de passe !';
			}
			
        }
        else
        {
                echo 'Erreur : Pense e remplir tous les champs !';
        }
}
if ($send==0)
{
?>
<form method="post" class="formul" action="inscription.html">
	<label>Pseudo :<br /><span class="petit">(Entre 4 et 15 caracteres)</span><br /><input type="text" name="Ipseudo" tabindex="10" size="15" maxlength="15" value="<?php echo stripslashes($pseudo);?>"/></label><br />
	<label>Mot de passe : <br /><span class="petit">(Entre 5 et 15 caracteres)</span><br /><input type="password" name="Imdp" tabindex="20" size="15" maxlength="15" value=""/></label><br />
	<label>Reecris le mot de passe : <br /><input type="password" name="Imdp2" tabindex="30" size="15" maxlength="15" value=""/></label><br />
    <input type="submit" name="inscription" value="S'inscrire" />
</form>
<?php
}
}
else
{
	echo 'Pfiou t\'es dja connected toi !!';
}
?>
