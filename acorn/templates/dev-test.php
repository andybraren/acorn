<main class="main">
  <div class="container">

Hello

<?php

try {
  $email = email(array(
    'service' => 'phpmailer',
    'from'    => 'happyrobot@maker.tufts.edu',
    'subject' => 'New photo from the Maker Studio',
    'attachment' => '/var/www/drewbaren.com/maker/content/makers/abraren/hero.jpg',
    'body'    => '
      <html>
        <head>
          <title>New photo from the Maker Studio</title>
        </head>
        <body>
          <p>The photo you just snapped from the Maker Studio has been added to your Maker Profile.</p>
          <p>- Maker Network Email Robot</p>
        </body>
      </html>
    '
  ));
  
  // Multiple recipients
  // https://forum.getkirby.com/t/send-e-mail-to-multiple-receipients/2903/6
  //foreach(array('andybraren@gmail.com','tuftsceeohelp@gmail.com') as $to) {
  foreach(array('andybraren@gmail.com') as $to) {
    $result = $email->send(array('to' => $to));
  }
  
} catch(Exception $e) {
  echo $e->getMessage();
}


?>

  </div>
</main>

<?php snippet('footer') ?>