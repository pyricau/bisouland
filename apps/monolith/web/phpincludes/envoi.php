<?php
function Envoyer_Message($pseudoS, $pseudoC, $source, $cible, $titre, $message)
{
    //Pour tester AdminMP
    //AdminMP(12,"Message Admin","Un MP a ete envoye a ".$pseudoS." par ".$pseudoC.".");
    $timer=time();
    mysql_query("UPDATE membres SET lastmsg='".$timer."' WHERE id='".$source."'");
    
    $message = nl2br($message);
    $titre=addslashes($titre);
    
    $sql = mysql_query("SELECT COUNT(*) AS nbmsg FROM messages WHERE destin=".$cible);
    if (mysql_result($sql, 0, 'nbmsg')>=20 && $pseudoC!="admin") {
        $Asuppr=mysql_result($sql, 0, 'nbmsg')-19;
        $date48=time()-172800;
        mysql_query("DELETE FROM messages WHERE destin=".$cible." AND timestamp<=$date48 ORDER BY id LIMIT $Asuppr");
    }
    
    mysql_query("INSERT INTO messages VALUES('', '" .$source. "', '" .$cible. "', '" . $message . "', '" .$timer. "', '0', '" .$titre."')");
}
if ($_SESSION['logged'] == true) {
    $destinataire='';
    $message = 'Entrez ici votre message';
    $titre = 'Objet';
    
    $msgSend='';

    if (isset($_GET['destinataire']) && !empty($_GET['destinataire'])) {
        $destinataire = htmlentities($_GET['destinataire']);
    } elseif (isset($_POST['destinataire']) && isset($_POST['titre'])) {
        $destinataire = htmlentities($_POST['destinataire']);
        $titre = htmlentities($_POST['titre']);
        if (isset($_POST['message'])) {

            $message = htmlentities($_POST['message']);

            if (!empty($destinataire)) {
                if (!empty($titre)) {
                    if (!empty($message)) {
                        $retour = mysql_query("SELECT lastmsg FROM membres WHERE id='".$id."'");
                        $donnees = mysql_fetch_assoc($retour);
                        if (time()-$donnees['lastmsg'] > 30) {
                            $sql = mysql_query("SELECT COUNT(*) AS nb_pseudo FROM membres WHERE pseudo='".addslashes($destinataire)."' AND confirmation=1");
                            if (mysql_result($sql, 0, 'nb_pseudo') != 0) {
                                $retour = mysql_query("SELECT id FROM membres WHERE pseudo='".addslashes($destinataire)."'");
                                $donnees = mysql_fetch_assoc($retour);
                                $message = addslashes($message);
                                Envoyer_Message($pseudo, $destinataire, $id, $donnees['id'], $titre, $message);
                                $msgSend = 'Message bien envoy&eacute; a '.stripslashes($destinataire);
                                $message = 'Entrez ici votre message';
                                $titre = 'Objet';
                            } else {
                                $msgSend= 'Erreur, le destinataire n\'existe pas.';
                            }
                        } else {
                            $msgSend= 'Erreur, vous devez attendre 30 secondes entre 2 messages.';
                        }
                    } else {
                        $msgSend= 'Erreur, vous devez sp&eacute;cifier un message.';
                        $message = 'Entrez ici votre message';
                    }
                } else {
                    $msgSend= 'Erreur, vous devez sp&eacute;cifier un objet.';
                    $titre = 'Objet';
                }
            } else {
                $msgSend= 'Erreur, vous devez sp&eacute;cifier un destinataire.';
            }
        }
    }

    ?>
<script language="javascript" type="text/javascript" src="includes/prev.js"></script><!-- on appelle le fichier prev.js pour faire fonctionne la previsualisation -->
<script language="Javascript">

</script>

<h1>Envoyer un message priv&eacute;</h1>

<div class="formul">
<?php echo $msgSend.'<br />';?>
<form method="post" action="envoi.html" name="formulaire">
    <p>N'oubliez pas que vous etes sur BisouLand.<br />N'employez donc que du vocabulaire amoureux !!</p>
    
    <p>
		<label>Destinataire<br />
        <input name="destinataire" tabindex="10" value="<?php echo stripslashes($destinataire); ?>" OnFocus="this.value=''"/><br /></label>
		<label>Objet<br />
        <input name="titre" tabindex="10" value="<?php echo $titre; ?>" OnFocus="this.value=''"/><br /></label>
		
<input type="button" id="gras" name="gras" value="Gras" onClick="javascript:bbcode('[b]', '[/b]');return(false)" />
<input type="button" id="italic" name="italic" value="Italique" onClick="javascript:bbcode('[i]', '[/i]');return(false)" /><br />
<img src="smileys/blink.gif" title="o_O" alt="" onClick="javascript:smilies('o_O');return(false)" />
<img src="smileys/clin.png" title=";)" alt="" onClick="javascript:smilies(';)');return(false)" />
<img src="smileys/heureux.png" title=":D" alt="" onClick="javascript:smilies(':D');return(false)" />
<img src="smileys/hihi.png" title="^^" alt="" onClick="javascript:smilies('^^');return(false)" />
<img src="smileys/huh.png" title=":o" alt="" onClick="javascript:smilies(':o');return(false)" />
<img src="smileys/langue.png" title=":p" alt="" onClick="javascript:smilies(':p');return(false)" />
<img src="smileys/mechant.png" title=":colere:" alt="" onClick="javascript:smilies(':colere:');return(false)" />
<img src="smileys/noel.png" title=":noel:" alt="" onClick="javascript:smilies(':noel:');return(false)" />
<img src="smileys/rire.gif" title=":lol:" alt="" onClick="javascript:smilies(':lol:');return(false)" />
<img src="smileys/siffle.png" title=":-o" alt="" onClick="javascript:smilies(':-o');return(false)" />
<img src="smileys/smile.png" title=":)" alt="" onClick="javascript:smilies(':)');return(false)" />
<img src="smileys/triste.png" title=":(" alt="" onClick="javascript:smilies(':(');return(false)" />
<img src="smileys/unsure.gif" title=":euh:" alt="" onClick="javascript:smilies(':euh:');return(false)" />
<img src="images/puce.png" title=":coeur:" alt="" onClick="javascript:smilies(':coeur:');return(false)" />

<br />		
        <label>Message :<br />
        <textarea id="textarea" name="message" tabindex="20" rows="8" cols="35"><?php echo stripslashes($message); ?></textarea> <br /></label>

<input type="button" value="Pr&eacute;visualiser" onClick="previsualisation();return(false)" /><!-- ce bouton va permettre aux utilisateurs de d'avoir un apercu quand ils le veulent, pas en direct -->
<input type="submit" tabindex="30" value="Envoyer" />
<div class="message" id="prev"></div>
		
        
    </p>
</form>
</div>

<?php
} else {
    echo 'Tu n\'es pas connect&eacute; !!';
}
?>
