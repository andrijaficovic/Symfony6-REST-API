<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\Client;
use App\Entity\Contact;
use App\Form\ClientType;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
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

    //Retrieve all clients
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

    //retrieve client by id
    public function showAction(Request $request)
    {
        $clientId = $request->get('clientId');
        $client = $this->em->getRepository(Client::class)->find($clientId);
        if(!$client)
        {
            throw new NotFoundHttpException('Requested client does not exist');
        }

        $clientUser = $client->getUser()->getId();
        $currentUser = $this->getUser()->getId();
        if($clientUser === $currentUser)
        {
            return $this->handleView($this->view($client, Response::HTTP_OK));
        }else{
            throw new AccessDeniedException('Access denied', Response::HTTP_CONFLICT);
        }
    }

    //create client record
    public function createAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $name = $data['name'];
        $companyRegistrationNumber = $data['company_registration_number'];
        $tin = $data['tin'];
        $website = $data['website'];
        $user = $this->getUser();
        //check if data is set
        //website can be null
        if(empty($name) || empty($companyRegistrationNumber) || empty($tin))
        {
            throw new BadRequestException('Fields can not be blank');
        }

        //inserting record in database
        $client = new Client();
        $client->setName($name);
        $client->setCompanyRegistrationNumber($companyRegistrationNumber);
        $client->setTin($tin);
        $client->setWebsite($website);
        $client->setUser($user);
        $this->em->persist($client);
        $this->em->flush();

        return $this->handleView($this->view($client, Response::HTTP_CREATED));
    }

    //update client record
    public function updateAction(Request $request)
    {
        $clientId = $request->get('clientId');
        $client = $this->em->getRepository(Client::class)->find($clientId);

        $clientUser = $client->getUser()->getId();
        $currentUser = $this->getUser()->getId();
        if(!($clientUser === $currentUser))
        {
            throw new AccessDeniedException('Access denied', Response::HTTP_CONFLICT);
        }

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
        //check if data is set
        //website can be null
        if(empty($name) || empty($companyRegistrationNumber) || empty($tin))
        {
            throw new BadRequestException('Fields can not be blank');
        }

        //inserting record in database
        $client->setName($name);
        $client->setCompanyRegistrationNumber($companyRegistrationNumber);
        $client->setTin($tin);
        $client->setWebsite($website);
        $client->setUser($user);
        $this->em->persist($client);
        $this->em->flush();

        return $this->handleView($this->view($client, Response::HTTP_CREATED));
    }

    //Delete address record
    public function deleteAction(Request $request)
    {
        $clientId = $request->get('clientId');
        $client = $this->em->getRepository(Client::class)->find($clientId);
        if(!$client)
        {
            throw new NotFoundHttpException('Requested client does not exist');
        }
        $clientUser = $client->getUser()->getId();
        $currentUser = $this->getUser()->getId();
        if(!($clientUser === $currentUser))
        {
            throw new AccessDeniedException('Access denied', Response::HTTP_CONFLICT);
        }

        $this->em->remove($client);
        $this->em->flush();

        return $this->handleView($this->view('Client successfully deleted', Response::HTTP_OK));
    }
}
