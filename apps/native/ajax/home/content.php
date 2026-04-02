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

if (empty($cl["is_logged"])) {
    $data['status'] = 400;
    $data['error']  = 'Invalid access token';
}

else if ($action == 'load_more') {

    require_once(cl_full_path("core/apps/home/app_ctrl.php"));

    $data['err_code'] = 0;
    $data['status']   = 400;
    $offset           = fetch_or_get($_GET['offset'], 0);
    $html_arr         = array();

    if (is_posnum($offset)) {    

        $posts_ls = cl_get_timeline_feed(30, $offset);

        if (not_empty($posts_ls)) {
            foreach ($posts_ls as $cl['li']) {
                $html_arr[] = cl_template('timeline/post');
            }

            $data['status'] = 200;
            $data['html']   = implode("", $html_arr);
        }
    }
}

else if ($action == 'update_timeline') {

    require_once(cl_full_path("core/apps/home/app_ctrl.php"));

    $data['err_code'] = 0;
    $data['status']   = 400;
    $onset            = fetch_or_get($_GET['onset'], 0);
    $html_arr         = array();

    if (is_posnum($onset)) {    

        $posts_ls = cl_get_timeline_feed(false, false, $onset);

        if (not_empty($posts_ls)) {
            foreach ($posts_ls as $cl['li']) {
                $html_arr[] = cl_template('timeline/post');
            }

            $data['status'] = 200;
            $data['html']   = implode("", $html_arr);
        }
    }
}