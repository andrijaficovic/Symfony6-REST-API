<?php

namespace App\Listeners;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\HttpFoundation\Cookie;

class AuthSuccessListener
{
    private $secure = false;

    public function onAuthSuccess(AuthenticationSuccessEvent $event)
    {
        $response = $event->getResponse();
        $data = $event->getData();
        $token = $data['token'];
        unset($data['token']);
        $event->setData($data);

        $response->headers->setCookie(new Cookie(name: 'BEARER', value: $token));
    }
}