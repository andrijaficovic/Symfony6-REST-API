<?php

namespace App\Controller;

use App\Entity\Country;
use App\Form\CountryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CountryController extends AbstractApiController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    //Retrieve all countries
    public function indexAction()
    {
        $countries = $this->em->getRepository(Country::class)->findAll();
        if(!$countries)
        {
            throw new NotFoundHttpException('Countries not found');
        }
        return $this->respond($countries, Response::HTTP_OK);
    }

    //Retrieve country by id
    public function showAction(Request $request)
    {
        $countryId = $request->get('countryId');
        $country = $this->em->getRepository(Country::class)->find($countryId);
        if($country === null)
        {
            throw new NotFoundHttpException('Requested country does not exist');
        }

        return $this->respond($country, Response::HTTP_OK);
    }

    //create country record
    public function createAction(Request $request)
    {
        $form = $this->buildForm(CountryType::class);
        $form->handleRequest($request);

        if(!$form->isSubmitted() || !$form->isValid())
        {
            return $this->respond($form, Response::HTTP_BAD_REQUEST);
        }

        $country = $form->getData();
        $this->em->persist($country);
        $this->em->flush();

        return $this->respond($country, Response::HTTP_CREATED);
    }

    //update country record
    public function updateAction(Request $request)
    {
        $countryId = $request->get('countryId');
        $country = $this->em->getRepository(Country::class)->find($countryId);

        if(!$country)
        {
            throw new NotFoundHttpException('Requested country does not exist');
        }

        $form = $this->buildForm(CountryType::class, $country, [
            'method' => $request->getMethod()
        ]);
        $form->handleRequest($request);

        if(!$form->isSubmitted() || !$form->isValid())
        {
            return $this->respond($form, Response::HTTP_BAD_REQUEST);
        }

        $country = $form->getData();
        $this->em->persist($country);
        $this->em->flush();

        return $this->respond($country, Response::HTTP_CREATED);
    }

    //Delete country record
    public function deleteAction(Request $request)
    {
        $countryId = $request->get('countryId');
        $country = $this->em->getRepository(Country::class)->find($countryId);
        if(!$country)
        {
            throw new NotFoundHttpException('Requested country does not exist');
        }

        $this->em->remove($country);
        $this->em->flush();

        return $this->respond('Requested country successfully deleted',Response::HTTP_OK);
    }
}
