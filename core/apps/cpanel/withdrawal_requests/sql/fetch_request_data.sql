SELECT r.`id`, u.`email`, u.`avatar`, u.`username`, CONCAT(u.`fname`, " ", u.`lname`) as full_name, r.`time`, r.`requisites`, r.`method`, r.`amount`

	FROM `<?php echo($data['t_reqs']) ?>` r

	INNER JOIN `<?php echo($data['t_users']); ?>` u ON r.`user_id` = u.`id`

	WHERE r.`id` = <?php echo($data['req_id']); ?>

LIMIT 1;