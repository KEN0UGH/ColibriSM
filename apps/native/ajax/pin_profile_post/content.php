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
    $data['status'] = 400;
    $data['error']  = 'Invalid access token';
}

else {
    $data['err_code'] = 0;
    $data['status']   = 400;
    $post_id          = fetch_or_get($_POST['id'], 0);

    if (is_posnum($post_id)) {
        $post_data = cl_raw_post_data($post_id);

        if (not_empty($post_data)) {
            if ($post_data["profile_pinned"] == "Y") {
                cl_update_post_data($post_id, array(
                    "profile_pinned" => "N"
                ));

                $data['status']      = 200;
                $data['status_code'] = '0';
            }
            else {

                $db->where("user_id", $me["id"])->where("profile_pinned", "Y")->update(T_PUBS, array(
                    "profile_pinned" => "N"
                ));

                cl_update_post_data($post_id, array(
                    "profile_pinned" => "Y"
                ));

                $data['status']      = 200;
                $data['status_code'] = '1';
            }
        }
    }
}