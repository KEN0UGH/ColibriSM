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

if (empty($cl['is_logged'])) {
	$data         = array(
		'code'    => 401,
		'data'    => array(),
		'message' => 'Unauthorized Access'
	);
}

else {
	require_once(cl_full_path("core/apps/thread/app_ctrl.php"));

	$offset    = fetch_or_get($_GET['offset'], null);
	$limit     = fetch_or_get($_GET['page_size'], 15);
    $thread_id = fetch_or_get($_GET['thread_id'], null);

    if (is_posnum($offset) && is_posnum($thread_id) && is_numeric($limit)) {
    	$replys_list = cl_get_thread_child_posts($thread_id, $limit, $offset, 'lt');

    	if (not_empty($replys_list)) {
    		$data["message"] = "Replies fetched successfully";
    		$data["code"]    = 200;
    		$data["data"]    = $replys_list;
    	}
    	else {
    		$data["message"] = "No data found";
    		$data["code"]    = 404;
    		$data["data"]    = array();
    	}
    }

    else {
    	$data['code']    = 400;
        $data['message'] = "The paging offset ID is missing or invalid. Please check your details";
    	$data['data']    = array();
    }
}