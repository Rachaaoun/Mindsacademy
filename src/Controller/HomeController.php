<?php

namespace App\Controller;

use App\Repository\CoursRepository;
use App\Repository\EnseignantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     */
    public function index(CoursRepository $coursRepository,EnseignantRepository $enseignantRepository): Response
    {
        $cours=$coursRepository->findByPopulaire(true);
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'cours'=>$cours,
            'enseignants'=>$enseignantRepository->findAll()
        ]);
    }

    /**
     * @Route("/homeadmin", name="homeadminbase")
     */
    public function indexAdmin(): Response
    {
        return $this->render('baseadmin.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }


    /**
     * @Route("/about", name="about")
     */
    public function about(): Response
    {
        return $this->render('home/about.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
