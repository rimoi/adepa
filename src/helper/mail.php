<?php
  require 'vendor/autoload.php';
  use Mailjet\Client;
  use \Mailjet\Resources;
  class Mail{

    public function send(){
        $mj = new Client('****************************1234','****************************abcd',true,['version' => 'v3.1']);
        $body = [
          'Messages' => [
            [
              'From' => [
                'Email' => "hozepha.abdoulalybarmal@gmail.com",
                'Name' => "Hozepha"
              ],
              'To' => [
                [
                  'Email' => "hozepha.abdoulalybarmal@gmail.com",
                  'Name' => "Hozepha"
                ]
              ],
              'Subject' => "Greetings from Mailjet.",
              'TextPart' => "My first Mailjet email",
              'HTMLPart' => "<h3>Dear passenger 1, welcome to <a href='https://www.mailjet.com/'>Mailjet</a>!</h3><br />May the delivery force be with you!",
              'CustomID' => "AppGettingStartedTest"
            ]
          ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body], ['timeout' => 1]);
        $response->success() && var_dump($response->getData());
    }

  }

?>