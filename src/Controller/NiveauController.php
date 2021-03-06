<?php

namespace App\Controller;

use App\Entity\Niveau;
use App\Form\NiveauType;
use App\Repository\NiveauRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/niveau")
 */
class NiveauController extends AbstractController
{
    /**
     * @Route("/", name="niveau_index", methods={"GET"})
     */
    public function index(NiveauRepository $niveauRepository): Response
    {
        return $this->render('niveau/index.html.twig', [
            'niveaux' => $niveauRepository->findAll(),
        ]);
    }

    /**
     * @Route("/list", name="niveau_list", methods={"GET"})
     */
    public function list(NiveauRepository $niveauRepository): Response
    {
        return $this->render('niveau/list.html.twig', [
            'niveaux' => $niveauRepository->findAll(),
        ]);
    }


    /**
     * @Route("/new/admin", name="niveau_list_admin", methods={"GET"})
     */
    public function listadmin(NiveauRepository $niveauRepository): Response
    {
        return $this->render('niveau/listçadmin.html.twig', [
            'niveaux' => $niveauRepository->findAll(),
        ]);
    }   
    
    
    /**
     * @Route("/list/admin", name="niveau_list_admi", methods={"GET"})
     */
    public function listadmi(NiveauRepository $niveauRepository): Response
    {
        return $this->render('niveau/listadmin.html.twig', [
            'niveaux' => $niveauRepository->findAll(),
        ]);
    }
    /**
     * @Route("/new", name="niveau_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $niveau = new Niveau();
        $form = $this->createForm(NiveauType::class, $niveau);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($niveau);
            $entityManager->flush();

            return $this->redirectToRoute('niveau_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('niveau/new.html.twig', [
            'niveau' => $niveau,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="niveau_show", methods={"GET"})
     */
    public function show(Niveau $niveau): Response
    {
        return $this->render('niveau/show.html.twig', [
            'niveau' => $niveau,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="niveau_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Niveau $niveau): Response
    {
        $form = $this->createForm(NiveauType::class, $niveau);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('niveau_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('niveau/edit.html.twig', [
            'niveau' => $niveau,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="niveau_delete", methods={"POST"})
     */
    public function delete(Request $request, Niveau $niveau): Response
    {
        if ($this->isCsrfTokenValid('delete'.$niveau->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($niveau);
            $entityManager->flush();
        }

        return $this->redirectToRoute('niveau_index', [], Response::HTTP_SEE_OTHER);
    }
}
