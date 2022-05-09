<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
        $currentUser = $this->getUser()->getId();
        if(!($userId == $currentUser))
        {
            throw new AccessDeniedException('Access denied', Response::HTTP_CONFLICT);
        }

        $clients = $this->em->getRepository(Client::class)->findBy([
            'user'=>$userId
        ]);

        return $this->view($clients);
    }
}
