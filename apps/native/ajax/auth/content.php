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

if ($action == "login") {
	$data['err_code']  = 0;
	$user_data_fileds  = array(
		'email'        => fetch_or_get($_POST['email'],null),
		'password'     => fetch_or_get($_POST['password'],null),
	);

	foreach ($user_data_fileds as $field_name => $field_val) {
		if ($field_name == 'email') {
			if (empty($field_val) || len($field_val) > 55) {
	            $data['err_code'] = $field_name; break;
	        }
		}

		if ($field_name == 'password') {
			if (empty($field_val) || len($field_val) > 20) {
	            $data['err_field'] = $field_name; break;
	        }
		}
	}

	if (empty($data['err_code'])) {
        $email    = cl_text_secure($user_data_fileds['email']);
        $password = cl_text_secure($user_data_fileds['password']);
        $db       = $db->where("username", $email);
        $db       = $db->orWhere("email", $email);
        $raw_user = $db->getOne(T_USERS, array("password", "id", "active"));

        if (cl_queryset($raw_user) != true || $raw_user["active"] != "1") {
        	$data['err_code'] = "invalid_creds";
        } 

        else if (password_verify($password, $raw_user["password"]) != true) {
        	$data['err_code'] = "invalid_creds";
        } 

        if (empty($data["err_code"])) {   
        	$user_ip        = cl_get_ip();
        	$user_ip        = ((filter_var($user_ip, FILTER_VALIDATE_IP) == true) ? $user_ip : '0.0.0.0');
	        $session_id     = cl_create_user_session($raw_user["id"], "web");
            $data['status'] = 200;

            cl_update_user_data($raw_user["id"],array(
            	'ip_address'  => $user_ip,
            	'last_active' => time(),
            ));
        }
    }
}

else if ($action == 'signup') {
    $invite_code = fetch_or_get($_POST["invite_code"], false);
    $invite_link = (not_empty($invite_code)) ? cl_db_get_item(T_USER_INVITES, array("code" => $invite_code)) : false;

    if ($cl['config']['user_signup'] == "on" || (not_empty($invite_link) && cl_verify_invite_code($invite_code))) {
        $data['err_code'] = 0;
        $data['status']   = 400;

        $username_restricts = cl_get_restricted_usernames();
        $useremail_restricts = cl_get_restricted_useremails();
        $user_data_fileds = array(
            'uname' => fetch_or_get($_POST['uname'], null),
            'email' => fetch_or_get($_POST['email'], null),
            'phone' => fetch_or_get($_POST['phone'], null),
            'password' => fetch_or_get($_POST['password'], null)
        );

        foreach ($user_data_fileds as $field_name => $field_val) {
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

                else if(cl_uname_exists($field_val)) {
                    $data['err_code'] = "doubling_uname"; break;
                }

                else if(in_array($field_val, $username_restricts)) {
                    $data['err_code'] = "denied_uname"; break;
                }
            }

            else if ($field_name == 'email' && $cl["config"]["signup_conf_system"] == "email") {
                if (empty($field_val)) {
                    $data['err_code'] = "invalid_email"; break;
                }

                else if (!filter_var(trim($field_val), FILTER_VALIDATE_EMAIL) || len($field_val) > 55) {
                    $data['err_code'] = "invalid_email"; break;
                }

                else if (cl_email_exists($field_val)) {
                    $data['err_code'] = "doubling_email"; break;
                }

                else if(in_array($field_val, $useremail_restricts)) {
                    $data['err_code'] = "denied_email"; break;
                }
            }

            else if ($field_name == 'phone' && $cl["config"]["signup_conf_system"] == "phone") {
                if (empty($field_val)) {
                    $data['err_code'] = "invalid_phone"; break;
                }

                else if (is_numeric(trim($field_val)) != true || len($field_val) > 15) {
                    $data['err_code'] = "invalid_phone"; break;
                }

                else if (cl_phone_exists($field_val)) {
                    $data['err_code'] = "doubling_phone"; break;
                }
            }

            else if ($field_name == 'password') {
                if (empty($field_val) || len_between($field_val,6,20) != true) {
                    $data['err_code'] = "invalid_password"; break;
                }
            }
        }

        if ($cl["config"]["google_recaptcha"] == "on") {
            $data['err_code'] = "grecaptcha_error";
            $recaptcha_code   = fetch_or_get($_POST['g-recaptcha-response'], false);
            $recaptcha_verif  = file_get_contents(cl_strf("https://www.google.com/recaptcha/api/siteverify?secret=%s&response=%s", $cl["config"]["google_recap_key2"], $recaptcha_code));
            
            if (not_empty($recaptcha_verif)) {
                $recaptcha_verif = json($recaptcha_verif);

                if (is_array($recaptcha_verif) && not_empty($recaptcha_verif["success"]) && $recaptcha_verif["success"] == 1) {
                    $data['err_code'] = false;
                }
            }
        }

        if (empty($data['err_code'])) {
            if ($cl['config']['acc_validation'] == 'off') {
                $email_code       = sha1(time() + rand(111,999));
                $password_hashed  = password_hash($user_data_fileds["password"], PASSWORD_DEFAULT);
                $user_ip          = cl_get_ip();
                $user_ip          = ((filter_var($user_ip, FILTER_VALIDATE_IP) == true) ? $user_ip : '0.0.0.0');
                $is_admin         = "0";

                if (not_empty($invite_link)) {
                    if ($invite_link["role"] == "admin") {
                        $is_admin = "1";
                    }
                    
                    cl_db_update(T_USER_INVITES, array(
                        "id" => $invite_link["id"]
                    ), array(
                        "registered_users" => ($invite_link["registered_users"] += 1)
                    ));
                }

                if ($cl["config"]["signup_conf_system"] == "phone") {
                    $user_data_fileds["email"] = "";
                }
                else{
                    $user_data_fileds["phone"] = "";
                }

                $insert_data      = array(
                    'fname'       => cl_text_secure($user_data_fileds["uname"]),
                    'lname'       => "",
                    'username'    => cl_text_secure($user_data_fileds["uname"]),
                    'password'    => $password_hashed,
                    'email'       => cl_text_secure($user_data_fileds["email"]),
                    'phone'       => cl_text_secure($user_data_fileds["phone"]),
                    'admin'       => $is_admin,
                    'active'      => '1',
                    'em_code'     => $email_code,
                    'last_active' => time(),
                    'joined'      => time(),
                    'start_up'    => json(array('source' => 'system', 'avatar' => 0, 'info' => 0, 'follow' => 0), true),
                    'ip_address'  => $user_ip,
                    'country_id'  => $cl['config']['country_id'],
                    'language'    => $cl['config']['language'],
                    'display_settings' => json(array("color_scheme" => $cl["config"]["default_color_scheme"], "background" => $cl["config"]["default_bg_color"]), true)
                );

                $user_id = $db->insert(T_USERS, $insert_data);

                if (is_posnum($user_id)) {
                    cl_create_user_session($user_id,'web');

                    cl_user_auto_follow($user_id);

                    $data['status'] = 200;
                }
            }

            else {
                $rand_code = rand(100000,999999);
                $vud_id = sha1(rand(11111, 99999)) . time() . md5(microtime() . $rand_code);

                if ($cl["config"]["signup_conf_system"] == "phone") {
                    $user_data_fileds['phone_conf_code'] = $rand_code;
                    $sms_sent = false;

                    try {
                        if ($cl["config"]["default_sms_provider"] == "twilio") {
                            require_once(cl_full_path("core/libs/twilio/vendor/autoload.php"));

                            $account_sid = $cl["config"]["twilio_account_sid"];
                            $auth_token = $cl["config"]["twilio_auth_token"];
                            $twilio_number = $cl["config"]["twilio_phone_number"];

                            $client = new Twilio\Rest\Client($account_sid, $auth_token);
                            $client->messages->create(
                                $user_data_fileds["phone"],
                                array(
                                    "from" => $twilio_number,
                                    "body" => cl_strf("%s sign up confirmation code is: %s", $cl["config"]["name"], $rand_code)
                                )
                            );
                        }
                        else if($cl["config"]["default_sms_provider"] == "infobip") {
                            $sms_body = array(
                                "messages" => array(
                                    array(
                                        "destinations" => array(
                                            array(
                                                "to" => $user_data_fileds["phone"]
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

                        setcookie("vud_id", $vud_id, strtotime("+7 days"), '/') or die('unable to create cookie');

                        cl_db_insert(T_ACC_VALIDS, array(
                            "hash" => $vud_id,
                            "json" => json($user_data_fileds, true),
                            "time" => time()
                        ));

                        $data['status'] = 401;  
                    }

                    catch (Exception $e) {
                        $data['status'] = 405;
                        $data['error_message'] = $e->getMessage();
                    }
                }
                else{
                    $user_data_fileds['em_code'] = $rand_code;
                    $user_name         = $user_data_fileds["uname"];
                    $user_email        = $user_data_fileds['email'];
                    $cl['email_data']  = array('name' => $user_name, 'code' => $rand_code);
                    $send_email_data   = array(
                        'from_email'   => $cl['config']['email'],
                        'from_name'    => $cl['config']['name'],
                        'to_email'     => $user_email,
                        'to_name'      => $user_name,
                        'subject'      => cl_translate("Confirm registration on - {@name@}", array("name" => $cl['config']['name'])),
                        'charSet'      => 'UTF-8',
                        'is_html'      => true,
                        'message_body' => cl_template('emails/confirm_registration')
                    ); 

                    if (cl_send_mail($send_email_data)) {

                        if (not_empty($invite_link)) {
                            if ($invite_link["role"] == "admin") {
                                $user_data_fileds["admin"] = "1";
                            }
                            
                            cl_db_update(T_USER_INVITES, array(
                                "id" => $invite_link["id"]
                            ), array(
                                "registered_users" => ($invite_link["registered_users"] += 1)
                            ));
                        }

                        setcookie("vud_id", $vud_id, strtotime("+7 days"), '/') or die('unable to create cookie');

                        $data['status'] = 401;

                        cl_db_insert(T_ACC_VALIDS, array(
                            "hash" => $vud_id,
                            "json" => json($user_data_fileds, true),
                            "time" => time()
                        ));
                    }
                }
            }
        }
    }
}

else if($action == 'confirm_registration') {
    $data['err_code']    = 0;
    $data['status']      = 400;
    $vud_id              = fetch_or_get($_COOKIE['vud_id']);
    $acc_validation_code = fetch_or_get($_POST['code'], false);
    $acc_validation_data = false;

    if (not_empty($vud_id)) {
        $vu_data = cl_db_get_item(T_ACC_VALIDS, array(
            "hash" => $vud_id
        ));

        if (not_empty($vu_data)) {
            $acc_validation_data = json($vu_data["json"]);
        }
    }
    
    if(empty($acc_validation_data)) {
        $data['err_code'] = "invalid_user_data";
    }

    else if(empty($acc_validation_code)) {
        $data['err_code'] = "invalid_acc_code";
        $data['status']   = 401;
    }

    else {

        $acc_validation_data_code = false;

        if ($cl["config"]["signup_conf_system"] == "phone") {
            $acc_validation_data_code = $acc_validation_data["phone_conf_code"];
        }
        else{
            $acc_validation_data_code = $acc_validation_data['em_code'];
        }
    
        if ($acc_validation_code == $acc_validation_data_code) {

            $is_phone_or_email_taken = false;

            if ($cl["config"]["signup_conf_system"] == "phone") {
                $is_phone_or_email_taken = cl_phone_exists($acc_validation_data['phone']);
            }
            else{
                $is_phone_or_email_taken = cl_email_exists($acc_validation_data['email']);
            }

            if ($is_phone_or_email_taken || cl_uname_exists($acc_validation_data['uname'])) {
                $data['err_code'] = "invalid_vu_data";
                $data['status']   = 402;
            }

            else{
                
                $password_hashed  = password_hash($acc_validation_data["password"], PASSWORD_DEFAULT);
                $user_ip          = cl_get_ip();
                $user_ip          = ((filter_var($user_ip, FILTER_VALIDATE_IP) == true) ? $user_ip : '0.0.0.0');
                $insert_data      = array(
                    'fname'       => cl_text_secure($acc_validation_data["uname"]),
                    'lname'       => "",
                    'username'    => cl_text_secure($acc_validation_data["uname"]),
                    'password'    => $password_hashed,
                    'active'      => '1',
                    'admin'       => fetch_or_get($acc_validation_data["admin"], "0"),
                    'last_active' => time(),
                    'joined'      => time(),
                    'start_up'    => json(array('source' => 'system', 'avatar' => 0, 'info' => 0, 'follow' => 0), true),
                    'ip_address'  => $user_ip,
                    'language'    => $cl['config']['language'],
                    'display_settings' => json(array("color_scheme" => $cl["config"]["default_color_scheme"], "background" => $cl["config"]["default_bg_color"]), true)
                );

                if ($cl["config"]["signup_conf_system"] == "phone") {
                    $insert_data['phone'] = cl_text_secure($acc_validation_data["phone"]);
                }

                else{
                    $email_code = sha1(time() + rand(111,999));
                    $insert_data['email'] = cl_text_secure($acc_validation_data["email"]);
                    $insert_data['em_code'] = $email_code;
                }

                $user_id = $db->insert(T_USERS, $insert_data);

                if (is_posnum($user_id)) {
                    
                    cl_create_user_session($user_id, 'web');

                    cl_user_auto_follow($user_id);
                    
                    $data['status'] = 200;

                    cl_db_delete_item(T_ACC_VALIDS, array(
                        "hash" => $vud_id
                    ));
                }

                if ($cl['config']['affiliates_system'] == 'on') {

                    $ref_id = cl_session('ref_id');

                    if (is_posnum($ref_id)) {
                        $ref_udata = cl_raw_user_data($ref_id);

                        if (not_empty($ref_udata)) {
                            cl_update_user_data($ref_id, array(
                                'aff_bonuses' => ($ref_udata['aff_bonuses'] += 1)
                            ));

                            cl_session_unset('ref_id');
                        }
                    }
                }
            }
        }
        else {
            $data['err_code'] = "invalid_acc_code";
            $data['status']   = 401;
        }
    }
}

else if ($action == 'resetpass') {
    $data['err_code'] = 0;
    $data['status']   = 400;
    $email_addr       = fetch_or_get($_POST['email'],null);

    if (empty($email_addr)) {
        $data['err_code'] = "invalid_email";
    } 

    else if (filter_var($email_addr, FILTER_VALIDATE_EMAIL) != true) {
        $data['err_code'] = "invalid_email";
    }

    else if (len_between($email_addr, 8, 55) != true) {
        $data['err_code'] = "invalid_email";
    }

    else {
        $email = cl_text_secure($email_addr);
        $db    = $db->where("email", $email);
        $db    = $db->where("active", "1");
        $me    = $db->getOne(T_USERS, array("password", "id", "em_code","fname","lname"));

        if (empty($me)) {
            $data['err_code'] = "unknown_email";
        }

        if (empty($data['err_code'])) { 
            $cl['me']            = $me;
            $user_id             = $me["id"];
            $email_code          = sha1(rand(11111, 99999) . $me["password"]);
            $update              = cl_update_user_data($user_id, array('em_code' => $email_code));
            $cl['me']['em_code'] = $email_code;
            $cl['me']['name']    = cl_strf("%s %s", $me['fname'], $me['lname']);
            $reset_url           = cl_strf("guest?auth=reset_pass&em_code=%s",$email_code);
            $cl['reset_url']     = cl_link($reset_url);
            $send_email_data     = array(
                'from_email'     => $cl['config']['email'],
                'from_name'      => $cl['config']['name'],
                'to_email'       => $email,
                'to_name'        => $cl['me']['name'],
                'subject'        => cl_translate("Reset your password"),
                'charSet'        => 'UTF-8',
                'is_html'        => true,
                'message_body'   => cl_template('emails/reset_password')
            ); 

            if (cl_send_mail($send_email_data)) {
                $data['status'] = 200;
            }
        }
    }
}

else if ($action == 'save_password') {
    $data['err_code']   = 0;
    $data['status']     = 400;
    $user_data_fileds   = array(
        'em_code'       => fetch_or_get($_POST['em_code'],null),
        'password'      => fetch_or_get($_POST['password'],null),
        'conf_pass'     => fetch_or_get($_POST['conf_pass'],null),
    );

    foreach ($user_data_fileds as $field_name => $field_val) {
        if ($field_name == 'em_code') {
            if (empty($field_val) || len($field_val) > 130) {
                $data['err_code'] = "invalid_emcode"; break;
            }

            else if(cl_verify_emcode($field_val) != true) {
                $data['err_code'] = "invalid_emcode"; break;
            }
        } 

        else if ($field_name == 'password') {
            if (empty($field_val) || len_between($field_val,6,20) != true) {
                $data['err_code'] = "invalid_pass"; break;
            }
        }

        else if ($field_name == 'conf_pass') {
            if (empty($field_val)) {
                $data['err_code'] = "invalid_pass"; break;
            }

            else if ($user_data_fileds['password'] != $field_val) {
                $data['err_code'] = "invalid_pass"; break;
            }
        }
    }

    $password    = cl_text_secure($user_data_fileds['password']);
    $c_password  = cl_text_secure($user_data_fileds['conf_pass']);
    $email_code  = cl_text_secure($user_data_fileds['em_code']);
    $passwd_hash = password_hash($password, PASSWORD_DEFAULT);

    if (empty($data['err_code'])) {
        $db->returnType = "Array";
        $db             = $db->where('em_code', $email_code);
        $user_id        = $db->getValue(T_USERS, "id");

        if (is_posnum($user_id)) {
            $data['status'] = 200;
            $email_code     = sha1(time() + rand(1111,9999));
            $update         = cl_update_user_data($user_id, array(
                'password'  => $passwd_hash, 
                'em_code'   => $email_code
            )); 

            cl_create_user_session($user_id);
        }
    }
}