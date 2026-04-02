SELECT * FROM `<?php echo($data['t_pubs']) ?>`
	
	WHERE `status` = 'active'

	<?php if($data['offset']): ?>

		<?php if($data['offset_to'] == 'gt'): ?>

			AND `id` > <?php echo($data['offset']) ?>

		<?php else: ?>

			AND `id` < <?php echo($data['offset']) ?>

		<?php endif; ?>	

	<?php endif; ?>

	<?php if($data['keyword']): ?>

		AND `text` LIKE '%<?php echo($data["keyword"]) ?>%'

	<?php endif; ?>

	ORDER BY `id` <?php echo($data['order']) ?> 

<?php if($data['limit']): ?>

	LIMIT <?php echo($data['limit']) ?>
	
<?php endif; ?>