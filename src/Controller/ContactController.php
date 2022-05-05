<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Contact;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ContactController extends AbstractFOSRestController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    //Retrieve all contact
    public function indexAction()
    {
        $contacts = $this->em->getRepository(Contact::class)->findAll();
        if(!$contacts)
        {
            throw new NotFoundHttpException('Contacts not found');
        }
        return $this->handleView($this->view($contacts, Response::HTTP_OK));
    }

    //retrieve contact by id
    public function showAction(Request $request)
    {
        $contactId = $request->get('contactId');
        $contact = $this->em->getRepository(Contact::class)->find($contactId);
        if(!$contact)
        {
            throw new NotFoundHttpException('Requested contact does not exist');
        }

        return $this->handleView($this->view($contact, Response::HTTP_OK));
    }

    //create contact record
    public function createAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $name = $data['name'];
        $surname = $data['surname'];
        $phoneNumber = $data['phone_number'];
        $email = $data['email'];
        $clientId = $data['client_id'];

        $client = $this->em->getRepository(Client::class)->find($clientId);

        if(!$client)
        {
            throw new NotFoundHttpException('Requested client does not exist');
        }

        //check if data is set
        if(empty($name) || empty($surname) || empty($phoneNumber) || empty($email) || empty($clientId))
        {
            throw new BadRequestException('Fields can not be blank');
        }

        //inserting record in database
        $contact = new Contact();
        $contact->setName($name);
        $contact->setSurname($surname);
        $contact->setPhoneNumber($phoneNumber);
        $contact->setEmail($email);
        $contact->setClient($client);
        $this->em->persist($contact);
        $this->em->flush();

        return $this->handleView($this->view($contact, Response::HTTP_CREATED));
    }

    //update contact record
    public function updateAction(Request $request)
    {
        $contactId = $request->get('contactId');
        $contact = $this->em->getRepository(Contact::class)->find($contactId);

        if(!$contact)
        {
            throw new NotFoundHttpException('Requested contact does not exist');
        }

        $data = json_decode($request->getContent(), true);
        $name = $data['name'];
        $surname = $data['surname'];
        $phoneNumber = $data['phone_number'];
        $email = $data['email'];
        $clientId = $data['client_id'];

        $client = $this->em->getRepository(Client::class)->find($clientId);
        if(!$client)
        {
            throw new NotFoundHttpException('Requested client does not exist');
        }

        //check if data is set
        if(empty($name) || empty($surname) || empty($phoneNumber) || empty($email) || empty($clientId))
        {
            throw new BadRequestException('Fields can not be blank');
        }

        //inserting record in database
        $contact = new Contact();
        $contact->setName($name);
        $contact->setSurname($surname);
        $contact->setPhoneNumber($phoneNumber);
        $contact->setEmail($email);
        $contact->setClient($client);
        $this->em->persist($contact);
        $this->em->flush();

        return $this->handleView($this->view('Contact successfully updated', Response::HTTP_OK));
    }

    //Delete contact record
    public function deleteAction(Request $request)
    {
        $contactId = $request->get('contactId');
        $contact = $this->em->getRepository(Contact::class)->find($contactId);
        if(!$contact)
        {
            throw new NotFoundHttpException('Requested contact does not exist');
        }

        $this->em->remove($contact);
        $this->em->flush();

        return $this->handleView($this->view('Contact successfully deleted', Response::HTTP_OK));
    }
}
