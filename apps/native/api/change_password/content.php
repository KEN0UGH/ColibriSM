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
	$valid_error       = false;
	$user_data_fileds  = array(
		'old_password' => fetch_or_get($_POST['old_password'], null),
        'new_password' => fetch_or_get($_POST['new_password'], null)
	);

	foreach ($user_data_fileds as $field_name => $field_val) {
		if ($field_name == 'old_password') {
			if (empty($field_val) || len_between($field_val, 6, 20) != true || password_verify($user_data_fileds["old_password"], $me["password"]) != true) {
	            $valid_error     = true;
	            $data['code']    = 402;
	        	$data['message'] = "Incorrect old password value";
	        	$data['data']    = array(); break;
	        }
		}

		else if ($field_name == 'new_password') {
			if (empty($field_val) || len_between($field_val, 6, 20) != true) {
				$valid_error     = true;
	            $data['code']    = 402;
	        	$data['message'] = "Incorrect new password value";
	        	$data['data']    = array(); break;
	        }
		}
	}

	if (empty($valid_error)) {
		$data['code']    = 200;
        $data['message'] = "Password changed";
        $data['data']    = array();

        cl_update_user_data($me["id"], array(
        	"password" => password_hash($user_data_fileds["new_password"], PASSWORD_DEFAULT)
        ));
	}
}