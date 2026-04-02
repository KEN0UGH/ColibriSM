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

require_once(cl_full_path("core/libs/configs/paypal.php"));

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $paypal_req_url . '/v2/checkout/orders');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json(array(
	"intent" => "CAPTURE",
	"purchase_units" => array(
		array(
			"items" => array(
				array(
					"name" => cl_translate('Top up your account balance'),
					"description" => cl_translate('Pay to: {@site_name@}', array('site_name' => $cl['config']['name'])),
					"quantity" => 1,
					"unit_amount" => array(
						"currency_code" => ($cl['config']['site_currency']),
						"value" => $topup_amount
					)
				)
			),
			"amount" => array(
				"currency_code" => ($cl['config']['site_currency']),
				"value" => $topup_amount,
				"breakdown" => array(
					"item_total" => array(
						"currency_code" => ($cl['config']['site_currency']),
						"value" => $topup_amount
					)
				)
			)
		)
	),
	"application_context" => array(
		"shipping_preference" => "NO_SHIPPING",
		"return_url" => cl_link("native_api/wallet/pgw1_wallet_tup_success"),
		"cancel_url" => cl_link("native_api/wallet/pgw1_wallet_tup_cancel")
	)
), true));

$headers = array();
$headers[] = 'Content-Type: application/json';
$headers[] = 'Authorization: Bearer '. $cl['paypal_access_token'];
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);
if (curl_errno($ch)) {
    $data["err_code"] = curl_error($ch);
}

curl_close($ch);
$result = json_decode($result);

if (not_empty($result) && not_empty($result->links) && not_empty($result->links[1]) && not_empty($result->links[1]->href)) {
    $data = array(
        "status" => 200,
        "type" => "SUCCESS",
        'url' => $result->links[1]->href
    );
}
elseif(not_empty($result->message)){
    $data = array(
        "status" => 400,
        'err_code' => 'request_error',
        'err_message' => $result->message
    );
}