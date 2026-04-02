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

else if ($action == "save_profile_name") {
	$data['err_code'] =  0;
    $data['status']   =  400;
    $username_restricts = cl_get_restricted_usernames();
	$user_data_fields =  array(
		'fname'       => fetch_or_get($_POST['fname'],null),
		'lname'       => fetch_or_get($_POST['lname'],null),
        'uname'       => fetch_or_get($_POST['uname'],null)
	);

	foreach ($user_data_fields as $field_name => $field_val) {
        if ($field_name == 'uname') {
            if (empty($field_val)) {
                $data['err_code'] = "invalid_uname"; break;
            }

            else if (len_between($field_val,3, 25) != true) {
                $data['err_code'] = "invalid_uname"; break;
            }

            else if (preg_match('/^[\w]+$/', $field_val) != true) {
                $data['err_code'] = "invalid_uname"; break;
            }

            else if(cl_uname_exists($field_val) && $field_val != $me['raw_uname']) {
                $data['err_code'] = "doubling_uname"; break;
            }

            else if(in_array($field_val, $username_restricts) && $field_val != $me['raw_uname']) {
                $data['err_code'] = "denied_uname"; break;
            }
        }

		else if ($field_name == 'fname') {
			if (empty($field_val) || len_between($field_val,3,25) != true) {
	            $data['err_code'] = "invalid_fname"; break;
	        }
		}

		else if ($field_name == 'lname') {
			if (not_empty($field_val) && len_between($field_val,3,25) != true) {
	            $data['err_code'] = "invalid_lname"; break;
	        }
		}
	}

	if (empty($data['err_code'])) {
        $fname          = cl_text_secure($user_data_fields['fname']);
        $lname          = cl_text_secure($user_data_fields['lname']);
        $uname          = cl_text_secure($user_data_fields['uname']);
        $data['status'] = 200;

        cl_update_user_data($me["id"], array(
            'fname'    => $fname,
            'lname'    => ((empty($lname)) ? "" : $lname),
            'username' => $uname,
        ));

        if ($uname != $me['raw_uname']) {
            cl_update_user_data($me["id"], array(
                'verified' => '0'
            ));
        }
    }
}

else if ($action == "save_profile_email") {
    $data['err_code'] = 0;
    $data['status']   = 400;
    $email            = fetch_or_get($_POST['email'], null);
    $useremail_restricts = cl_get_restricted_useremails();

    if (empty($email)) {
        $data['err_code'] = "invalid_email";
    }

    else if (filter_var($email, FILTER_VALIDATE_EMAIL) != true || len($email) > 55) {
        $data['err_code'] = "invalid_email";
    }

    else if (cl_email_exists($email) && ($email != $me['email'])) {
        $data['err_code'] = "doubling_email";
    }

    else if (in_array($email, $useremail_restricts) && ($email != $me['email'])) {
        $data['err_code'] = "denied_email";
    }

    else {

        $rand_code         = rand(100000,999999);
        $cl['email_data']  = array('name' => $me["name"], 'code' => $rand_code);
        $send_email_data   = array(
            'from_email'   => $cl['config']['email'],
            'from_name'    => $cl['config']['name'],
            'to_email'     => $email,
            'to_name'      => $me['name'],
            'subject'      => cl_translate("Confirm email on - {@name@}", array("name" => $cl['config']['name'])),
            'charSet'      => 'UTF-8',
            'is_html'      => true,
            'message_body' => cl_template('emails/confirm_email')
        ); 

        if (cl_send_mail($send_email_data)) {
            cl_update_user_data($me["id"], array(
                "email_conf_code" => json(array(
                    "email" => cl_text_secure($email), 
                    "code" => $rand_code
                ), true)
            ));

            $data['status'] = 200;
        }
    }
}

else if ($action == "save_profile_phone" && $cl["config"]["signup_conf_system"] == "phone") {
    $data['err_code'] = 0;
    $data['status']   = 400;
    $phone            = trim(fetch_or_get($_POST['phone'], ""));

    if (empty($phone)) {
        $data['err_code'] = "invalid_phone";
    }

    else if (is_numeric($phone) != true || len($phone) > 15) {
        $data['err_code'] = "invalid_phone";
    }

    else if (cl_phone_exists($phone) && ($phone != $me['phone'])) {
        $data['err_code'] = "doubling_phone";
    }

    else {

        try {
            $rand_code = rand(100000,999999);

            if ($cl["config"]["default_sms_provider"] == "twilio") {
                require_once(cl_full_path("core/libs/twilio/vendor/autoload.php"));

                $account_sid = $cl["config"]["twilio_account_sid"];
                $auth_token = $cl["config"]["twilio_auth_token"];
                $twilio_number = $cl["config"]["twilio_phone_number"];

                $client = new Twilio\Rest\Client($account_sid, $auth_token);
                $client->messages->create(
                    $phone,
                    array(
                        "from" => $twilio_number,
                        "body" => cl_strf("%s\n\nYour confirmation code is: %s", $cl["config"]["name"], $rand_code)
                    )
                );
            }

            else if($cl["config"]["default_sms_provider"] == "infobip") {
                $sms_body = array(
                    "messages" => array(
                        array(
                            "destinations" => array(
                                array(
                                    "to" => $phone
                                )
                            ),
                            "from" => $cl["config"]["infobip_phone_number"],
                            "text" => cl_strf("%s sign up confirmation code is: %s", $cl["config"]["name"], $rand_code)
                        )
                    )
                );

                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, cl_strf("%s/sms/2/text/advanced", $cl["config"]["infobip_base_url"]));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json($sms_body, true));

                $headers = array();
                $headers[] = cl_strf("Authorization: App %s", $cl["config"]["infobip_api_key"]);
                $headers[] = "Content-Type: application/json";
                $headers[] = "Accept: application/json";

                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                $result = curl_exec($ch);

                if (curl_errno($ch)) {
                    throw new Exception(curl_error($ch));
                }

                curl_close($ch);

                $result_text = $result;

                $result = json_decode($result, true);

                if (is_array($result) || empty($result["messages"])) {
                    throw new Exception(stripslashes($result_text));
                }
            }

            cl_update_user_data($me["id"], array(
                "phone_conf_code" => json(array(
                    "phone" => cl_text_secure($phone), 
                    "code" => $rand_code
                ), true)
            ));

            $data['status'] = 200;
        } 

        catch (Exception $e) {
            $data['status'] = 405;
            $data['err_message'] = $e->getMessage();
        }
    }
}

else if($action == "confirm_email" && not_empty($me["email_conf_code"])) {
    $data['err_code'] = 0;
    $data['status']   = 400;
    $email_conf_data  = json($me["email_conf_code"]);

    if (is_array($email_conf_data) && count($email_conf_data) == 2) {

        $email_conf_code1 = $email_conf_data["code"];
        $email_conf_code2 = fetch_or_get($_POST['code'], false);
        $new_email        = $email_conf_data["email"];

        if(empty($email_conf_code1) || empty($email_conf_code2)) {
            $data['err_code'] = "invalid_req_data";
        }

        else if(empty($new_email) || ($email_conf_code1 != $email_conf_code2)) {
            $data['err_code'] = "invalid_req_data";
        }
        else {

            $data['status'] = 200;

            cl_update_user_data($me["id"], array(
                'email' => $new_email,
                'email_conf_code' => ""
            ));
        }
    }
}

else if($action == "confirm_phone" && not_empty($me["phone_conf_code"])) {
    $data['err_code'] = 0;
    $data['status']   = 400;
    $phone_conf_data  = json($me["phone_conf_code"]);

    if (is_array($phone_conf_data) && count($phone_conf_data) == 2) {

        $phone_conf_code1 = $phone_conf_data["code"];
        $phone_conf_code2 = fetch_or_get($_POST['code'], false);
        $new_phone        = $phone_conf_data["phone"];

        if(empty($phone_conf_code1) || empty($phone_conf_code2)) {
            $data['err_code'] = "invalid_req_data";
        }

        else if(empty($new_phone) || ($phone_conf_code1 != $phone_conf_code2)) {
            $data['err_code'] = "invalid_req_data";
        }
        else {

            $data['status'] = 200;

            cl_update_user_data($me["id"], array(
                'phone' => $new_phone,
                'phone_conf_code' => ""
            ));
        }
    }
}

else if ($action == "save_profile_url") {
    $data['err_code'] = 0;
    $data['status']   = 400;
    $website          = fetch_or_get($_POST['url'], null);

    if (not_empty($website)) {
        if (is_url($website) != true || len($website) > 115) {
            $data['err_code'] = "invalid_url";
        }

        else {
            $website        = cl_text_secure($website);
            $data['status'] = 200;

            if ($website != $me['website']) {
                cl_update_user_data($me["id"], array(
                    'website' => $website
                ));
            }
        }
    }
    else {
        $data['status'] = 200;
        cl_update_user_data($me["id"], array(
            'website' => ""
        )); 
    }
}

else if ($action == "save_social_networks") {
    $data['err_code'] = 0;
    $data['status'] = 400;
    $twitter_url = fetch_or_get($_POST['twitter'], "");
    $youtube_url = fetch_or_get($_POST['youtube'], "");
    $instagram_url = fetch_or_get($_POST['instagram'], "");
    $vkontakte_url = fetch_or_get($_POST['vkontakte'], "");
    $tiktok_url = fetch_or_get($_POST['tiktok'], "");
    $linkedin_url = fetch_or_get($_POST['linkedin'], "");
    $facebook_url = fetch_or_get($_POST['facebook'], "");

    $update_data = array();

    if (empty($twitter_url) || (is_url($twitter_url) && len($twitter_url) <= 200)) {
        $update_data["twitter"] = $twitter_url;
    }

    if (empty($youtube_url) || (is_url($youtube_url) && len($youtube_url) <= 200)) {
        $update_data["youtube"] = $youtube_url;
    }

    if (empty($instagram_url) || (is_url($instagram_url) && len($instagram_url) <= 200)) {
        $update_data["instagram"] = $instagram_url;
    }

    if (empty($vkontakte_url) || (is_url($vkontakte_url) && len($vkontakte_url) <= 200)) {
        $update_data["vkontakte"] = $vkontakte_url;
    }

    if (empty($tiktok_url) || (is_url($tiktok_url) && len($tiktok_url) <= 200)) {
        $update_data["tiktok"] = $tiktok_url;
    }

    if (empty($linkedin_url) || (is_url($linkedin_url) && len($linkedin_url) <= 200)) {
        $update_data["linkedin"] = $linkedin_url;
    }

    if (empty($facebook_url) || (is_url($facebook_url) && len($facebook_url) <= 200)) {
        $update_data["facebook"] = $facebook_url;
    }

    if (not_empty($update_data)) {
        $data['status'] = 200;


        cl_update_user_data($me["id"], $update_data);
    }
    else {
        $data['status'] = 200;
    }
}

else if ($action == "save_profile_bio") {
    $data['err_code'] = 0;
    $data['status']   = 400;
    $user_bio         = fetch_or_get($_POST['bio'], null);

    if (not_empty($user_bio)) {
        if (len($user_bio) > 140) {
            $data['err_code'] = "invalid_bio";
        }

        else {
            $user_bio       = cl_text_secure($user_bio);
            $data['status'] = 200;

            if ($user_bio != $me['about']) {  
                cl_update_user_data($me["id"], array(
                    'about' => $user_bio
                ));
            }
        }
    }
    else {
        $data['status'] = 200;
        cl_update_user_data($me["id"], array(
            'about' => ""
        )); 
    }
}

else if ($action == "save_profile_city") {
    $data['err_code'] = 0;
    $data['status']   = 400;
    $user_city        = fetch_or_get($_POST['city'], null);

    if (not_empty($user_city)) {
        if (len($user_city) > 30) {
            $data['err_code'] = "invalid_city_name";
        }

        else {
            $user_city      = cl_text_secure($user_city);
            $data['status'] = 200;

            if ($user_city != $me['city']) {  
                cl_update_user_data($me["id"], array(
                    'city' => $user_city
                ));
            }
        }
    }
    else {
        $data['status'] = 200;
        cl_update_user_data($me["id"], array(
            'city' => ""
        )); 
    }
}

else if ($action == "save_profile_gender") {
    $data['err_code'] = 0;
    $data['status']   = 400;
    $gender           = fetch_or_get($_POST['gender'], null);

    if (not_empty($gender) && in_array($gender, array('M', 'F', 'T', 'O'))) {

        if($cl['config']['non_binary_gender'] == 'off' && in_array($gender, array('O', 'T'))) {
            $data['err_code'] = "invalid_gender";
        }
        else{
            cl_update_user_data($me["id"], array(
                'gender' => $gender
            ));

            $data['status'] = 200;
        }
    }
}

else if ($action == "save_privacy_settings") {
    $data['err_code'] = 0;
    $data['status']   = 400;
    $profile_privacy  = fetch_or_get($_POST['profile_privacy'], null);
    $follow_privacy   = fetch_or_get($_POST['follow_privacy'], null);
    $contact_privacy  = fetch_or_get($_POST['contact_privacy'], null);
    $index_privacy    = fetch_or_get($_POST['index_privacy'], null);
    $online_ind    = fetch_or_get($_POST['online_ind'], null);

    if (in_array($profile_privacy, array('everyone','followers')) != true) {
        $data['err_code'] = "invalid_profile_privacy";
    }

    else if (in_array($follow_privacy, array('everyone', 'approved')) != true) {
        $data['err_code'] = "invalid_follow_privacy";
    }

    else if (in_array($contact_privacy, array('everyone','followed')) != true) {
        $data['err_code'] = "invalid_contact_privacy";
    }

    else if (in_array($index_privacy, array('Y','N')) != true) {
        $data['err_code'] = "invalid_index_privacy";
    }

    else if (in_array($online_ind, array('on','off')) != true) {
        $data['err_code'] = "invalid_online_ind";
    }

    else {
        cl_update_user_data($me["id"], array(
            'profile_privacy' => $profile_privacy,
            'follow_privacy'  => $follow_privacy,
            'contact_privacy' => $contact_privacy,
            'online_ind' => $online_ind,
            'index_privacy'   => $index_privacy
        ));

        cl_db_update(T_PUBS, array(
            "user_id" => $me["id"],
            "status"  => "active"
        ), array(
            "priv_wcs" => $profile_privacy
        ));

        if ($online_ind == "off") {
            cl_update_user_data($me['id'], array(
                'is_online' => cl_minify_js(json(array(
                    "last_seen" => 0,
                    "online_ind" => "off"
                ), true))
            ));
        }

        else{
            cl_update_user_data($me['id'], array(
                'is_online' => cl_minify_js(json(array(
                    "last_seen" => time(),
                    "online_ind" => "on"
                ), true))
            ));
        }

        $data['status'] = 200;
    }
}

else if ($action == 'save_profile_pass') {
    $data['status']     =  400;
    $data['err_code']   =  null;
    $user_data_fields   =  array(
        'curr_password' => fetch_or_get($_POST['curr_password'],null),
        'new_password'  => fetch_or_get($_POST['new_password'],null),
        'new_conf_pass' => fetch_or_get($_POST['new_conf_pass'],null),
    );

    foreach ($user_data_fields as $field_name => $field_val) {
        if ($field_name == 'curr_password') {
            if (empty($field_val) || (password_verify($field_val, $me['password']) != true)) {
                $data['err_code'] = "invalid_curr_pass"; break;
            }
        }

        else if ($field_name == 'new_password') {
            if (empty($field_val) || len_between($field_val,6,20) != true) {
                $data['err_code'] = "invalid_password"; break;
            }
        }

        else if($field_name == 'new_conf_pass') {
            if (empty($field_val) || ($field_val != $user_data_fields['new_password'])) {
                $data['err_code'] = "invalid_password"; break;
            }
        }
    }

    if (empty($data['err_code'])) {
        $data['status'] =  200;
        $user_id        =  $me['id'];
        $update_data    =  array(
            'password'  => password_hash(cl_text_secure($user_data_fields['new_password']), PASSWORD_DEFAULT),
        ); 

        cl_update_user_data($user_id, $update_data);
    }
}

else if ($action == "save_profile_lang") {
    $data['err_code'] = 0;
    $data['status']   = 400;
    $prof_lang        = fetch_or_get($_POST['language'],null);

    if (empty($prof_lang) || empty($cl["languages"][$prof_lang])) {
        $data['err_code'] = "invalid_lang";
    }

    else {
        $data['status'] = 200;

        if ($prof_lang != $me['language']) {
            cl_update_user_data($me["id"], array(
                'language' => $prof_lang
            ));
        }
    }
}

else if ($action == "save_profile_country") {
    $data['err_code'] = 0;
    $data['status']   = 400;
    $prof_country     = fetch_or_get($_POST['country'], null);
    $country_list     = array_keys($cl["countries"]);

    if (not_num($prof_country) || (in_array($prof_country, $country_list) != true)) {
        $data['err_code'] = "invalid_country";
    }

    else {
        $data['status'] = 200;

        if ($prof_country != $me['country_id']) {
            cl_update_user_data($me["id"], array(
                'country_id' => $prof_country
            ));
        }
    }
}

else if ($action == 'delete_account') {
    $data['status']   = 400;
    $data['err_code'] = null;
    $curr_password    = fetch_or_get($_POST['password'],null);

    if (empty($curr_password) || (password_verify($curr_password, $me['password']) != true)) {
        $data['err_code'] = "invalid_pass";
    }

    else {
        $data['status'] = 200;

        unset($_COOKIE['user_id']);
        setcookie('user_id', "", -1);

        unset($_COOKIE['dark_mode']);
        setcookie('dark_mode', "", -1);

        cl_delete_user_data($me['id']);
    }
}

else if ($action == 'upload_profile_avatar') {
    if (not_empty($_FILES['avatar']) && not_empty($_FILES['avatar']['tmp_name'])) {
        $file_info      =  array(
            'file'      => $_FILES['avatar']['tmp_name'],
            'size'      => $_FILES['avatar']['size'],
            'name'      => $_FILES['avatar']['name'],
            'type'      => $_FILES['avatar']['type'],
            'file_type' => 'thumbnail',
            'folder'    => 'avatars',
            'slug'      => 'avatar',
            'crop'      => array('width' => 512, 'height' => 512),
            'allowed'   => 'jpg,png,jpeg,gif'
        );

        $file_upload = cl_upload($file_info);

        if (not_empty($file_upload['cropped'])) {
            $data['status'] = 200;
            $data['url']    = cl_get_media($file_upload['cropped']);

            cl_delete_media($file_upload['filename']);
            cl_delete_media($me['raw_avatar']);

            cl_update_user_data($me['id'], array(
                'avatar' => $file_upload['cropped']
            ));
        } 

        else{
            $data['err_code'] = "invalid_req_data";
            $data['status']   = 400;
        }
    }
}

else if ($action == 'upload_profile_cover') {
    if (not_empty($_FILES['cover']) && not_empty($_FILES['cover']['tmp_name'])) {
        $file_info           = array(
            'file'           => $_FILES['cover']['tmp_name'],
            'size'           => $_FILES['cover']['size'],
            'name'           => $_FILES['cover']['name'],
            'type'           => $_FILES['cover']['type'],
            'file_type'      => 'image',
            'folder'         => 'covers',
            'slug'           => 'cover',
            'allowed'        => 'jpg,png,jpeg,gif',
            'aws_uploadfile' => "N"
        );

        $file_upload = cl_upload($file_info);

        if (not_empty($file_upload['filename'])) {
            try {
                require_once(cl_full_path("core/libs/PHPgumlet/ImageResize.php"));
                require_once(cl_full_path("core/libs/PHPgumlet/ImageResizeException.php"));

                $prof_cover = new \Gumlet\ImageResize(cl_full_path($file_upload['filename']));
                $sw         = $prof_cover->getSourceWidth();
                $sh         = $prof_cover->getSourceHeight();
                $data['sw'] = $sw;
                $data['sh'] = $sh;

                $path_info      = explode(".", $file_upload['filename']);
                $filepath       = fetch_or_get($path_info[0],"");
                $file_ext       = fetch_or_get($path_info[1],"");
                $cropped_cover  = cl_strf("%s_600x200.%s", $filepath, $file_ext);
                $data['status'] = 200;

                $prof_cover->crop(600, 200, true);
                $prof_cover->save(cl_full_path($cropped_cover));

                cl_delete_media($me['raw_cover']);
                cl_delete_media($me['cover_orig']);

                cl_update_user_data($me['id'], array(
                    'cover' => $cropped_cover,
                    'cover_orig' => $file_upload['filename']
                ));

                if ($sw != 600) {
                    $prof_cover = new \Gumlet\ImageResize(cl_full_path($file_upload['filename']));
                    $prof_cover->resize(600,(($sh * 600) / $sw), true);
                    $prof_cover->save(cl_full_path($file_upload['filename']));
                }

                if ($cl['config']['as3_storage'] == 'on') {
                    cl_upload2s3($cropped_cover);
                    cl_upload2s3($file_upload['filename']);
                }
            } 

            catch (Exception $e) {
                $data['err_code']    = "invalid_req_data";
                $data['err_message'] = $e->getMessage();
                $data['status']      = 400;
            }
        } 

        else{
            $data['err_code'] = "invalid_req_data";
            $data['status']   = 400;
        }
    }
}

else if($action == "save_profcover_rep") {
    $data['err_code'] = 0;
    $data['status']   = 400;
    $new_position     = fetch_or_get($_POST['position'], 0);
    $dw               = 600;
    $dh               = 200;

    if (is_numeric($new_position)) {
        try {
            require_once(cl_full_path("core/libs/PHPgumlet/ImageResize.php"));
            require_once(cl_full_path("core/libs/PHPgumlet/ImageResizeException.php"));


            $cover_orig = $me['cover_orig'];

            if ($cl['config']['as3_storage'] == 'on') {
                $cover_orig = cl_import_aws_media($cover_orig);
            }
            
            if (file_exists(cl_full_path($cover_orig))) {

                $prof_cover     = new \Gumlet\ImageResize(cl_full_path($cover_orig));
                $data['status'] = 200;
                $file_ext       = explode('.', $me['raw_cover']);
                $file_ext       = end($file_ext);
                $file_ext       = (empty($file_ext)) ? 'jpg' : $file_ext;
                $filename       = cl_gen_path(array(
                    'file_ext'  => $file_ext,
                    'file_type' => 'image',
                    'folder'    => 'covers',
                    'slug'      => 'cover',
                ));

                $prof_cover->freecrop($dw, $dh, 0, abs($new_position));
                $prof_cover->save(cl_full_path($filename));
                
                cl_delete_media($me['raw_cover']);

                cl_update_user_data($me['id'], array(
                    'cover' => $filename
                ));

                if ($cl['config']['as3_storage'] == 'on') {
                    try {
                        cl_upload2s3($filename);
                    } catch (Exception $e) { /* pass */ }

                    cl_delete_loc_media($cover_orig);
                }
            }

            else{
                $data['err_code'] = "invalid_req_data";
                $data['status']   = 500;
            }
        } 

        catch (Exception $e) {
            $data['err_code']    = "invalid_req_data";
            $data['err_message'] = $e->getMessage();
            $data['status']      = 400;
        }
    }
}

else if($action == 'verify_account') {
    $data['status']   = 400;
    $data['err_code'] = 0;

    if ($me['verified'] == '2') {
        $data['err_code'] = "duplicate_request_error";
    }

    else if (empty($_POST['full_name']) || len_between($_POST['full_name'], 3, 60) != true) {
        $data['err_code'] = "invalid_full_name";
    }

    else if (empty($_POST['text_message']) || len_between($_POST['text_message'], 1, 1200) != true) {
        $data['err_code'] = "invalid_text_message";
    }

    else if(empty($_FILES['video']) || empty($_FILES['video']['tmp_name'])) {
        $data['err_code'] = "invalid_video_message";
    }

    else {
        $file_info      = array(
            'file'      => $_FILES['video']['tmp_name'],
            'size'      => $_FILES['video']['size'],
            'name'      => $_FILES['video']['name'],
            'type'      => $_FILES['video']['type'],
            'file_type' => 'video',
            'folder'    => 'videos',
            'slug'      => 'video_message',
            'allowed'   => 'mp4,mov,3gp,webm',
        );

        $file_upload = cl_upload($file_info);

        if (not_empty($file_upload['filename'])) {
            $full_name          = cl_text_secure($_POST['full_name']);
            $text_message       = cl_text_secure($_POST['text_message']);
            $insert_data        = array(
                'user_id'       => $me['id'],
                'full_name'     => $full_name,
                'text_message'  => $text_message,
                'video_message' => $file_upload['filename'],
                'time'          => time(),
            );

            $req_id = $db->insert(T_VERIFICATIONS, $insert_data);

            if (is_posnum($req_id)) {
                $data['err_code'] = 0;
                $data['status']   = 200;

                cl_update_user_data($me['id'], array(
                    'verified' => '2'
                ));
            }
        }
    }
}

else if($action == 'affiliate_payout_req') {
    $data['status']   = 400;
    $data['err_code'] = 0;
    $curr_aff_balance = cl_calc_affiliate_bonuses();
    $payout_amount    = fetch_or_get($_POST['amount'], false);
    $payout_email     = fetch_or_get($_POST['paypal'], false);

    if (empty($payout_amount) || not_num($payout_amount) || ($payout_amount > $curr_aff_balance)) {
        $data['status'] = "invalid_payment_amount";
    }

    else if(empty($payout_email) || filter_var($payout_email, FILTER_VALIDATE_EMAIL) != true) {
        $data['status'] = "invalid_payment_email";
    }

    else if(cl_aff_request_exists()) {
        $data['status'] = "invalid_req_data";
    }

    else {

        $insert_data  = array(
            'user_id' => $me['id'],
            'amount'  => $payout_amount,
            'email'   => $payout_email,
            'status'  => 'pending',
            'bonuses' => $me['aff_bonuses'],
            'time'    => time()
        );

        $insert_id = $db->insert(T_AFF_PAYOUTS, $insert_data);

        if (is_posnum($insert_id)) {
            $data['status'] = 200;
        }
    }
}

else if ($action == "save_notif_settings") {
    $data['err_code']         = 0;
    $data['status']           = 200;
    $me["settings"]["notifs"] = array(
        "like"                => ((not_empty($_POST["like"])) ? 1 : 0),
        "subscribe"           => ((not_empty($_POST["subscribe"])) ? 1 : 0),
        "subscribe_request"   => ((not_empty($_POST["subscribe_request"])) ? 1 : 0),
        "subscribe_accept"    => ((not_empty($_POST["subscribe_accept"])) ? 1 : 0),
        "reply"               => ((not_empty($_POST["reply"])) ? 1 : 0),
        "repost"              => ((not_empty($_POST["repost"])) ? 1 : 0),
        "mention"             => ((not_empty($_POST["mention"])) ? 1 : 0)
    );

    cl_update_user_data($me["id"], array(
        'settings' => json($me["settings"], true)
    ));
}

else if ($action == "save_enotif_settings") {
    $data['err_code']          = 0;
    $data['status']            = 200;
    $me["settings"]["enotifs"] = array(
        "like"                 => ((not_empty($_POST["like"])) ? 1 : 0),
        "subscribe"            => ((not_empty($_POST["subscribe"])) ? 1 : 0),
        "subscribe_request"    => ((not_empty($_POST["subscribe_request"])) ? 1 : 0),
        "subscribe_accept"     => ((not_empty($_POST["subscribe_accept"])) ? 1 : 0),
        "reply"                => ((not_empty($_POST["reply"])) ? 1 : 0),
        "repost"               => ((not_empty($_POST["repost"])) ? 1 : 0),
        "mention"              => ((not_empty($_POST["mention"])) ? 1 : 0)
    );

    if ($cl["config"]["email_notifications"] == "on") {
        cl_update_user_data($me["id"], array(
            'settings' => json($me["settings"], true)
        ));
    }
}

else if($action == "download_profile_info") {

    require_once(cl_full_path("core/apps/info/app_ctrl.php"));

    $data['err_code']  = 0;
    $data['status']    = 400;
    $prof_data_options = array(
        "user_info"    => fetch_or_get($_POST["my_info"], "N"),
        "following"    => fetch_or_get($_POST["following"], "N"),
        "followers"    => fetch_or_get($_POST["followers"], "N"),
        "posts"        => fetch_or_get($_POST["posts"], "N"),
        "bookmarks"    => fetch_or_get($_POST["bookmarks"], "N")
    );

    $cl["account_data"] = cl_get_user_account_data($me["id"], $prof_data_options);

    if (not_empty($cl["account_data"])) {
        $time_hash    = md5(microtime());
        $info_file    = cl_template("info/content");
        $info_tmpfile = tempnam(sys_get_temp_dir(), $time_hash);
        
        file_put_contents($info_tmpfile, $info_file);

        cl_update_user_data($me["id"], array(
            "info_file" => $info_tmpfile
        ));

        $data["status"] = 200;
        $data["url"]    = cl_link("download_info");
    }
}

else if ($action == "cancel_affiliate_payout_req") {
    $data['err_code'] = 0;
    $data['status']   = 200;

    cl_db_delete_item(T_AFF_PAYOUTS, array(
        "user_id" => $me["id"],
        "status" => "pending"
    ));
}

else if ($action == "upgrade_to_premium" && $cl["config"]["user_wallet_status"] == "on") {
    $data['err_code'] = 0;
    $data['status']   = 400;

    if ($me["is_premium"] != "1" && $me["wallet"] >= $cl["config"]["premium_account_mprice"]) {
        $data['status'] = 200;

        cl_update_user_data($me["id"], array(
            "wallet" => ($me["wallet"] - $cl["config"]["premium_account_mprice"]),
            "is_premium" => "1",
            "premium_ex_date" => (time() + (86400 * 31))
        ));

        cl_db_insert(T_WALLET_HISTORY, array(
            'user_id'   => $me['id'],
            'operation' => 'premium_account_purchase',
            'amount'    => $cl["config"]["premium_account_mprice"],
            'json_data' => json(array(), true),
            'time' => time()
        ));
    }
}

else if ($action == "trans_aff_wallet") {
    $data['err_code'] = 0;
    $data['status']   = 400;

    if ($me["aff_bonuses"] && $cl["config"]["user_wallet_status"] == "on") {

        $aff_earnings = cl_calc_affiliate_bonuses();
        $data['status'] = 200;
    
        cl_update_user_data($me["id"], array(
            "wallet" => ($me["wallet"] + $aff_earnings),
            "aff_bonuses" => 0
        ));

        $history_rec_id = $db->insert(T_WALLET_HISTORY, array(
            "user_id" => $me["id"],
            "operation" => "affiliate_wallet_tup",
            "amount" => $aff_earnings,
            "time" => time(),
            "status" => "success",  
            "trans_id" => ""
        ));
    }
}

else if ($action == "save_monitiz_settings") {
    $data['err_code'] = 0;
    $data['status']   = 400;

    $monitiz_status = fetch_or_get($_POST["monitiz_status"], "off");
    $subscription_price = fetch_or_get($_POST["price"], "N");

    if (is_posnum($subscription_price) && in_array($monitiz_status, array("on", "off"))) {
        cl_update_user_data($me["id"], array(
            "cont_monetization" => (($monitiz_status == "on") ? "Y" : "N"),
            "subscription_price" => $subscription_price
        ));

        $data['status'] = 200;
    }
}

else if ($action == "save_premium_features_settings") {
    $data['err_code'] = 0;
    $data['status'] = 200;

    $verified_badge = fetch_or_get($_POST["verified_badge"], false);

    $me["premium_settings"] = array(
        "disable_native_ads" => ((not_empty($_POST["disable_native_ads"])) ? 1 : 0),
        "disable_adsense_ads" => ((not_empty($_POST["disable_adsense_ads"])) ? 1 : 0)
    );

    $user_update_data = array(
        'premium_settings' => json($me["premium_settings"], true)
    );
    

    if (not_empty($verified_badge)) {
        $user_update_data["verified"] = "1";
    }
    else{
        $user_update_data["verified"] = "0";
    }

    cl_update_user_data($me["id"], $user_update_data);
}

else if ($action == "save_feed_preferences_settings") {
    $data['err_code'] = 0;
    $data['status'] = 200;

    $feed_rec_status = fetch_or_get($_POST["feed_rec_status"], false);

    if ($feed_rec_status != "on") {
        $feed_rec_status = "off";
    }

    else{
        $feed_rec_status = "on";
    }

    cl_update_user_data($me["id"], array(
        "rec_feed" => $feed_rec_status
    ));
}