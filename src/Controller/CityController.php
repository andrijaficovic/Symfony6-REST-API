<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Country;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;

class CityController extends AbstractFOSRestController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Rest\Get('/api/cities', name: 'get_cities')]
    public function getCitiesAction()
    {
        $cities = $this->em->getRepository(City::class)->findAll();
        if($cities === null)
        {
            return new View('There are no cities exist', Response::HTTP_NOT_FOUND);
        }
        return $this->view($cities, Response::HTTP_OK);
    }

    #[Rest\Get('/api/cities/{id}', name: 'get_city')]
    public function getCityAction($id)
    {
        $city = $this->em->getRepository(City::class)->find($id);
        if($city === null)
        {
            return new View('The requested result does not exist', Response::HTTP_NOT_FOUND);
        }

        return $this->view($city, Response::HTTP_OK);
    }

    #[Rest\Get('/api/cities/country/{id}', name: 'get_cities_by_country2')]
    public function getCitiesByCountryAction($id)
    {
        $country = $this->em->getRepository(Country::class)->find($id);
        $cities = $country->getCities();
        if($cities === null)
        {
            return new View('This country still has no cities', Response::HTTP_NOT_FOUND);
        }
        return $this->view($cities, Response::HTTP_OK);
    }

    #[Rest\Post('/api/cities', name: 'post_city')]
    public function createCityAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $name = $data['name'];
        $countryId = $data['country']['id'];
        $country = $this->em->getRepository(Country::class)->find($countryId);

        if(empty($name) || $country === null)
        {
            return new View('It is impossible to pass null data', Response::HTTP_NOT_ACCEPTABLE);
        }

        $city = new City();
        $city->setName($name);
        $city->setCountry($country);


        $this->em->persist($city);
        $this->em->flush();

        return $this->view('The city was successfully created', Response::HTTP_CREATED);
    }

    #[Rest\Put('/api/cities/{id}', name: 'update_city')]
    public function updateCityAction($id, Request $request)
    {
        $city = $this->em->getRepository(City::class)->find($id);
        if($city === null)
        {
            return new View('The requested result does not exist', Response::HTTP_NOT_FOUND);
        }
        $data = json_decode($request->getContent(), true);
        $name = $data['name'];
        $countryId = $data['country']['id'];
        $country = $this->em->getRepository(Country::class)->find($countryId);


        $city->setName($name);
        $city->setCountry($country);
        $this->em->persist($city);
        $this->em->flush();

        return $this->view('City updated successfully', Response::HTTP_OK);

    }

    #[Rest\Delete('/api/cities/{id}', name: 'delete_city')]
    public function deleteCityAction($id)
    {
        $city = $this->em->getRepository(City::class)->find($id);
        if($city === null)
        {
            return new View('The requested result does not exist', Response::HTTP_NOT_FOUND);
        }

        $this->em->remove($city);
        $this->em->flush();

        return $this->view('Deleted successfully', Response::HTTP_OK);
    }
}