<?php
if ($_SESSION['logged'] == false)
{
$send=0;
$pseudo='';
$mdp='';
$email='';
if (isset($_POST['inscription']))
{
	//Mesure de sécurité.
    $pseudo = htmlentities(addslashes($_POST['Ipseudo']));
    $mdp = htmlentities(addslashes($_POST['Imdp']));
	$mdp2 = htmlentities(addslashes($_POST['Imdp2']));
    $email = htmlentities(addslashes($_POST['Iemail']));
		//Prévoir empecher de prendre un pseudo déjà existant
        //Si les variables contenant le pseudo, le mot de passe et l'email existent et contiennent quelque chose.
        if (isset($_POST['Ipseudo'], $_POST['Imdp'], $_POST['Imdp2'], $_POST['Iemail']) && !empty($_POST['Ipseudo']) && !empty($_POST['Imdp']) && !empty($_POST['Imdp2']) && !empty($_POST['Iemail']))
        {
			if ($mdp==$mdp2)
			{
            //Si le pseudo est supérieur à 3 caractères et inférieur à 35 caractères.
			$taille = strlen(trim($_POST['Ipseudo']));
            if ( $taille >= 4 && $taille <= 15 )
            {
			
			    /* //Mesure de sécurité.
                $pseudo = htmlentities(addslashes($_POST['pseudo']));
                $mdp = htmlentities(addslashes($_POST['mdp']));
                $email = htmlentities(addslashes($_POST['email']));*/
				
				//La requête qui compte le nombre de pseudos
				$sql = mysql_query("SELECT COUNT(*) AS nb_pseudo FROM membres WHERE pseudo='".$pseudo."'");
   
				//Vérifie si le pseudo n'est pas déjà pris.
				if (mysql_result($sql,0,'nb_pseudo') == 0 && $pseudo!="BisouLand")
				{
					//La requête qui compte le nombre de mail
					$sql = mysql_query("SELECT COUNT(*) AS nb_mail FROM membres WHERE email='".$email."'");
					if (mysql_result($sql,0,'nb_mail') == 0)
					{
						//Vérifie que le pseudo est correct.
						if (preg_match("!^\w+$!", $pseudo))
						{
						if (preg_match("!^\w+$!", $mdp))
						{
						
						//Vérifie que le mail est correct.
						if (preg_match("!^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]{2,}\.[a-zA-Z]{2,4}$!", $email))
						{
                        //Si le mot de passe est supérieur à 4 caractères.
						$taille = strlen(trim($_POST['Imdp']));
                        if ( $taille >= 5 && $taille <= 15 )
                        {
						
							//A ajouter
							$entete='From:'.EMAIL_EXPEDITOR;
							//On éxécute la requête qui enregistre un nouveau membre.
							
							//Hashage du mot de passe avec md5().
                            $hmdp = md5($mdp);
							
                            mysql_query("INSERT INTO membres (id, pseudo, mdp, confirmation, email, lastconnect) VALUES ('', '".$pseudo."', '".$hmdp."', '0', '".$email."', ".time().")");

							$sql_info = mysql_query("SELECT id FROM membres WHERE pseudo='".$pseudo."'");
							$donnees_info = mysql_fetch_array($sql_info);
							$id = $donnees_info['id'];
							
                            //Envoi du mail de confirmation.
                            $message ='Bonjour '.stripslashes($pseudo).' !!

	Bienvenue sur BisouLand !!
Pour valider ton inscription à BisouLand, clique sur le lien suivant :

http://bisouland.piwai.info/'.$id.'.confirmation.html

Je te rapelle les informations que tu nous as fourni :

Ton pseudo est : '.stripslashes($pseudo).'
Ton mot de passe est : '.$mdp.'

Tu peux te rendre sur BisouLand à l\'adresse suivante :

http://bisouland.piwai.info

A bientot sur BisouLand !!!

La BisouTeam.';
								


								//"Content-Type: text/html; charset=iso-8859-1\n";

                                //Si le mail a été envoyé on peut enregistrer le membre
                                if (mail($email, 'Confirmation de l\'inscription sur BisouLand !!', $message, $entete))
                                {

									echo 'Un mail de confirmation t\'as été envoyé.<br />';
									echo 'Tu pourras te connecter après avoir confirmé ton inscription.<br />';
									$send=1;

                                }
                                else
                                {
                                        echo 'Erreur : echec lors de l\'envoi du mail ! Tu dois ré-inscrire';
                                }
                        }
                        else
                        {
                                echo 'Erreur : le mot de passe est soit trop court, soit trop long !';
                        }
						}
						else
                        {
                                echo 'Erreur : l\'adresse email n\'est pas valide !';
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
						echo 'Erreur : email déjà enregistré !';
					}
				}
				else
				{
					echo 'Erreur : pseudo déjà pris !';
				}
			}
                else
                {
                        echo 'Erreur : le pseudo est soit trop court, soit trop long !';
                }
			}
			else
			{
				echo 'Erreur : Tu n\'as pas rentré deux fois le même mot de passe !';
			}
			
        }
        else
        {
                echo 'Erreur : Pense à remplir tous les champs !';
        }
}
if ($send==0)
{
?>
<form method="post" class="formul" action="inscription.html">
	<label>Pseudo :<br /><span class="petit">(Entre 4 et 15 caractères)</span><br /><input type="text" name="Ipseudo" tabindex="10" size="15" maxlength="15" value="<?php echo stripslashes($pseudo);?>"/></label><br />
	<label>Mot de passe : <br /><span class="petit">(Entre 5 et 15 caractères)</span><br /><input type="password" name="Imdp" tabindex="20" size="15" maxlength="15" value=""/></label><br />
	<label>Réécris le mot de passe : <br /><input type="password" name="Imdp2" tabindex="30" size="15" maxlength="15" value=""/></label><br />
	<label>Email : <br /><span class="petit">(Un mail d'activation du compte te sera envoyé)</span><br /><input type="text" name="Iemail" tabindex="40" size="30" value="<?php echo stripslashes($email);?>"/></label><br />
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