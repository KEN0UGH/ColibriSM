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
    $user_id   = fetch_or_get($_POST['user_id'], false); 
    $user_data = cl_raw_user_data($user_id);

    if (empty($user_data) || $user_id == $me['id']) {
        $data['code']    = 400;
        $data['message'] = "User id is missing or invalid";
        $data['data']    = array();
    }

    else {
        if (cl_is_blocked($me['id'], $user_id)) {
            $data['message'] = "User profile unblocked successfully";

            cl_db_delete_item(T_BLOCKS, array(
                'user_id'    => $me['id'],
                'profile_id' => $user_id
            ));
        }

        else{
            $data['message'] = "User profile blocked successfully";
            $insert_id       = cl_db_insert(T_BLOCKS, array(
                'user_id'    => $me['id'],
                'profile_id' => $user_id,
                'time'       => time()
            ));

            if (cl_is_following($me['id'], $user_id)) {
                cl_unfollow($me['id'], $user_id);
                cl_follow_decrease($me['id'], $user_id);
            }

            if (cl_is_following($user_id, $me['id'])) {
                cl_unfollow($user_id, $me['id']);
                cl_follow_decrease($user_id, $me['id']);
            }
        }

        $data['code'] = 200;       
        $data['data'] = array();
    }
}