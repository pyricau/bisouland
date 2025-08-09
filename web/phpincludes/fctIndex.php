<?php
	//Fonction pour calculer un temps en millisecondes.
	function microtime_float() {
		return array_sum(explode(' ', microtime()));
	}
	
	function calculterAmour($CalAmour,$timeDiff,$LvlCoeur,$nb1,$nb2,$nb3)
	{
		$CalAmour=calculerGenAmour($CalAmour,$timeDiff,$LvlCoeur,$nb1,$nb2,$nb3);
		//Cette fonction ajoute un frein sur le minima.
		if ($CalAmour<0) {$CalAmour=0;}
		
		return $CalAmour;
	}
	
	function calculerGenAmour($CalAmour,$timeDiff,$LvlCoeur,$nb1,$nb2,$nb3)
	{
		$diff=$LvlCoeur - (0.3*$nb1 + 0.7*$nb2 + $nb3);
		if ($diff>0)
		{
			//2 équations :  lvl 50 : 100 000 par heure et lvl 20  : 20000 par heure.
			$CalAmour += ((ExpoSeuil(5500, 6, $diff))*$timeDiff)/3600;
		}
		elseif($diff<0)
		{
			$CalAmour -= ((ExpoSeuil(5500, 6, -1*$diff))*$timeDiff)/3600;
		}
		return $CalAmour;
	}
	
	//Permet de convertir un timestamp en chaine sous la forme heure:minutes:secondes.
	function strTemps($s)
	{
    $m=0;
    $h=0;
    if($s<0)
    {
      return '0:00:00';
    }
    else
    {
      if($s>59)
      {
        $m=floor($s/60);
        $s=$s-$m*60;
      }
      if($m>59)
      {
        $h=floor($m/60);
        $m=$m-$h*60;
      }
      $ts=$s;
      $tm=$m;
      if($s<10)
      {
        $ts='0'.$s;
      }
      if($m<10)
      {
        $tm='0'.$m;
      }
      if ($h>24)
      {
        $d = floor($h/24);
        $h = $h-$d*24;
        $h = $d.' jours '.$h;
      }
      return	$h.' h '.$tm.' min '.$ts.' sec';
    }        
	}
	
	//Renvoi un s (ou^$lettre) si le nombre est plus grand que 1, renvoi '' (ou $alt) sinon.
	function pluriel($nombre, $lettre = 's', $alt='') {
        return ( $nombre > 1 ) ? $lettre : $alt;
	}
	
	function expo($a, $b, $val, $int=0)
	{
	
		$ret=$a*exp($b*$val);
	
		if ($int==1)
		{
			return ceil($ret);
		}
		else
		{
			return $ret;
		}
	
	}
	
	//Val doit être différent de 0.
	function InvExpo($a, $b, $val, $int=0)
	{
	//Patch to avoid division by 0...
	if ($val==0) $val=1;

		$ret=$a*exp($b/$val);
	
		if ($int==1)
		{
			return ceil($ret);
		}
		else
		{
			return $ret;
		}
	
	}
	
	//Plus a augmente, plus on augmente la valeur de seuil
	//Plus b augmente, plus on éloigne le moment ou on atteint le seuil .
	function ExpoSeuil($a, $b, $val, $int=0)
	{
	
		if ($val<=0) {$val=1;}
		$ret=$a*exp((-1*$b)/$val);
	
		if ($int==1)
		{
			return ceil($ret);
		}
		else
		{
			return $ret;
		}
	
	}
	
	function AdminMP($cible,$objet,$message,$lu=0)
	{
		$message = nl2br(addslashes($message));
		$objet=addslashes($objet);
		
		$sql = mysql_query("SELECT COUNT(*) AS nbmsg FROM messages WHERE destin=".$cible);
		if(mysql_result($sql,0,'nbmsg')>=20)
		{
			$Asuppr=mysql_result($sql,0,'nbmsg')-19;
			$date48=time()-172800;
			mysql_query("DELETE FROM messages WHERE destin=".$cible." AND timestamp<=$date48 ORDER BY id LIMIT $Asuppr");
		}
		
		mysql_query("INSERT INTO messages VALUES('', 1, '" .$cible. "', '" . $message . "', '" .time(). "', $lu, '" .$objet."')");
	}
	
	function SupprimerCompte($idCompteSuppr)
	{
		mysql_query("DELETE FROM membres WHERE id=$idCompteSuppr");
		mysql_query("DELETE FROM messages WHERE destin=$idCompteSuppr");
		mysql_query("DELETE FROM messages WHERE auteur=$idCompteSuppr");
		mysql_query("DELETE FROM evolution WHERE auteur=$idCompteSuppr");
		mysql_query("DELETE FROM ban WHERE auteur=$idCompteSuppr");
		mysql_query("DELETE FROM liste WHERE auteur=$idCompteSuppr");
		mysql_query("DELETE FROM logatt WHERE auteur=$idCompteSuppr");
		//Attaques à gerer.
		$sql_info = mysql_query("SELECT auteur FROM attaque WHERE cible=".$idCompteSuppr);
		while ($donnees_info = mysql_fetch_assoc($sql_info))
		{
			mysql_query("UPDATE membres SET bloque=0 WHERE id=".$donnees_info['auteur']);
			mysql_query("DELETE FROM attaque WHERE auteur=".$donnees_info['auteur']);
			AdminMP($donnees_info['auteur'],"Pas de chance","Ta cible vient de supprimer son compte.
			Une prochaine fois, peut-être...");
		}
		$sql_info = mysql_query("SELECT cible FROM attaque WHERE auteur=".$idCompteSuppr);
		if ($donnees_info = mysql_fetch_assoc($sql_info))
		{
			mysql_query("DELETE FROM attaque WHERE auteur=".$idCompteSuppr);
			AdminMP($donnees_info['cible'],"Veinard !!","Tu as vraiment de la chance !!
			Ton agresseur vient de supprimer son compte, tu peux donc dormir tranquille.");
		}

	}
	
	//Présuppose que toutes les vérifications ont été faites.
	function ChangerMotPasse($idChange,$newMdp)
	{
		$newMdp = md5($newMdp);
		mysql_query("UPDATE membres SET mdp='".$newMdp."' WHERE id='".$idChange."'");
	}
	
	//Présuppose que toutes les vérifications ont été faites.
	function AjouterScore($idScore,$valeur)
	{
		$sql_info = mysql_query("SELECT score FROM membres WHERE id=".$idScore);
		$donnees_info = mysql_fetch_assoc($sql_info);
		mysql_query("UPDATE membres SET score=".($donnees_info['score']+$valeur)." WHERE id=".$idScore);
	}
	
	//Présuppose que toutes les vérifications ont été faites.
	function ForcerAttaque($auteur,$cible,$duree,$pseudoAuteur,$nuageSource,$positionSource)
	{
		mysql_query("UPDATE membres SET bloque=1 WHERE id='".$auteur."'");
		mysql_query("INSERT INTO attaque VALUES (".$auteur.", ".$cible.", ".(time()+$duree).", ".(time()+2*$duree).", 0)");
					AdminMP($cible,"Alerte",$pseudo." vient d'envoyer ses bisous dans ta direction, et va tenter de t'embrasser.
					".$pseudo." est situ&eacute; sur le nuage ".$nuageSource.", à la position ".$positionSource.".
					Ses Bisous arrivent dans ".strTemps($duree).".");
					AdminMP($Auteur,"GoGoGo","T'as pas honte d'attaquer les gens comme ca ??");
	}

  function formaterNombre($nombre)
  {
    return number_format($nombre, 0, ',', ' ');
  }
	
	//Fonction modifiable à souhait, destinée à l'administrateur.
	function actionAdmin()
	{
		//AdminMP(12,"Test","Youuhouuu");
		//ChangerMotPasse(14,"elimaroyalispau");
		//ChangerMotPasse(47,"michael");
		//SupprimerCompte(71);
		//ForcerAttaque(47,13,10000,'kaelkael',4,7);
		//autoriserImage(12);
	}
	
	function distanceMax($coeur, $jambes)
	{
		return $coeur + 8*$jambes;
	}
	
	//Fonction qui retourne 0 si joueurAutre est même niveau, 1 s'il est intouchable parce que trop faible, 2 s'il est intouchable parce que trop fort.
	function voirNiveau($scoreJoueur,$scoreAutre)
	{
		if ($scoreJoueur<50)
		{
			return 2;
		}
		if ($scoreAutre<50)
		{
			return 1;
		}
		if ($scoreJoueur>2000 && $scoreAutre>2000)
		{
			return 0;
		}
		if (abs($scoreAutre-$scoreJoueur)<=200)
		{
			return 0;
		}
		if ($scoreJoueur-$scoreAutre>200)
		{
			return 1;
		}
		else
		{
			return 2;
		}
	}
	
			//transformation de bbcode smiley en images.
			function smileys($texte)
			{ 
				$in=array(
					"o_O",
					";)",
					":D",
					"^^",
					":o",
					":p",
					":colere:",
					":noel:",
					":)",
					":lol:",
					":-&deg;",
					":(",
					":euh:",
					":coeur:"
				);

				$out=array(
					'<img src="smileys/blink.gif" alt="un smiley" title="o_O"/>',
					'<img src="smileys/clin.png" alt="un smiley" title=";)"/>',
					'<img src="smileys/heureux.png" alt="un smiley" title=":D"/>',
					'<img src="smileys/hihi.png" alt="un smiley" title="^^"/>',
					'<img src="smileys/huh.png" alt="un smiley" title=":o"/>',
					'<img src="smileys/langue.png" alt="un smiley" title=":p"/>',
					'<img src="smileys/mechant.png" alt="un smiley" title=":colere:"/>',
					'<img src="smileys/noel.png" alt="un smiley" title=":noel:"/>',
					'<img src="smileys/smile.png" alt="un smiley" title=":)"/>',
					'<img src="smileys/rire.gif" alt="un smiley" title=":lol:"/>',
					'<img src="smileys/siffle.png" alt="un smiley" title=":-&deg;"/>',
					'<img src="smileys/triste.png" alt="un smiley" title=":("/>',
					'<img src="smileys/unsure.gif" alt="un smiley" title=":euh:"/>',
					'<img src="images/puce.png" alt="un smiley" title=":coeur:"/>'
				);

				return str_replace($in,$out,$texte);
			}

	function bbLow($text)
	{
		$bbcode = array(
                "[b]", "[/b]", 
                "[u]", "[/u]", 
                "[i]", "[/i]",
                );
		$htmlcode = array(
                "<strong>", "</strong>", 
                "<u>", "</u>", 
                "<em>", "</em>",
                );
				
		$text=stripslashes($text);
		

		$text = str_replace($bbcode, $htmlcode, $text);
		
		$text = preg_replace('!\[color=(red|green|blue|yellow|purple|olive|white|black)\](.+)\[/color\]!isU', '<span style="color:$1">$2</span>', $text);
		$text = preg_replace('!\[size=(xx-small|x-small|small|medium|large|x-large|xx-large)\](.+)\[/size\]!isU', '<span style="font-size:$1">$2</span>', $text);	

		$text=smileys($text);
	
		
		return $text;
	}
			
	function tempsAttaque($distance, $jambes)
	{
		return floor(($distance*1000)/(1 + 0.3*$jambes));
	}
	
	function coutAttaque($distance, $jambes)
	{
		$exp=$distance-$jambes;
		if ($exp<0){$exp==0;}
		return expo(100, 0.4, $exp, 1);
	}
	
	function autoriserImage($idAuteur)
	{
		mysql_query("INSERT INTO ban VALUES('', $idAuteur)");
		$sql = mysql_query("SELECT id FROM ban WHERE auteur=$idAuteur");
		$donnees = mysql_fetch_assoc($sql);
		return $donnees['id'];
	}
	function interdireImage($idAuteur)
	{
		mysql_query("DELETE FROM ban WHERE auteur=$idAuteur");
	}
	
	
?>
