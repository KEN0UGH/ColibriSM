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

require_once(cl_full_path("core/apps/profile/app_ctrl.php"));

if ($action == 'load_more') {
	$data['err_code'] = 0;
    $data['status']   = 400;
    $offset           = fetch_or_get($_GET['offset'], 0);
    $prof_id          = fetch_or_get($_GET['prof_id'], 0);
    $type             = fetch_or_get($_GET['type'], false);
    $html_arr         = array();

    if (is_posnum($prof_id) && is_posnum($offset) && cl_can_view_profile($prof_id)) { 	
    	if (in_array($type, array('posts', 'media'))) {

            $media_type = (($type == 'media') ? true : false);
            $posts_ls   = cl_get_profile_posts($prof_id, 30, $media_type, $offset);

            if (not_empty($posts_ls)) {
                foreach ($posts_ls as $cl['li']) {
                    $html_arr[] = cl_template('timeline/post');
                }

                $data['status'] = 200;
                $data['html']   = implode("", $html_arr);
            }
        }
        else {
            if (cl_can_view_profile($prof_id)) {
                $posts_ls = cl_get_profile_likes($prof_id, 30, $offset);

                if (not_empty($posts_ls)) {
                    foreach ($posts_ls as $cl['li']) {
                        $html_arr[] = cl_template('timeline/post');
                    }

                    $data['status'] = 200;
                    $data['html']   = implode("", $html_arr);
                }
            }
        }
    }
}