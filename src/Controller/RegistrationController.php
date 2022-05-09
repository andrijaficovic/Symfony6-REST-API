<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;


class RegistrationController extends AbstractFOSRestController
{
    private $em;
    private UserPasswordHasherInterface $passwordEncoder;

    /**
     * @param $em
     */
    public function __construct(EntityManagerInterface $em, UserPasswordHasherInterface $passwordEncoder)
    {
        $this->em = $em;
        $this->passwordEncoder = $passwordEncoder;
    }

    //Register new user
    public function register(Request $request)
    {
        //getting data from post request
        $data = json_decode($request->getContent(), true);
        $email = $data['email'];
        $plainPassword = $data['password'];
        $role = $data['role'];
        $name = $data['name'];
        $surname = $data['surname'];

        if(empty($email) || empty($plainPassword) || empty($role) || empty($name) || empty($surname))
        {
            throw new BadCredentialsException("Fields can not be blank");
        }

        $user = $this->em->getRepository(User::class)->findOneBy([
            'email'=>$email
        ]);
        //check if user exists
        if(!is_null($user))
        {
            return $this->view([
                'message'=>'User already exists'
            ], Response::HTTP_CONFLICT);
        }

        //creating user
        $user = new User();
        $user->setEmail($email);
        $user->setPassword(
            $this->passwordEncoder->hashPassword($user, $plainPassword)
        );
        $user->setName($name);
        $user->setSurname($surname);
        if($role === strtolower('admin'))
        {
            $user->setRoles(["ROLE_ADMIN"]);
        }elseif ($role === strtolower('user'))
        {
            $user->setRoles(["ROLE_USER"]);
        }

        $this->em->persist($user);
        $this->em->flush();

        return $this->view($user, Response::HTTP_CREATED);
    }
}
