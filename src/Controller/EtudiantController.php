<?php

namespace App\Controller;

use App\Entity\Etudiant;
use App\Entity\User;
use App\Form\ConfirmationFormType;
use App\Form\EtudiantgroupeType;
use App\Form\EtudiantType;
use App\Repository\CoursRepository;
use App\Repository\EtudiantRepository;
use App\Repository\PdfRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/etudiants")
 */
class EtudiantController extends AbstractController
{
    /**
     * @Route("/", name="etudiant", methods={"GET"})
     */
    public function index(EtudiantRepository $etudiantRepository): Response
    {
        return $this->render('etudiant/index.html.twig', [
            'etudiants' => $etudiantRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new/{id}", name="etudiant_neww", methods={"GET","POST"})
     */
    public function new(Request $request,$id,UserRepository $userRepository): Response
    {
        $user=$userRepository->findOneById($id);
        $etudiant = new Etudiant();
        $form = $this->createForm(EtudiantType::class, $etudiant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $etudiant->setEmail($user->getEmail());
            $etudiant->setNom($user->getNom());
            $etudiant->setPrenom($user->getPrenom());
            $etudiant->setImage($user->getImage());
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($etudiant);
            $entityManager->flush();

            return $this->redirectToRoute('etudiant_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('etudiant/new.html.twig', [
            'etudiant' => $etudiant,
            'form' => $form->createView(),
        ]);
    }

    
    // /**
    //  * @Route("/cours/{id}", name="etudiant_cours", methods={"GET","POST"})
    //  */
    // public function addCours(Request $request,$id,PdfRepository $pdfRepository,UserRepository $userRepository,CoursRepository $coursRepository): Response
    // {
    //     $cours=$coursRepository->findOneById($id);
    //     $user = $this->getUser();
    //     if($user){
    //         $form = $this->createForm(ConfirmationFormType::class);
    //         $form->handleRequest($request);
    //         if ($form->isSubmitted() && $form->isValid()) {
    //             $user->addCour($cours);
    //             $entityManager = $this->getDoctrine()->getManager();
    //             $entityManager->persist($user);
    //             $entityManager->persist($cours);
    //             $entityManager->flush();
    //         }
    //     }
    //     else{
    //         return $this->redirectToRoute('app_login');
    //     }
    //     $cours=$coursRepository->findOneById($id);
       

    //     return $this->render('cours/detail.html.twig', [
    //         'cours' => $cours,
    //         'matiere'=>$cours->getMatiere(),
    //         'pdfs' =>$pdfRepository->findByCours($cours),
    //         'form' => $form->createView(),
    //     ]);
    // }

    /**
     * @Route("/{id}", name="etudiant_showww", methods={"GET"})
     */
    public function show(Etudiant $etudiant): Response
    {
        return $this->render('etudiant/show.html.twig', [
            'etudiant' => $etudiant,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="etudiant_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Etudiant $etudiant,UserRepository $userRepository): Response
    {
        $form = $this->createForm(EtudiantType::class, $etudiant);
        $form->handleRequest($request);

        $email = $etudiant->getEmail();
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('etudiant', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('etudiant/edit.html.twig', [
            'etudiant' => $etudiant,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="etudiant_delete", methods={"POST"})
     */
    public function delete(Request $request, Etudiant $etudiant,UserRepository $userRepository): Response
    {
       $email= $etudiant->getEmail();
        $user=$userRepository->findOneByEmail($email);
        if ($this->isCsrfTokenValid('delete'.$etudiant->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->remove($etudiant);
            $entityManager->flush();
        }

        return $this->redirectToRoute('etudiant', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/{id}/groupeetudiant", name="etudgroupe_edit" , methods={"GET" ,"POST"})
     */
    public function groupe(Request $request, Etudiant $etudiant): Response
    {
        $form = $this->createForm(EtudiantgroupeType::class, $etudiant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('etudiant', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('etudiant/groupe.html.twig', [
            'etudiant' => $etudiant,
            'form' => $form->createView(),
        ]);
    }


}
