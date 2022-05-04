<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\Client;
use App\Entity\Contact;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
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

    #[Rest\Get('/api/clients', name: 'get_clients')]
   public function getClientsAction()
   {
        $clients = $this->em->getRepository(Client::class)->findAll();
        if($clients === null)
        {
            return new View('There are no clients exist', Response::HTTP_NOT_FOUND);
        }

        return $this->view($clients,Response::HTTP_OK);
   }

   #[Rest\Get('/api/clients/{id}', name: 'get_client')]
   public function getClientAction($id)
   {
       $client = $this->em->getRepository(Client::class)->find($id);
       if($client === null)
       {
           return new View('The requested result does not exist', Response::HTTP_NOT_FOUND);
       }

       return $this->view($client,Response::HTTP_OK);
   }

//   #[Rest\Post('/api/clients')]
//   public function postClientAction(Request $request)
//   {
//       $data = json_decode($request->getContent(), true);
//       $name = $data['name'];
//       $companyRegistrationNumber = $data['company_registration_number'];
//       $tin = $data['tin'];
//       $website = $data['website'];
//       $numAddr = count($data['addresses']);
//       $numCont = count($data['contacts']);
//       $addressesIds = [];
//       $contactsIds = [];
//       if($numAddr === 1)
//       {
//           $addressesIds = $data['addresses']['id'];
//       }else{
//           foreach ($data['addresses'] as $address)
//           {
//               array_push($addressesIds, $address['id']);
//           }
//       }
//
//       if($numCont === 1)
//       {
//           $contactsIds = $data['contacts']['id'];
//       }else{
//           foreach ($data['contacts'] as $contact)
//           {
//               array_push($contactsIds, $contact['id']);
//           }
//       }
//
//       $client = new Client();
//       $client->setName($name);
//       $client->setWebsite($website);
//       $client->setTin($tin);
//       $client->setCompanyRegistrationNumber($companyRegistrationNumber);
//       foreach ($addressesIds as $addressId)
//       {
//           $findAddress = $this->em->getRepository(Address::class)->find($addressId);
//           $client->addAddress($findAddress);
//       }
//
//       foreach ($contactsIds as $contactId)
//       {
//           $findContact = $this->em->getRepository(Contact::class)->find($contactId);
//           $client->addContact($findContact);
//       }
//
//   }

    #[Rest\Post('/api/clients')]
    public function createClientsAction(Request $request)
    {

    }
}
