<?php

namespace App\Controller;

use App\Entity\City;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
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
}
