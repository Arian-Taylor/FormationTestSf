<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="app_contact")
     */
    public function index(
        \Swift_Mailer $mailer, 
        Request $request
    ): Response
    {

        $twig_param = [] ;

        if ($request->isMethod("POST")) {
            $errors = [];
            $values = [];
            $username = $request->request->get("username");
            $useremail = $request->request->get("useremail");
            $subject = $request->request->get("subject");
            $from = $request->request->get("from");
            $sms = $request->request->get("message");

            $values["username"] = $username ;
            $values["useremail"] = $useremail ;
            $values["subject"] = $subject ;
            $values["sms"] = $sms ;

            if (!$username) {
                $errors["username"] = "Invalid username" ;
            }
            if (!$subject) {
                $errors["subject"] = "Invalid subject" ;
            }
            if (
                !$useremail || 
                !filter_var($useremail, FILTER_VALIDATE_EMAIL)
            ) {
                $errors["useremail"] = "Invalid useremail" ;
            }

            $twig_param["values"] = $values ;

            if ($errors) {
                $twig_param["errors"] = $errors ;
            } else {
                $message = (new \Swift_Message($subject))
                    ->setFrom("app@app.com")
                    ->setTo("contact@app.com")
                    ->setBody($sms);

                $mailer->send($message) ;

                $this->addFlash(
                    'succes',
                    'An email sended!'
                );
            }
        }

        return $this->render('contact/index.html.twig', $twig_param);
    }
}
