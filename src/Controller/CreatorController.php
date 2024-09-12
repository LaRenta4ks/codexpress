<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CreatorController extends AbstractController
{
    #[IsGranted('IS_AUTHENTIFICATED_FULLY')]
    #[Route('/creator', name: 'app_profile', methods:['GET'])]
    public function profile(): Response
    {
        return $this->render('creator/index.html.twig', );
    }
    #[Route('/creator', name: 'app_profile_edit', methods:['GET','POST'])]
    public function edit(): Response
    {
        $user = $this->getUser();
        return $this->render('creator/edit.html.twig', );
    }
}
