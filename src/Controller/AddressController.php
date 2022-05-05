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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AddressController extends AbstractFOSRestController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Rest\Get('/api/addresses', name: 'get_addresses')]
    public function getAddressesAction()
    {
        $addresses = $this->em->getRepository(Address::class)->findAll();
        if($addresses === null)
        {
            return new View('There are no addresses exist', Response::HTTP_NOT_FOUND);
        }

        return $this->view($addresses, Response::HTTP_OK);
    }

    private function object_to_array($obj) {
        if(is_object($obj)) $obj = (array) $this->dismount($obj);
        if(is_array($obj)) {
            $new = array();
            foreach($obj as $key => $val) {
                $new[$key] = $this->object_to_array($val);
            }
        }
        else $new = $obj;
        return $new;
    }

    private function dismount($object) {
        $reflectionClass = new \ReflectionClass(get_class($object));
        $array = array();
        foreach ($reflectionClass->getProperties() as $property) {
            $property->setAccessible(true);
            $array[$property->getName()] = $property->getValue($object);
            $property->setAccessible(false);
        }
        return $array;
    }

    #[Rest\Get('/api/addresses/{id}', name: 'get_address')]
    public function getAddressAction($id, Request $request)
    {
        $address = $this->em->getRepository(Address::class)->find($id);
        if ($address === null) {
            return new View('The requested result does not exist', Response::HTTP_NOT_FOUND);
        }
//        $serializer = JMS::create()->build();
//        $jsonContent = $serializer->serialize($address, 'json');
//        $data = json_decode($jsonContent);
//        $array = $this->object_to_array($address);
//        $postalCode = $array['postalCode'];
//        dd($array['client']);
        return $this->view($address, Response::HTTP_OK);
    }

    #[Rest\Post('/api/addresses', name: 'post_address')]
    public function postAddressAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $street = $data['street'];
        $streetNumber = $data['street_number'];
        $postalCode = $data['postal_code'];
        $clientId = $data['client']['id'];
        $cityId = $data['city']['id'];
        $countryId = $data['country']['id'];

        $address = new Address();
        $client = $this->em->getRepository(Client::class)->find($clientId);
        $city = $this->em->getRepository(City::class)->find($cityId);
        $country = $this->em->getRepository(Country::class)->find($countryId);

        $address->setStreet($street);
        $address->setStreetNumber($streetNumber);
        $address->setPostalCode($postalCode);
        $address->setCity($city);
        $address->setCountry($country);
        $address->setClient($client);

        $this->em->persist($address);
        $this->em->flush();

        return $this->view('The address was successfully created', Response::HTTP_CREATED);
    }

    #[Rest\Put('/api/addresses/{id}', name: 'update_address')]
    public function updateAddressAction($id, Request $request)
    {
        $address = $this->em->getRepository(Address::class)->find($id);

        $data = json_decode($request->getContent(), true);
        $street = $data['street'];
        $streetNumber = $data['street_number'];
        $postalCode = $data['postal_code'];
        $clientId = $data['client']['id'];
        $cityId = $data['city']['id'];
        $countryId = $data['country']['id'];

        $client = $this->em->getRepository(Client::class)->find($clientId);
        $city = $this->em->getRepository(City::class)->find($cityId);
        $country = $this->em->getRepository(Country::class)->find($countryId);

        $address->setStreet($street);
        $address->setStreetNumber($streetNumber);
        $address->setPostalCode($postalCode);
        $address->setCity($city);
        $address->setCountry($country);
        $address->setClient($client);

        $this->em->persist($address);
        $this->em->flush();

        return $this->view('The address was successfully updated', Response::HTTP_OK);
    }

    #[Rest\Delete('/api/addresses/{id}', name: 'delete_address')]
    public function deleteAddressAction($id)
    {
        $address = $this->em->getRepository(Address::class)->find($id);

        if($address === null)
        {
            return new View('The requested result does not exist', Response::HTTP_NOT_FOUND);
        }

        $this->em->remove($address);
        $this->em->flush();

        return $this->view('Deleted successfully', Response::HTTP_OK);
    }

}
