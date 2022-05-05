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
        $city = new City();
        $city->setName($name);
        $city->setCountry($country);
        $this->em->persist($city);
        $this->em->flush();

        return $this->handleView($this->view($city, Response::HTTP_CREATED));
    }

    #[Rest\Put('/api/contacts/{id}', name: 'update_contact')]
    public function updateContactAction($id, Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $name = $data['name'];
        $surname = $data['surname'];
        $phoneNumber = $data['phone_number'];
        $email = $data['email'];
        $clientId = $data['client']['id'];
        $client = $this->em->getRepository(Client::class)->find($clientId);

        if(empty($name) || empty($surname) || empty($phoneNumber) || empty($email) || $client === null)
        {
            return new View('It is impossible to pass null data', Response::HTTP_NOT_ACCEPTABLE);
        }

        $contact = $this->em->getRepository(Contact::class)->find($id);
        $contact->setName($name);
        $contact->setSurname($surname);
        $contact->setPhoneNumber($phoneNumber);
        $contact->setEmail($email);
        $contact->setClient($client);
        $this->em->persist($contact);
        $this->em->flush();

        return $this->view('Contact updated successfully', Response::HTTP_OK);
    }

    #[Rest\Delete('/api/contacts/{id}', name: 'delete_contact')]
    public function deleteContactAction($id)
    {
        $contact = $this->em->getRepository(Contact::class)->find($id);

        if($contact === null)
        {
            return new View('The requested result does not exist', Response::HTTP_NOT_FOUND);
        }

        $this->em->remove($contact);
        $this->em->flush();

        return $this->view('Deleted successfully', Response::HTTP_OK);
    }
}
