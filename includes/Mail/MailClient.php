<?php

use Http as Http;
use Http\HttpHeader as HttpHeader; 
use Http\AcceptHeader as AcceptHeader; 
use Http\HttpResponse as HttpResponse;
use Http\HttpRequest as HttpRequest;
use Http\HttpHeaderCollection;






class MailClient {


    // in_array($class,["MailMessage","MailMessageList"]) ? $this->sendMail($message) : 
    public static function sendMail(MailMessageList $list) {

        $statuses = array();


        foreach($list->getMessages() as $message) {
            // $template = new Template("email");
            // $template->addPath(get_theme_path());
            /*$body = $template->render(array(
                "content" => $message->getBody(),
                "title" => $message->getTitle()
            ));
            */

            $statuses[] = mail(
                $message->getTo(),
                $message->getSubject(),
                $message->getBody(),
                $message->getHeaders()
            );
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


        /*
                    else if(count($list->getMessages()) == 0) {
                $req = new HttpRequest("No emails to send.");
                $req->addHeader(new HttpHeader("Accept","text/html"));
                $req->addHeader(new HttpHeader("Request-URI","system/status/No emails to send."));
                return $req;
            }
            */
        return $statuses;
    }



}