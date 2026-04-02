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
	$swift_id       = fetch_or_get($_POST["swid"], false);
    $swift_user_id  = fetch_or_get($_POST["user_id"], false);

    if (not_empty($swift_id) && is_posnum($swift_user_id) && $swift_user_id != $me["id"]) {
        $swift_udata = cl_raw_user_data($swift_user_id);

        if (not_empty($swift_udata)) {
            $swift_data = cl_init_swift($swift_udata["swift"]);

            if (is_array($swift_data) && isset($swift_data[$swift_id])) {

                if (in_array($me["id"], $swift_data[$swift_id]["views"]) != true) {
                    $swift_data[$swift_id]["views"][$me["id"]] = time();

                    cl_update_user_data($swift_user_id, array(
                        "swift" => cl_minify_js(json($swift_data, true))
                    ));
                }
            }

            $data['code']    = 200;
            $data['message'] = "Swift view registered successfully";
            $data['data']    = array();
        }
        else {
        	$data         = array(
				'code'    => 400,
				'data'    => array(),
				'message' => 'User ID is not valid or missing'
			);
        }
    }
}