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

if (not_empty($cl['is_logged'])) {
	$data         = array(
		'code'    => 400,
		'message' => 'You are already logged in'
	);
}
else {
    $email_addr = fetch_or_get($_POST['email'],null);

    if (empty($email_addr) || (filter_var($email_addr, FILTER_VALIDATE_EMAIL) == false) || (len_between($email_addr, 8, 55) != true)) {
        $data['code']    = 411;
        $data['message'] = "Email address is not valid";
    } 

    else {
        $email = cl_text_secure($email_addr);
        $db    = $db->where("email",$email);
        $me    = $db->getOne(T_USERS, array("password", "id", "em_code","fname","lname"));

        if (empty($me)) {
	        $data['code']    = 411;
	        $data['message'] = "Email address is unknown";
        }

        else { 
            $cl['me']            = $me;
            $user_id             = $me["id"];
            $email_code          = sha1(rand(11111, 99999) . $me["password"]);
            $update              = cl_update_user_data($user_id, array('em_code' => $email_code));
            $cl['me']['em_code'] = $email_code;
            $cl['me']['name']    = cl_strf("%s %s", $me['fname'], $me['lname']);
            $reset_url           = cl_strf("guest?em_code=%s", $email_code);
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
                $data['code']    = 411;
                $data['message'] = "Check your email for resetting your password";
                $data['data']    = array();
            }
            else {
            	$data['code']    = 500;
            	$data['message'] = "An error occurred while processing your request. Please try again later";
            }
        }
    }
}