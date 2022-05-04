<?php

namespace App\Controller;

use App\Entity\Client;
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

   #[Rest\Post('/api/clients')]
   public function postClientAction(Request $request)
   {
       $data = json_decode($request->getContent(), true);
       $name = $data['name'];
       $companyRegistrationNumber = $data['company_registration_number'];
       $tin = $data['tin'];
       $website = $data['website'];
       $addresses = $data['addresses']['id'];

       // mzd je resenje da napravim poseban query i da uzmem samo ono sto mi treba
       // mislim na adrese i slicno
       /*npr napravim niz adrese, uzmem sve adrese iz baze,
       prodjem kroz njih, nadjem one koji imaju klijentId i samo vratim ono sto mi treba*/
   }


}
