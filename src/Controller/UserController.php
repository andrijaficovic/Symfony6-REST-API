<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserController extends AbstractFOSRestController
{
    private $em;

    /**
     * @param $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function indexAction(Request $request)
    {
        $userId = $request->get('userId');

        $user = $this->em->getRepository(User::class)->find($userId);
        if(!$user)
        {
            throw new NotFoundHttpException('User not found');
        }

    }
}
