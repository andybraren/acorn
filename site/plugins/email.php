<?php

/* Mail Plugin
   Because the default one doesn't allow the use of HTML in email bodies
   This is also simpler because I don't need the key to be everywhere
   Mailgun adapter here: https://github.com/getkirby/toolkit/blob/master/lib/email.php
   Adaptation inspired from this: https://forum.getkirby.com/t/is-there-a-way-to-send-html-emails/504
*/


// Sparkpost
// https://gist.github.com/cmpscabral/b0945fecfdea14e88869769dc15b7484

email::$services['sparkpost'] = function($email) {
  
  $url = 'https://api.sparkpost.com/api/v1/transmissions';
  
  function get_domain($url)
  {
        $urlobj=parse_url($url);
        $domain=$urlobj['host'];
        if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
          return $regs['domain'];
        }
        return false;
  }

  $domain = 'robot@' . get_domain(site()->url());
  
  $headers = array(
    'Authorization: ' . yaml(site()->settings())['connections']['sparkpost']['key'],
    'Content-Type: application/json',
  );
  
	$data = array(
		'options' => array(
			'sandbox' => false,
		),
		'content' => array(
			'from' => array(
  			'email' => $domain,
			  'name' => site()->title() . ' Robot',
			),
			'subject' => $email->subject,
			'html' => $email->body,
		),
		'recipients' => array(
			array('address' => $email->to),
		),
	);
  
  //open connection
  $ch = curl_init();
  
  //set the url, number of POST vars, POST data
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
  
  //execute post
  $response = curl_exec($ch);
  $err    = curl_error($ch);
  
  //close connection
  curl_close($ch);
  
	if ($err) {
	  $status = 'error';
	  $result = $err;
	} else {
	  $status = 'ok';
	  $result = $response;
	}
	
	return array('status'=>$status,'result'=>$result);
  
};

/*
email::$services['html_email'] = function($email) {
  
  $mailgunKey = yaml(site()->settings())['connections']['mailgun']['key'];
  $mailgunDomain = yaml(site()->settings())['connections']['mailgun']['domain'];
  
  if(empty($mailgunKey))    throw new Error('Missing Mailgun API key');
  if(empty($mailgunDomain)) throw new Error('Missing Mailgun API domain');
  $url  = 'https://api.mailgun.net/v2/' . $mailgunDomain . '/messages';
  $auth = base64_encode('api:' . $mailgunKey);
  
  $headers = array(
    'Accept: application/json',
    'Authorization: Basic ' . $auth,
  );
  
  $data = array(
    'from'       => 'Tufts Maker Network <happyrobot@maker.tufts.edu>',
    'to'         => $email->to,
    'subject'    => $email->subject,
    'html'       => $email->body,
    'h:Reply-To' => $email->replyTo,
  );

  $email->response = remote::post($url, array(
    'headers' => $headers,
    'data'    => $data,
  ));
  
  if($email->response->code() != 200) {
    throw new Error('The mail could not be sent!');
  }
};
*/

?>