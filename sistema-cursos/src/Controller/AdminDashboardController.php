<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminDashboardController extends AbstractController
{
    #[Route('/admin/dashboard', name: 'app_admin_dashboard')]
    public function index(UserRepository $repUser): Response
    {
        $arrayUser =array();
        $arrayUser = array_merge($repUser->findByUsersRole(User:: ROLE_TESTER),
                                 $repUser->findByUsersRole(User:: ROLE_DEV));
        return $this->render('admin_dashboard/index.html.twig', [
            'listUsers' => $arrayUser
        ]);
    }
}
