<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\City;
use App\Entity\Client;
use App\Entity\Country;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AddressController extends AbstractFOSRestController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    //Retrieve all addresses
    public function indexAction()
    {
        $addresses = $this->em->getRepository(Address::class)->findAll();
        if(!$addresses)
        {
            throw new NotFoundHttpException('Addresses not found');
        }
        return $this->handleView($this->view($addresses, Response::HTTP_OK));
    }

    //retrieve address by id
    public function showAction(Request $request)
    {
        $addressId = $request->get('addressId');
        $address = $this->em->getRepository(Address::class)->find($addressId);
        if(!$address)
        {
            throw new NotFoundHttpException('Requested address does not exist');
        }

        return $this->handleView($this->view($address, Response::HTTP_OK));
    }

    //create address record
    public function createAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $street = $data['street'];
        $streetNumber = $data['street_number'];
        $postalCode = $data['postal_code'];
        $cityId = $data['city_id'];
        $countryId = $data['country_id'];
        $clientId = $data['client_id'];

        $city = $this->em->getRepository(City::class)->find($cityId);
        $country = $this->em->getRepository(Country::class)->find($countryId);
        $client = $this->em->getRepository(Client::class)->find($clientId);

        if(!$client)
        {
            throw new NotFoundHttpException('Requested client does not exist');
        }

        if(!$city)
        {
            throw new NotFoundHttpException('Requested city does not exist');
        }

        if(!$country)
        {
            throw new NotFoundHttpException('Requested country does not exist');
        }

        //check if data is set
        if(empty($street) || empty($streetNumber) || empty($postalCode) || empty($cityId) || empty($countryId) || empty($clientId))
        {
            throw new BadRequestException('Fields can not be blank');
        }

        //inserting record in database
        $address = new Address();
        $address->setStreet($street);
        $address->setStreetNumber($streetNumber);
        $address->setPostalCode($postalCode);
        $address->setCity($city);
        $address->setCountry($country);
        $address->setClient($client);
        $this->em->persist($address);
        $this->em->flush();

        return $this->handleView($this->view($address, Response::HTTP_CREATED));
    }

    //update address record
    public function updateAction(Request $request)
    {
        $addressId = $request->get('addressId');
        $address = $this->em->getRepository(Address::class)->find($addressId);

        if(!$address)
        {
            throw new NotFoundHttpException('Requested address does not exist');
        }

        $data = json_decode($request->getContent(), true);
        $street = $data['street'];
        $streetNumber = $data['street_number'];
        $postalCode = $data['postal_code'];
        $cityId = $data['city_id'];
        $countryId = $data['country_id'];
        $clientId = $data['client_id'];

        $city = $this->em->getRepository(City::class)->find($cityId);
        $country = $this->em->getRepository(Country::class)->find($countryId);
        $client = $this->em->getRepository(Client::class)->find($clientId);

        if(!$client)
        {
            throw new NotFoundHttpException('Requested client does not exist');
        }

        if(!$city)
        {
            throw new NotFoundHttpException('Requested city does not exist');
        }

        if(!$country)
        {
            throw new NotFoundHttpException('Requested country does not exist');
        }

        //check if data is set
        if(empty($street) || empty($streetNumber) || empty($postalCode) || empty($cityId) || empty($countryId) || empty($clientId))
        {
            throw new BadRequestException('Fields can not be blank');
        }

        //inserting record in database
        $address->setStreet($street);
        $address->setStreetNumber($streetNumber);
        $address->setPostalCode($postalCode);
        $address->setCity($city);
        $address->setCountry($country);
        $address->setClient($client);
        $this->em->persist($address);
        $this->em->flush();

        return $this->handleView($this->view($address, Response::HTTP_CREATED));
    }

    //Delete address record
    public function deleteAction(Request $request)
    {
        $addressId = $request->get('addressId');
        $address = $this->em->getRepository(Address::class)->find($addressId);
        if(!$address)
        {
            throw new NotFoundHttpException('Requested address does not exist');
        }

        $this->em->remove($address);
        $this->em->flush();

        return $this->handleView($this->view('Address successfully deleted', Response::HTTP_OK));
    }

}
