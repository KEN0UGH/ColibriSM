SELECT subs.`id`, subs.`subscription_start`, subs.`subscription_end`, subs.`subscriber_id`, subs.`creator_id`, users.`id` as user_id, users.`about`, users.`followers`, users.`following`, users.`posts`, users.`website`, users.`country_id`, users.`avatar`, users.`username`, users.`fname`, users.`lname`, users.`verified`, users.`is_online` FROM `<?php echo($data['t_subs']); ?>` subs

	INNER JOIN `<?php echo($data['t_users']); ?>` users ON subs.`subscriber_id` = users.`id`
	
	WHERE subs.`creator_id` = "<?php echo($data['user_id']); ?>"

	<?php if($data['type'] == "active"): ?>
		AND subs.`subscription_end` > <?php echo($data['time']); ?>
	<?php else: ?>
		AND subs.`subscription_end` < <?php echo($data['time']); ?>
	<?php endif; ?>

	<?php if(not_empty($data['offset'])): ?>
		AND subs.`id` < <?php echo($data['offset']); ?>
	<?php endif; ?>

ORDER BY subs.`id` DESC

LIMIT 30;