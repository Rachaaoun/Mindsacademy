<?php

namespace App\Controller;

use App\Entity\Matiere;
use App\Entity\Niveau;
use App\Form\MatiereType;
use App\Repository\MatiereRepository;
use App\Repository\NiveauRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/matiere")
 */
class MatiereController extends AbstractController
{
    /**
     * @Route("/", name="matiere_index", methods={"GET"})
     */
    public function index(MatiereRepository $matiereRepository): Response
    {
        
        return $this->render('matiere/index.html.twig', [
            'matieres' => $matiereRepository->findAll(),
        ]);
    }

    /**
     * @Route("/niveau/{idniveau}", name="matiere_niveau", methods={"GET"})
     */
    public function matiereparniveau(MatiereRepository $matiereRepository,$idniveau,NiveauRepository $niveauRepository): Response
    {

        $niveau=$niveauRepository->findOneById($idniveau);
        //dd($niveau);
        return $this->render('matiere/niveau.html.twig', [
            'matieres' => $niveau->getMatiere(),
            'idniveau'=>$idniveau
        ]);
    }

    /**
     * @Route("/niveau/admin/{idniveau}", name="matiere_niveau_admin", methods={"GET"})
     */
    public function matiereparniveauadmin(MatiereRepository $matiereRepository,$idniveau,NiveauRepository $niveauRepository): Response
    {


        $niveau=$niveauRepository->findOneById($idniveau);
        //dd($niveau);
        return $this->render('matiere/niveau_admin.html.twig', [
            'matieres' => $matiereRepository->findByNiveau($niveau),
            'idniveau'=>$idniveau
        ]);
    }


    /**
     * @Route("/new", name="matiere_new", methods={"GET","POST"})
     */
    public function new(Request $request,NiveauRepository $niveauRepository): Response
    {
        $matiere = new Matiere();
        $form = $this->createForm(MatiereType::class, $matiere);
        $form->handleRequest($request);
        $n=null;
        $niveaux=new Niveau();
       $niveaux = $form->get('niveau')->getData();
        
      
        if ($form->isSubmitted() && $form->isValid()) {
            $file=$matiere->getImage();
            $fileName=md5(uniqid()).'.'.$file->guessExtension();
            try{
                $file->move($this->getParameter('images_directory'),$fileName);
            }catch(FileException $e){

            }
            $matiere->setImage($fileName);
            foreach($niveaux as $n){
            $matiere->addNiveau($n);
            $n->addMatiere($matiere);
        }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($matiere,$niveaux);
            $entityManager->flush();

            return $this->redirectToRoute('matiere_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('matiere/new.html.twig', [
            'matiere' => $matiere,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="matiere_show", methods={"GET"})
     */
    public function show(Matiere $matiere): Response
    {
        return $this->render('matiere/show.html.twig', [
            'matiere' => $matiere,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="matiere_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Matiere $matiere): Response
    {

        $form = $this->createForm(MatiereType::class, $matiere);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
          
                $file=$matiere->getImage();
                $fileName=md5(uniqid()).'.'.$file->guessExtension();
                try{
                    $file->move($this->getParameter('images_directory'),$fileName);
                }catch(FileException $e){
    
                }
                $matiere->setImage($fileName);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('matiere_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('matiere/edit.html.twig', [
            'matiere' => $matiere,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="matiere_delete", methods={"POST"})
     */
    public function delete(Request $request, Matiere $matiere): Response
    {
        if ($this->isCsrfTokenValid('delete'.$matiere->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($matiere);
            $entityManager->flush();
        }

        return $this->redirectToRoute('matiere_index', [], Response::HTTP_SEE_OTHER);
    }
}
