<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Contact;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ContactController extends AbstractFOSRestController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Rest\Get('/api/contacts', name: 'get_contacts')]
    public function getContactsAction()
    {
        $contacts = $this->em->getRepository(Contact::class)->findAll();
        if($contacts === null)
        {
            return new View('There are no contacts exist', Response::HTTP_NOT_FOUND);
        }

        return $this->view($contacts, Response::HTTP_OK);
    }

    #[Rest\Get('/api/contacts/{id}', name: 'get_contact')]
    public function getContactAction($id)
    {
        $contact = $this->em->getRepository(Contact::class)->find($id);
        if($contact === null)
        {
            return new View('The requested result does not exist', Response::HTTP_NOT_FOUND);
        }

        return $this->view($contact, Response::HTTP_OK);
    }

    #[Rest\Post('/api/contacts', name: 'post_contact')]
    public function postContactAction(Request $request)
    {
        $contact = new Contact();
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

        $contact->setName($name);
        $contact->setSurname($surname);
        $contact->setPhoneNumber($phoneNumber);
        $contact->setEmail($email);
        $contact->setClient($client);
        $this->em->persist($contact);
        $this->em->flush();


        return $this->view('The contact was successfully created', Response::HTTP_CREATED);
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
