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
       if (confirm("Êtes-vous sûr de vouloir supprimer cette/ces notification(s)?"))
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
       alert("Veuillez sélectionner au moins une notification !");
	  return false;
    }
 }

</SCRIPT>

<?php
if (true === $blContext['is_signed_in']) {
    $pdo = bd_connect();
    $castToUnixTimestamp = cast_to_unix_timestamp();

    if (isset($_POST['supprimer'])) {
        $stmt = $pdo->prepare(<<<'SQL'
            DELETE FROM notifications
            WHERE (
                notification_id = :notification_id
                AND account_id = :current_account_id
            )
        SQL);
        $stmt->execute([
            'notification_id' => $_POST['supprimer'],
            'current_account_id' => $blContext['account']['id'],
        ]);
    } elseif (isset($_POST['supboite']) && [] !== $_POST['supboite']) {
        $notificationIds = array_map('strval', array_keys($_POST['supboite']));

        $inSize = count($notificationIds);
        $inValues = implode(', ', array_fill(0, $inSize, '?'));

        $notificationIdIn = "notification_id IN ({$inValues})";
        $stmt = $pdo->prepare(<<<SQL
            DELETE FROM notifications
            WHERE (
                {$notificationIdIn}
                AND account_id = ?
            )
        SQL);
        $stmt->execute([
            ...$notificationIds,
            $blContext['account']['id'],
        ]);
    }

    $stmt = $pdo->prepare(<<<'SQL'
        SELECT
            notification_id,
            title,
            received_at,
            has_been_read
        FROM notifications
        WHERE account_id = :current_account_id
        ORDER BY notification_id DESC
        LIMIT 20
    SQL);
    $stmt->execute([
        'current_account_id' => $blContext['account']['id'],
    ]);
    /**
     * @var array<int, array{
     *      notification_id: string, // UUID
     *      title: string,
     *      received_at: string, // ISO 8601 timestamp string
     *      has_been_read: bool,
     * }> $notifications
     */
    $notifications = $stmt->fetchAll();
    $totalNotifications = count($notifications);
    ?>
<h1>Notifications</h1>
<form name="main" method="post" action="boite.html" onSubmit="return verifselection()">
	<center>
        <h2>Vous avez <?php echo $totalNotifications; ?>/20 notifications</h2>

		<table>
			<tr>
				<th style="width:5%;"><input type="checkbox" name="supboite[0]" title="Selectionner toutes les notifications" alt="Selectionner toutes les notifications" onclick="checkall()"/></th>
				<th style="width:5%;"><a class="bulle" style="cursor: default;" onclick="return false;" href=""><img src="images/newmess.png" alt="Notifications non lues" title="" /><span>Notifications non lues</span></a></th>
				<th style="width:45%;">Notification</th>
				<th style="width:45%;">Date</th>
			</tr>
            <?php foreach ($notifications as $notification) { ?>
                <tr>
                    <td>
                        <input type="checkbox" name="supboite[<?php echo $notification['notification_id']; ?>]" onclick="checkone()" />
                    </td>
                    <td>
                        <?php if (false === $notification['has_been_read']) { ?>
                        <a class="bulle" style="cursor: default;" onclick="return false;" href=""><img src="images/newmess.png" alt="Notification non lue" title="" /><span>Notification non lue</span></a>
                        <?php } ?>
                    </td>
                    <td>
                        <a href="<?php echo $notification['notification_id']; ?>.lire.html"><?php echo htmlspecialchars($notification['title']); ?></a>
                    </td>
                    <td>
                        le <?php echo date('d/m/Y à H\hi', $castToUnixTimestamp->fromPgTimestamptz($notification['received_at'])); ?>
                    </td>
                </tr>
            <?php } ?>
		</table>
		<input type="submit" tabindex="20" value="Supprimer" />
	<center>
</form>

<?php } else { ?>
    Tu n'es pas connect&eacute; !!
<?php } ?>
