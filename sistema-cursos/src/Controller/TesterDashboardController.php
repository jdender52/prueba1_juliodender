<?php

namespace App\Controller;

use DateTimeZone;
use App\Entity\Curso;
use App\Entity\Testing;
use App\Form\RegisterCursoType;
use App\Repository\UserRepository;
use App\Repository\CursoRepository;
use App\Repository\TestingRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TesterDashboardController extends AbstractController
{
    #[Route('/tester/dashboard', name: 'app_tester_dashboard')]
    public function index(CursoRepository $cursoRepository): Response
    {
        
        $arrayCursos =array();
        $arrayCursos = array_merge($cursoRepository ->findBy(["estado" => "Activo"],[])
                                 );
        return $this->render('tester_dashboard/index.html.twig', [
            'controller_name' => 'TesterDashboardController',
            'listCurso' => $arrayCursos
        ]);
    }
    #[Route('/tester/dashboard/Test/{id}', name: 'app_tester_dashboard_test')]
    public function Test(Request $request,Curso $curso, UserRepository $userRepo, CursoRepository $cursoRepository,TestingRepository $testRep): Response
    {
        $mensaje="jj";
        $estado="jj";
        $email = $request->getSession()->get('_security.last_username', '');
        $objUsuario = $userRepo -> findOneBy(["email" => $email]);
        $listaTests = $testRep -> findAll();
        foreach ($listaTests as $test)
        {
         
            if (($curso ->getId() == $test->getCursoId()))
            {
                    $mensaje = "Error: Usted ya se encuentra probando este curso";
                    $estado = "error";
                
            }
            elseif(($curso->getCreador($userRepo) == $test->getCreador($userRepo,$cursoRepository)  ))
            {
                $mensaje = "Error: Usted ya se encuentra probando un curso con este mismo desarrollador";
                    $estado = "error";
            }
            
        }
            
            if($estado!= "error")
            {
                $mensaje="Se te ha permitido testear el juego, comenzando descarga del curso";
                $estado="success";
                date_default_timezone_set("America/Lima");
                $fecha=date("d-m-Y");
                $testNuevo = new Testing($objUsuario->getId(),
                                         $curso->getId(),
                                         date_create($fecha));
                $testRep ->save($testNuevo,true);
            }
            $this -> addFlash($estado, $mensaje);
            return $this->redirectToRoute('app_tester_dashboard');
            
        
        
    }
    
}
