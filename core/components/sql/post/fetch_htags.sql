<?php
$twentyEightDaysAgo = strtotime('-28 days');
?>

SELECT * FROM `<?php echo($data['t_htags']); ?>`
WHERE `posts` > 0
  AND `time` >= <?php echo $twentyEightDaysAgo; ?>

ORDER BY RAND()
<?php if($data['offset']): ?>
LIMIT <?php echo($data['limit']); ?> OFFSET <?php echo($data['offset']); ?>;
<?php else: ?>
LIMIT <?php echo($data['limit']); ?>;
<?php endif; ?>