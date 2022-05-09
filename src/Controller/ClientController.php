<?php

namespace App\Controller;

use App\Entity\Client;
use App\Service\ClientGenerator;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ClientController extends AbstractFOSRestController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /*
     * Retrieve all clients, only provided for admin user
     * You can find all related clients for specific user:
     * check route /api/user/clients in UserController::indexAction
     * */
    public function indexAction()
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $clients = $this->em->getRepository(Client::class)->findAll();
        if(!$clients)
        {
            throw new NotFoundHttpException('Clients not found');
        }
        return $this->handleView($this->view($clients, Response::HTTP_OK));
    }

    /*
     * Retrieve client by id, only provided for admin user
     * You can find specific client:
     * check route /api/user/clients/{clientId} in UserController::showAction
     * */
    public function showAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $clientId = $request->get('clientId');
        $client = $this->em->getRepository(Client::class)->find($clientId);
        if(!$client)
        {
            throw new NotFoundHttpException('Requested client does not exist');
        }

        return $this->handleView($this->view($client, Response::HTTP_OK));
    }

    /*
     * Create new client, only provided for admin user
     * User can create client:
     * check route /api/user/clients in UserController::createAction
     * */
    public function createAction(Request $request, ClientGenerator $clientGenerator)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
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

    /*
     * Update client by id, only provided for admin user
     * You can update specific client:
     * check route /api/user/clients/{clientId} in UserController::updateAction
     * */
    public function updateAction(Request $request, ClientGenerator $clientGenerator)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $clientId = $request->get('clientId');
        $client = $this->em->getRepository(Client::class)->find($clientId);

        if(!$client)
        {
            throw new NotFoundHttpException('Requested client does not exist');
        }

        $data = json_decode($request->getContent(), true);
        $name = $data['name'];
        $companyRegistrationNumber = $data['company_registration_number'];
        $tin = $data['tin'];
        $website = $data['website'];
        $user = $this->getUser();

        //included service from Service\ClientGenerator::updateClient
        $clientGenerator->updateClient($client, $user, $name, $companyRegistrationNumber, $tin, $website);
        return $this->handleView($this->view($client, Response::HTTP_CREATED));
    }

    //Delete address record
    public function deleteAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $clientId = $request->get('clientId');
        $client = $this->em->getRepository(Client::class)->find($clientId);

        if(!$client)
        {
            throw new NotFoundHttpException('Requested client does not exist');
        }

        $this->em->remove($client);
        $this->em->flush();

        return $this->handleView($this->view('Client successfully deleted', Response::HTTP_OK));
    }
}
