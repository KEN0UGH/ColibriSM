SELECT r.`id`, u.`avatar`, u.`username`, CONCAT(u.`fname`, " ", u.`lname`) as full_name, r.`currency`, r.`amount`, r.`time`

	FROM `<?php echo($data['t_reqs']) ?>` r

	INNER JOIN `<?php echo($data['t_users']); ?>` u ON r.`user_id` = u.`id`

	WHERE u.`active` IN ('1', '2')

	<?php if($data['offset']): ?>

		<?php if($data['offset_to'] == 'gt'): ?>

			AND r.`id` > <?php echo($data['offset']) ?>

		<?php else: ?>

			AND r.`id` < <?php echo($data['offset']) ?>

		<?php endif; ?>	

	<?php endif; ?>

	ORDER BY r.`id` <?php echo($data['order']) ?> 

<?php if($data['limit']): ?>	
	LIMIT <?php echo($data['limit']) ?>
<?php endif; ?>