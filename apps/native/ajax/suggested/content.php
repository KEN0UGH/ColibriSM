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

if ($action == 'load_more') {
	$data['err_code'] = "invalid_req_data";
    $data['status']   = 400;
    $offset           = fetch_or_get($_GET['offset'], 0);
    $users_list       = array();
    $html_arr         = array();

    if (is_posnum($offset)) {
    	
    	$users_list = cl_get_follow_suggestions(30, $offset);

    	if (not_empty($users_list)) {
			foreach ($users_list as $cl['li']) {
				$html_arr[] = cl_template('suggested/includes/list_item');
			}

			$data['status'] = 200;
			$data['html']   = implode("", $html_arr);
		}
    }
}