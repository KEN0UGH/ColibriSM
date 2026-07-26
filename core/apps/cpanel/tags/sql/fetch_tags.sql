SELECT * FROM `<?php echo($data['t_htags']) ?>`

	WHERE 1 = 1

	<?php if($data['keyword']): ?>

		AND `tag` LIKE '%<?php echo($data["keyword"]) ?>%'

	<?php endif; ?>

	ORDER BY `id` <?php echo($data['order']) ?>

	<?php if($data['offset'] !== false && $data['offset'] !== ''): ?>

		LIMIT <?php echo($data['offset']) ?>, <?php echo($data['limit']) ?>

	<?php elseif($data['limit']): ?>

		LIMIT <?php echo($data['limit']) ?>

	<?php endif; ?>
