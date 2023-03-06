<?php

namespace App\Controller;

use App\Entity\Curso;
use App\Form\RegisterCursoType;
use App\Repository\TestingRepository;
use App\Repository\UserRepository;
use App\Repository\CursoRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Security\LoginCustomAuthenticator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DevDashboardController extends AbstractController
{
    #[Route('/dev/dashboard', name: 'app_dev_dashboard')]
    public function index(Request $request, UserRepository $userRepository,CursoRepository $cursoRepository): Response
    {
        $email = $request->getSession()->get('_security.last_username', '');
        //var_dump($email);
        $user = $userRepository->findOneBy(["email"=>$email]);
        
        return $this->render('dev_dashboard/index.html.twig', [
            'listCurso'=>$cursoRepository->findBy(["userId"=>($user->getId())]),
        ]);
    }

    #[Route('/dev/dashboard/register', name: 'app_dev_dashboard_register')]
    public function register(Request $request,CursoRepository $cursoRepository,ManagerRegistry $doctrine, UserRepository $userRepository): Response
    {
        //$user = $request->request ->ge()
        $email = $request->getSession()->get('_security.last_username', '');
        //var_dump($email);
        $user = $userRepository->findOneBy(["email"=>$email]);
        $curso=new Curso();
        $form =$this->createForm(RegisterCursoType::class, $curso);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $curso=$form->getData();
            $curso->setUserId($user->getId());
            $curso->setEstado("EnDesarrollo");
            $em=$doctrine->getManager();
            $em->persist($curso);
            $em->flush();
            $this->addFlash("success","Registro de Curso Exitoso");
            return $this->redirectToRoute('app_dev_dashboard');
        }
        return $this->render('dev_dashboard/registerCurso.html.twig', [
            'formulario'=>$form->createView(),
        ]);
    }

    #[Route('/dev/dashboard/edit/{id}', name: 'app_dev_dashboard_edit')]
    public function edit(Request $request,Curso $curso,CursoRepository $cursoRepository,UserRepository $userRepo): Response
    {
        
        $mensaje="jj";
        $estado="jj";
        $email = $request->getSession()->get('_security.last_username', '');
        
        $objUsuario = $userRepo->findOneBy(["email"=>$email]);
        $form = $this->createForm(RegisterCursoType::class, $curso, array('accion'=>'editCurso'));
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $curso = $form->getData();
            if ($curso->getEstado() == "Activo")
            {
                $totalCurso = $cursoRepository->getTotalByUserIdyEstado($objUsuario->getId(),"Activo");
                if($totalCurso != null && $totalCurso >=2)
                {
                    $mensaje="Error: No puede tener mas de dos Cursos activos para test";
                    $estado="error";
                }
            }
            elseif ($curso->getEstado() == "EnDesarrollo" )
            {
                    $mensaje="Error: No puede cambiar el estado a En Desarrollo";
                    $estado="error";
            }
            if($estado!= "error")
            {
                $mensaje="Curso actualizado correctamente";
                $estado="success";
                $cursoRepository->save($curso,true);
            }
            $this->addFlash($estado, $mensaje);
            return $this->redirectToRoute('app_dev_dashboard');
            
        }
        return $this->render('dev_dashboard/registerCurso.html.twig', [
            'formulario'=>$form->createView(),
        ]);
    }

    #[Route('/dev/dashboard/delete/{id}', name: 'app_dev_dashboard_delete')]
    public function delete(Curso $curso,CursoRepository $cursoRepository, TestingRepository $testingRepository): Response
    {
        //$lista=$testingRepository->findAll();
      
        if($curso->getEstado() == "Activo")    
        {
            $this->addFlash("error", "Error: No puede eliminar un curso que este siendo testeado");
        }
        else 
        {
           $cursoRepository->remove($curso,true);
           $this->addFlash("success", "Curso eliminado con exito"); 
        }
        
        return $this->redirectToRoute('app_dev_dashboard');
    }

}
