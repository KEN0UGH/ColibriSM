<?php 
# @*************************************************************************@
# @ Software author: Mansur Terla (Mansur_TL)                               @
# @ UI/UX Designer & Web developer ;)                                       @
# @                                                                         @
# @*************************************************************************@
# @ Instagram: https://www.instagram.com/mansur_tl                          @
# @ VK: https://vk.com/mansur_tl_uiux                                       @
# @ Envato: http://codecanyon.net/user/mansur_tl                            @
# @ Behance: https://www.behance.net/mansur_tl                              @
# @ Telegram: https://t.me/mansurtl_contact                                 @
# @*************************************************************************@
# @ E-mail: mansurtl.contact@gmail.com                                      @
# @ Website: https://www.mansurtl.com                                       @
# @*************************************************************************@
# @ ColibriSM - The Ultimate Social Network PHP Script                      @
# @ Copyright (c)  ColibriSM. All rights reserved                           @
# @*************************************************************************@

$post_id   = fetch_or_get($_GET['post_id'], null);
$post_data = cl_raw_post_data($post_id);
$offset    = fetch_or_get($_GET['offset'], null);
$offset    = (is_posnum($offset)) ? $offset : null;
$limit     = fetch_or_get($_GET['page_size'], 15);
$limit     = (is_posnum($limit)) ? $limit: 15;

if (not_empty($post_data)) {
	$post_likes = cl_get_post_likes($post_id, $limit, $offset);

	if (not_empty($post_likes)) {
		$data['code']    = 200;
	    $data['message'] = "Likes fetched successfully";
		$data['data']    = $post_likes;
	}
	else {
		$data['code']    = 404;
	    $data['message'] = "No data found";
		$data['data']    = array();
	}
} 

else {
	$data['code']    = 400;
    $data['message'] = "Post id is missing or invalid";
	$data['data']    = array();
}