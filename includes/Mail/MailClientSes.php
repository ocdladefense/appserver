<?php

use Http as Http;
use Http\HttpHeader as HttpHeader; 
use Http\AcceptHeader as AcceptHeader; 
use Http\HttpResponse as HttpResponse;
use Http\HttpRequest as HttpRequest;
use Http\HttpHeaderCollection;
use Aws\Ses\SesClient;
use Aws\Exception\AwsException;



// https://github.com/awsdocs/aws-doc-sdk-examples/tree/main/php/example_code
class MailClientSes {


    // Create an SesClient. Change the value of the region parameter if you're 
    // using an AWS Region other than US West (Oregon). Change the value of the
    // profile parameter if you want to use a profile in your credentials file
    // other than the default.


    // Replace sender@example.com with your "From" address.
    // This address must be verified with Amazon SES.
    private static $sender_email = 'admin@ocdla.org';

    // Replace these sample addresses with the addresses of your recipients. If
    // your account is still in the sandbox, these addresses must be verified.
    // $recipient_emails = ['recipient1@example.com','recipient2@example.com'];
    /*$recipient_emails = ['jbernal.web.dev@gmail.com'];
    // Specify a configuration set. If you do not want to use a configuration
    // set, comment the following variable, and the
    // 'ConfigurationSetName' => $configuration_set argument below.
    // $configuration_set = 'ConfigSet';

    $subject = 'Amazon SES test (AWS SDK for PHP)';
    $plaintext_body = 'This email was sent with Amazon SES using the AWS SDK for PHP.' ;
    $html_body =  '<h1>AWS Amazon Simple Email Service Test Email</h1>'.
                '<p>This email was sent with <a href="https://aws.amazon.com/ses/">'.
                'Amazon SES</a> using the <a href="https://aws.amazon.com/sdk-for-php/">'.
                'AWS SDK for PHP</a>.</p>';
                */
    private static $char_set = 'UTF-8';






    // in_array($class,["MailMessage","MailMessageList"]) ? $this->sendMail($message) : 
    public static function sendMail(MailMessageList $list) {


        $SesClient = new SesClient([
            //'profile' => 'default',
            'version' => 'latest',
            'region'  => 'us-west-2'
        ]);

        
        $statuses = array();


        foreach($list->getMessages() as $message) {



            try {
                $result = $SesClient->sendEmail([
                    'Destination' => [
                        'ToAddresses' => [$message->getTo()],
                        'CcAddresses' => ['jroot@ocdla.org', 'info@ocdla.org']
                    ],
                    'ReplyToAddresses' => [self::$sender_email],
                    'Source' => self::$sender_email,
                    'Message' => [
                    'Body' => [
                        'Html' => [
                            'Charset' => self::$char_set,
                            'Data' => $message->getBody(),
                        ],
                        'Text' => [
                            'Charset' => self::$char_set,
                            'Data' => '',
                        ],
                    ],
                    'Subject' => [
                        'Charset' => self::$char_set,
                        'Data' => $message->getSubject(),
                    ],
                    ],
                    // If you aren't using a configuration set, comment or delete the
                    // following line
                    // 'ConfigurationSetName' => $configuration_set,
                ]);
                
                // $messageId = $result['MessageId'];
                $statuses[] = true;
            } catch (AwsException $e) {
                // output error message if fails
                $statuses[] = false;
                // echo $e->getMessage();
                // echo("The email was not sent. Error message: ".$e->getAwsErrorMessage()."\n");
                // echo "\n";
            }
        }

        foreach($statuses as $sent) {
            if($sent)
            {
                $req = new HttpRequest("Your email was sent.");
                $req->addHeader(new HttpHeader("Accept","text/html"));
                $req->addHeader(new HttpHeader("Request-URI","system/status/Your email was sent."));
                
            }

            else
            {
                $resp = new HttpResponse("Your email was not sent");
                $resp->setStatusCode(500);
            }
        }


        return $statuses;
    }



}