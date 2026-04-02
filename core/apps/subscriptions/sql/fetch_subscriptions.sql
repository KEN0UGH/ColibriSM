SELECT subs.`id`, subs.`subscription_start`, subs.`subscription_end`, subs.`subscriber_id`, subs.`creator_id`, users.`id` as user_id, users.`about`, users.`followers`, users.`following`, users.`posts`, users.`website`, users.`country_id`, users.`avatar`, users.`username`, users.`fname`, users.`lname`, users.`verified`, users.`last_active`, users.`is_online` FROM `<?php echo($data['t_subs']); ?>` subs

	INNER JOIN `<?php echo($data['t_users']); ?>` users ON subs.`creator_id` = users.`id`
	
	WHERE subs.`subscriber_id` = "<?php echo($data['user_id']); ?>"

ORDER BY subs.`id` DESC;