<table class="admintable" cellspacing=0>
<tr><th>ID: </th><th>Display Name: </th><th>Username: </th><th>Creation Date: </th></tr>
    <?php foreach($db['users'] as $user){ ?>

        <tr><td><?php echo $user['id']; ?></td><td><?php echo $user['displayname']; ?></td><td><?php echo $user['username']; ?></td><td><?php echo $user['date']; ?></td></tr>

    <?php } ?>
</table>