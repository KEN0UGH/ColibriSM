SELECT posts.`id` as offset_id, posts.`publication_id`, posts.`type`, posts.`user_id` FROM `<?php echo($data['t_posts']); ?>` posts
	
	INNER JOIN `<?php echo($data['t_pubs']); ?>` pubs ON posts.`publication_id` = pubs.`id`

	WHERE pubs.`status` = 'active'

	AND pubs.`admin_pinned` = "N"

	AND posts.`user_id` IN (SELECT `member_id` FROM `<?php echo($data['t_list_members']); ?>` WHERE `list_id` = <?php echo($data['list_id']); ?>)

	AND posts.`publication_id` NOT IN (SELECT `post_id` FROM `<?php echo($data['t_reports']); ?>` WHERE `user_id` = <?php echo($data['user_id']); ?>)

	AND posts.`user_id` NOT IN (SELECT `profile_id` FROM `<?php echo($data['t_mutes']); ?>` WHERE `user_id` = <?php echo($data['user_id']); ?>)

	<?php if($data['offset']): ?>
		AND posts.`id` < <?php echo($data['offset']); ?>
	<?php endif; ?>

	ORDER BY posts.`id` DESC, pubs.`likes_count` DESC, pubs.`replys_count` DESC, pubs.`reposts_count` DESC

<?php if($data['limit']): ?>
	LIMIT <?php echo($data['limit']); ?>;
<?php endif; ?>