<?php

namespace App\Controller;

use App\Entity\Country;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CountryController extends AbstractFOSRestController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    //Retrieve all countries
    public function indexAction()
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $countries = $this->em->getRepository(Country::class)->findAll();
        if(!$countries)
        {
            throw new NotFoundHttpException('Countries not found');
        }
        return $this->handleView($this->view($countries, Response::HTTP_OK));
    }

    //Retrieve country by id
    public function showAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $countryId = $request->get('countryId');
        $country = $this->em->getRepository(Country::class)->find($countryId);
        if($country === null)
        {
            throw new NotFoundHttpException('Requested country does not exist');
        }

        return $this->handleView($this->view($country, Response::HTTP_OK));
    }

    //create country record
    public function createAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $data = json_decode($request->getContent(), true);
        $name = $data['name'];
        $country = new Country();

        //check if name is set
        if(empty($name))
        {
            throw new BadRequestException('Field name can not be blank');
        }

        //Check if there are same countries
        $countries = $this->em->getRepository(Country::class)->findAll();
        foreach ($countries as $c)
        {
            if(strtolower($c->getName()) === strtolower($name))
            {
                throw new BadRequestException('That country already exists');
            }
        }

        //inserting record in database
        $country->setName($name);
        $this->em->persist($country);
        $this->em->flush();

        return $this->handleView($this->view($country, Response::HTTP_CREATED));
    }

    //update country record
    public function updateAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $countryId = $request->get('countryId');
        $country = $this->em->getRepository(Country::class)->find($countryId);

        if(!$country)
        {
            throw new NotFoundHttpException('Requested country does not exist');
        }

        $data = json_decode($request->getContent(), true);
        $name = $data['name'];
        $country->setName($name);
        $this->em->persist($country);
        $this->em->flush();

        return $this->handleView($this->view('Country successfully updated', Response::HTTP_OK));
    }

    //Delete country record
    public function deleteAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $countryId = $request->get('countryId');
        $country = $this->em->getRepository(Country::class)->find($countryId);
        if(!$country)
        {
            throw new NotFoundHttpException('Requested country does not exist');
        }

        $this->em->remove($country);
        $this->em->flush();

        return $this->handleView($this->view('Country successfully deleted', Response::HTTP_OK));
    }
}
