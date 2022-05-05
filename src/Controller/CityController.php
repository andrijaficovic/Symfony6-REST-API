<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Country;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CityController extends AbstractFOSRestController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    //Retrieve all cities
    public function indexAction()
    {
        $cities = $this->em->getRepository(City::class)->findAll();
        if(!$cities)
        {
            throw new NotFoundHttpException('Cities not found');
        }
        return $this->handleView($this->view($cities, Response::HTTP_OK));
    }

    //retrieve city by id
    public function showAction(Request $request)
    {
        $cityId = $request->get('cityId');
        $city = $this->em->getRepository(City::class)->find($cityId);
        if(!$city)
        {
            throw new NotFoundHttpException('Requested city does not exist');
        }

        return $this->handleView($this->view($city, Response::HTTP_OK));
    }

    //create city record
    public function createAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $name = $data['name'];
        $countryId = $data['country_id'];
        $country = $this->em->getRepository(Country::class)->find($countryId);

        //check if name is set
        if(empty($name) || empty($countryId))
        {
            throw new BadRequestException('Fields can not be blank');
        }

        //Check if there are same cities
        $cities = $this->em->getRepository(City::class)->findAll();
        foreach ($cities as $c)
        {
            if($country->getId() === $countryId)
            {
                if(strtolower($c->getName()) === strtolower($name))
                {
                    throw new BadRequestException('That city already exists');
                }
            }
        }

        //inserting record in database
        $city = new City();
        $city->setName($name);
        $city->setCountry($country);
        $this->em->persist($city);
        $this->em->flush();

        return $this->handleView($this->view($city, Response::HTTP_CREATED));
    }


    //update city record
    public function updateAction(Request $request)
    {
        $cityId = $request->get('cityId');
        $city = $this->em->getRepository(City::class)->find($cityId);

        if(!$city)
        {
            throw new NotFoundHttpException('Requested city does not exist');
        }

        $data = json_decode($request->getContent(), true);
        $name = $data['name'];
        $countryId = $data['country_id'];
        $country = $this->em->getRepository(Country::class)->find($countryId);
        if(!$country)
        {
            throw new NotFoundHttpException('Requested country does not exist');
        }

        $city->setName($name);
        $city->setCountry($country);
        $this->em->persist($city);
        $this->em->flush();

        return $this->handleView($this->view('City successfully updated', Response::HTTP_OK));
    }

    //Delete city record
    public function deleteAction(Request $request)
    {
        $cityId = $request->get('cityId');
        $city = $this->em->getRepository(Country::class)->find($cityId);
        if(!$city)
        {
            throw new NotFoundHttpException('Requested city does not exist');
        }

        $this->em->remove($city);
        $this->em->flush();

        return $this->handleView($this->view('City successfully deleted', Response::HTTP_OK));
    }
}