SELECT lm.`id` AS `offset_id`, u.* FROM `<?php echo($data['t_list_members']); ?>` lm

	INNER JOIN `<?php echo($data['t_users']); ?>` u ON lm.`member_id` = u.`id`

	WHERE lm.`list_id` = <?php echo($data['list_id']); ?>

	AND u.`active` = '1'

	<?php if(not_empty($data['offset'])): ?>
		AND lm.`id` < <?php echo($data['offset']); ?>
	<?php endif; ?>

	ORDER BY lm.`id` DESC

	<?php if(not_empty($data['limit'])): ?>
		LIMIT <?php echo($data['limit']); ?>
	<?php endif; ?>;