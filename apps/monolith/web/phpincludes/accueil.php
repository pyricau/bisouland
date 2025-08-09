<?php
if ($_SESSION['logged'] == true && $pseudo=='admin')
{
	echo '<div class="cache" ><a href="news/liste_news.php">Admin</a></div>';
}
?>
<h1>.:Accueil:.</h1>
<h2>Bonjour et bienvenue &agrave; tous !!</h2>
<p>
	BisouLand est un jeu multijoueurs sur internet.<br />
	Pour jouer, il suffit de disposer d'un simple navigateur internet.<br />
	Rejoins la communaut&eacute; !<br />
	Tu peux visiter la page d'aide pour apprendre les bases.<br />
	<br />
	Ce site est en perp&eacute;tuelle &eacute;volution, n'h&eacute;site donc pas &agrave; me signaler tout bug.<br />
	<span class="info">[ Ce site est optimis&eacute; pour <a href="http://www.mozilla-europe.org/fr/products/firefox/">Mozilla Firefox</a> ]</span><br />
	<br />
	Piwaï (admin)
</p>
<h1>.:Les News:.</h1>

<?php


			
	function bb2html($text)
	{
		$bbcode = array("<", ">",
                "[list]", "[*]","[/*]", "[/list]", 
                "[img]", "[/img]", 
                "[b]", "[/b]", 
                "[u]", "[/u]", 
                "[i]", "[/i]",
                '[url="', "[/url]",
                );
		$htmlcode = array("&lt;", "&gt;",
                "<ul>", "<li>","</li>", "</ul>", 
                "<img src=\"", "\">", 
                "<strong>", "</strong>", 
                "<u>", "</u>", 
                "<em>", "</em>",
                '<a href="', "</a>",
                );
				
		$text=htmlentities(stripslashes($text));
		

		$text = str_replace($bbcode, $htmlcode, $text);
		
		$text = preg_replace('!\[color=(red|green|blue|yellow|purple|olive|white|black)\](.+)\[/color\]!isU', '<span style="color:$1">$2</span>', $text);
		$text = preg_replace('!\[size=(xx-small|x-small|small|medium|large|x-large|xx-large)\](.+)\[/size\]!isU', '<span style="font-size:$1">$2</span>', $text);	

		
		$text = preg_replace('![^\"]http://[a-z0-9._/?&=-]+!i', '<a href="$0">$0</a>', $text);
		
		$text=smileys($text);
		
		$text=nl2br($text);
		
		return $text;
	}

$retour = mysql_query('SELECT * FROM newsbisous ORDER BY id DESC LIMIT 0, 5');

while ($donnees = mysql_fetch_array($retour))
{
?>

<div class="news">
    <h3>
        <?php echo stripslashes($donnees['titre']);?>
	</h3>
	<em>
        le <?php echo date('d/m/Y à H\hi', $donnees['timestamp']); ?></em>
		<?php if ($donnees['timestamp_modification']!=0) { ?>
		<br />
		<em>modifi&eacute;e le <?php echo date('d/m/Y à H\hi', $donnees['timestamp_modification']); ?></em>
		<?php }?>

		<p>
		<?php

			$contenu=bb2html($donnees['contenu']);
			echo $contenu;
			
		?>
		</p>
</div>
<?php
	}
?>

