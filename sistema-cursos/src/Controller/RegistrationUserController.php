<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrateUserType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationUserController extends AbstractController
{
    #[Route('/registration/user', name: 'app_registration_user')]
    public function index(Request $request, UserPasswordHasherInterface $encoderPassword, ManagerRegistry $doctrine): Response
    {
        $user = new User(User :: ROLE_TESTER,"Tester");
        $form = $this -> createForm(RegistrateUserType :: class, $user);
        $form -> handleRequest($request);
        if($form ->isSubmitted() && $form ->isValid())
        {
            $user=$form ->getData();
            $user -> setPassword($encoderPassword ->hashPassword($user,$form["password"]->getData()));
            if($this ->isGranted(USER :: ROLE_ADMIN))
            {
                $user -> setRoles([USER ::ROLE_DEV]);
                $user -> setTipo("Desarrollador");
            }
            $em = $doctrine -> getManager();
            $em -> persist($user);
            $em -> flush();
            if($this ->isGranted(USER :: ROLE_ADMIN))
            {
                return $this-> redirectToRoute('app_admin_dashboard');
            }
            else 
            {
                return $this -> redirectToRoute('app_login');
            }
            
        }
        return $this->render('registration_user/index.html.twig', [
            'formulario' => $form ->createView(),
        ]);
    }
}
