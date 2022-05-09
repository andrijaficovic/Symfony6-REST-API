<?php

namespace App\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends AbstractFOSRestController
{
    public function index(Request $request)
    {
        $user = $this->getUser();
        if(null === $user)
        {
            return $this->handleView($this->view('missing credentials'),Response::HTTP_UNAUTHORIZED);
        }

        $token = $request->get('lexik_jwt_authentication.encoder');
        return $this->handleView($this->view([
            'email'=>$user->getUserIdentifier(),
            'token'=>$token
        ]));
    }
}
