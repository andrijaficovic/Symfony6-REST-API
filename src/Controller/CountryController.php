<?php

namespace App\Controller;

use App\Entity\Country;
use App\Repository\CountryRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CountryController extends AbstractFOSRestController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    //Retrieve all countries
    public function indexAction(): View
    {
        $countries = $this->em->getRepository(Country::class)->findAll();
        if($countries === null)
        {
            return new View('There are no countries exist', Response::HTTP_NOT_FOUND);
        }
        return $this->view($countries, Response::HTTP_OK);
    }

    //Retrieve country by id
    public function uniqueIndexAction($countryId):View
    {
        $country = $this->em->getRepository(Country::class)->find($countryId);
        if($country === null)
        {
            return new View('The requested country does not exist', Response::HTTP_NOT_FOUND);
        }

        return $this->view($country, Response::HTTP_OK);
    }

    #[Rest\Get('/api/countries/{id}/cities', name: 'get_cities_by_country')]
    public function getCitiesByCountryAction($id)
    {
        $country = $this->em->getRepository(Country::class)->find($id);
        if($country === null)
        {
            return new View('The requested country does not exist', Response::HTTP_NOT_FOUND);
        }
        $cities = $country->getCities();
        if($cities === null)
        {
            return new View('This country still has no cities', Response::HTTP_NOT_FOUND);
        }
        return $this->view($cities, Response::HTTP_OK);
    }

    #[Rest\Post('/api/countries', name: 'post_country')]
    public function createCountryAction(Request $request)
    {
        $countries = $this->em->getRepository(Country::class)->findAll();
        $country = new Country();
        $data = json_decode($request->getContent(), true);
        $name = $data['name'];
        //validacija da li je unutar json body strukture poslata null vrednost za name
        if(empty($name))
        {
            return new View('It is impossible to pass null data', Response::HTTP_NOT_ACCEPTABLE);
        }

        //provera da li country vec postoji u bazi podataka
        foreach ($countries as $c){
            if($c->getName() === $name)
            {
                return new View('Country already exists', Response::HTTP_CONFLICT);
            }
        }

        $country->setName($name);
        $this->em->persist($country);
        $this->em->flush();

        return $this->view('The country was successfully created', Response::HTTP_CREATED);
    }

    #[Rest\Put('/api/countries/{id}', name: 'put_country')]
    public function updateCountryAction($id, Request $request)
    {
        $country = $this->em->getRepository(Country::class)->find($id);
        if($country === null)
        {
            return new View('The requested country does not exist', Response::HTTP_NOT_FOUND);
        }
        $data = json_decode($request->getContent(), true);
        $name = $data['name'];

        $country->setName($name);
        $this->em->persist($country);
        $this->em->flush();

        return $this->view('Country updated successfully', Response::HTTP_OK);
    }

    #[Rest\Delete('/api/countries/{id}', name: 'delete_country')]
    public function deleteCountryAction($id)
    {
        $country = $this->em->getRepository(Country::class)->find($id);
        if($country === null)
        {
            return new View('The requested result does not exist', Response::HTTP_NOT_FOUND);
        }

        $this->em->remove($country);
        $this->em->flush();

        return $this->view('Deleted successfully', Response::HTTP_OK);
    }
}
