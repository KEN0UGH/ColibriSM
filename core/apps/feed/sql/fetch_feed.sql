<?php
$twentyEightDaysAgo = strtotime('-28 days');
?>

SELECT * FROM `<?php echo($data['t_pubs']); ?>` 
WHERE `status` = "active"
  AND `target` = "publication"
  AND `admin_pinned` = "N"
  AND `priv_wcs` = "everyone"

ORDER BY 
    (CASE 
        WHEN `time` >= <?php echo $twentyEightDaysAgo; ?> THEN `likes_count` 
        ELSE 0 
     END) DESC,
    `time` DESC,
    `likes_count` DESC,
    `replys_count` DESC,
    `reposts_count` DESC

<?php if(is_posnum($data['limit'])): ?>
    LIMIT <?php echo($data['limit']); ?>
    <?php if(not_empty($data['offset'])): ?>
        OFFSET <?php echo($data['offset']); ?>
    <?php endif; ?>
<?php endif; ?>