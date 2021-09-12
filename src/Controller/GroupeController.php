<?php

namespace App\Controller;

use App\Entity\Groupe;
use App\Form\EtudiantgroupeType;
use App\Form\GroupeType;
use App\Repository\GroupeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/groupe")
 */
class GroupeController extends AbstractController
{
    /**
     * @Route("/", name="groupe_index", methods={"GET"})
     */
    public function index(GroupeRepository $groupeRepository): Response
    {

        return $this->render('groupe/index.html.twig', [
            'groupes' => $groupeRepository->findAll(),
        ]);
    }


    /**
     * @Route("{id}/etudiants" , name="etudiants_par_groupe", methods={"GET"})
     */
    public function getEtudiants($id,GroupeRepository $groupeRepository):Response
    {

        $groupe=$groupeRepository->findOneById($id);
        return $this->render('groupe/etudiants.html.twig',[
            'etudiants'=> $groupe->getEtudiants()
        ]);
    }
    /**
     * @Route("/new", name="groupe_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $groupe = new Groupe();
        $form = $this->createForm(GroupeType::class, $groupe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($groupe);
            $entityManager->flush();

            return $this->redirectToRoute('groupe_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('groupe/new.html.twig', [
            'groupe' => $groupe,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="groupe_show", methods={"GET"})
     */
    public function show(Groupe $groupe): Response
    {
        return $this->render('groupe/show.html.twig', [
            'groupe' => $groupe,
        ]);
    }

     /**
     * @Route("/{id}/edit", name="groupe_edit", methods={"GET","POST"})
     */
     public function edit(Request $request, Groupe $groupe): Response
     {
         $form = $this->createForm(GroupeType::class, $groupe);
         $form->handleRequest($request);

         if ($form->isSubmitted() && $form->isValid()) {
             $this->getDoctrine()->getManager()->flush();

             return $this->redirectToRoute('groupe_index', [], Response::HTTP_SEE_OTHER);
         }

         return $this->render('groupe/edit.html.twig', [
             'groupe' => $groupe,
             'form' => $form->createView(),
         ]);
     }


    //  /**
    //  * @Route("/{id}/groupe/etudiant", name="groupe_etudiant", methods={"GET","POST"})
    //  */
    // public function ajouterEtudiant(Request $request, Groupe $groupe): Response
    // {
    //     $form = $this->createForm(EtudiantgroupeType::class, $groupe);
    //     $form->handleRequest($request);
    //     $form->get('groupe')->getData();
    //     if ($form->isSubmitted() && $form->isValid()) {

    //         $this->getDoctrine()->getManager()->flush();

    //         return $this->redirectToRoute('groupe_index', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->render('groupe/edit.html.twig', [
    //         'groupe' => $groupe,
    //         'form' => $form->createView(),
    //     ]);
    // }

    /**
     * @Route("/{id}", name="groupe_delete", methods={"POST"})
     */
    public function delete(Request $request, Groupe $groupe): Response
    {
        if ($this->isCsrfTokenValid('delete'.$groupe->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($groupe);
            $entityManager->flush();
        }

        return $this->redirectToRoute('groupe_index', [], Response::HTTP_SEE_OTHER);
    }
}
