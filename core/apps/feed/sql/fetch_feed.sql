SELECT * FROM `<?php echo($data['t_pubs']); ?>` 

	WHERE `status` = "active"

	AND `target` = "publication"

	AND `admin_pinned` = "N"

	AND `priv_wcs` = "everyone"

	ORDER BY `time` DESC, `likes_count` DESC, `replys_count` DESC, `reposts_count` DESC

<?php if(is_posnum($data['limit'])): ?>
	LIMIT <?php echo($data['limit']); ?>

	<?php if(not_empty($data['offset'])): ?>
		OFFSET <?php echo($data['offset']); ?>
	<?php endif; ?>

<?php endif; ?>

