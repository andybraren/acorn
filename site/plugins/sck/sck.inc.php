<?php
  require_once('stripe-php/init.php');

  if (c::get('stripe_test_mode')) {
    $pk = c::get('stripe_test_publishable_key');
    $sk = c::get('stripe_test_secret_key');
  } else {
    $pk = c::get('stripe_live_publishable_key');
    $sk = c::get('stripe_live_secret_key');
    }

  $stripe = array(
    "publishable_key" => $pk,
    "secret_key"      => $sk
  );

  \Stripe\Stripe::setApiKey($stripe['secret_key']);

?>