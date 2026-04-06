SELECT pubs.`id` as offset_id, pubs.`id` as publication_id, pubs.`type`, pubs.`user_id`
FROM `<?php echo($data['t_pubs']); ?>` pubs

WHERE pubs.`user_id` = <?php echo($data['user_id']); ?>

AND pubs.`target` = 'pub_reply'

AND pubs.`profile_pinned` = "N"

<?php if($data['offset']): ?>
    AND pubs.`id` < <?php echo($data['offset']); ?>
<?php endif; ?>

ORDER BY pubs.`id` DESC, pubs.`likes_count` DESC, pubs.`replys_count` DESC, pubs.`reposts_count` DESC 

<?php if($data['limit']): ?>
    LIMIT <?php echo($data['limit']); ?>;
<?php endif; ?>