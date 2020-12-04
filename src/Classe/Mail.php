<?php

namespace App\Classe;

use Mailjet\Client;
use Mailjet\Resources;



class Mail
{
    private $api_key = "dc05ca3bb9adc20781ea2c56e037ad44";

    private $api_key_secret = "a24b1d99f7459048c254eac72d88da35";

    public function send($to_email, $to_name, $subject, $content){

        $mj =new Client($this->api_key, $this->api_key_secret, true, ['version' => 'v3.1']);
        
        $body = [
          'Messages' => [
            [
              'From' => [
                'Email' => "le-berre.jeremy@orange.fr",
                'Name'  => "Jérémy"
              ],
              'To' => [
                [
                  'Email' => $to_email,
                  'Name'  => $to_name
                ]
              ],
              'TemplateID'       => 2027302,
              'TemplateLanguage' => true,
              'Subject'          => $subject,
              'Variables'        => [
                'content' => $content
              ]
            ]
          ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        $response->success();
    }
}
 