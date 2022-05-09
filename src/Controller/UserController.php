<?php

namespace App\Controller;

use App\Entity\Client;
use App\Service\ClientGenerator;
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

    //get user's clients
    public function indexAction(Request $request)
    {
        //get current user id
        $currentUser = $this->getUser()->getId();

        //find clients that belong to the user with id current user
        $clients = $this->em->getRepository(Client::class)->findBy([
            'user'=>$currentUser
        ]);

        return $this->view($clients, Response::HTTP_OK);
    }

    //get user's specific client provided by id
    public function showAction(Request $request)
    {
        //get client from provided id
        $clientId = $request->get('clientId');
        $client = $this->em->getRepository(Client::class)->find($clientId);

        if(!$client)
        {
            throw new NotFoundHttpException('Client not found');
        }

        return $this->view($client, Response::HTTP_OK);
    }

    //create new user
    public function createAction(Request $request, ClientGenerator $clientGenerator)
    {
        //get data from request
        $data = json_decode($request->getContent(), true);
        $name = $data['name'];
        $companyRegistrationNumber = $data['company_registration_number'];
        $tin = $data['tin'];
        $website = $data['website'];
        $user = $this->getUser();

        //create new user and persist data
        //included service from Service\ClientGenerator::updateClient
        $client = $clientGenerator->createClient($user, $name, $companyRegistrationNumber, $tin, $website);
        $this->em->persist($client);
        $this->em->flush();
        return $this->handleView($this->view($client, Response::HTTP_CREATED));
    }

    //update user's client
    public function updateAction(Request $request, ClientGenerator $clientGenerator)
    {
        $currentUserId = $this->getUser()->getId();
        $clientId = $request->get('clientId');
        $client = $this->em->getRepository(Client::class)->find($clientId);

        //check if client exists
        if(!$client)
        {
            throw new NotFoundHttpException('User not found');
        }

        //check if client belong to user
        if(!($client->getUser()->getId() == $currentUserId))
        {
            throw new AccessDeniedException('Access denied');
        }

        //get data from request
        $data = json_decode($request->getContent(), true);
        $name = $data['name'];
        $companyRegistrationNumber = $data['company_registration_number'];
        $tin = $data['tin'];
        $website = $data['website'];
        $user = $this->getUser();

        //update user and persist data
        //included service from Service\ClientGenerator::updateClient
        $clientGenerator->updateClient($client, $user, $name, $companyRegistrationNumber, $tin, $website);
        return $this->handleView($this->view($client, Response::HTTP_OK));
    }

    //delete user's client
    public function deleteAction(Request $request)
    {
        $currentUserId = $this->getUser()->getId();
        $clientId = $request->get('clientId');
        $client = $this->em->getRepository(Client::class)->find($clientId);

        //check if client exists
        if(!$client)
        {
            throw new NotFoundHttpException('User not found');
        }

        //check if client belong to user
        if(!($client->getUser()->getId() == $currentUserId))
        {
            throw new AccessDeniedException('Access denied');
        }

        $this->em->remove($client);
        $this->em->flush();

        return $this->view('Client successfully deleted', Response::HTTP_OK);
    }
}
