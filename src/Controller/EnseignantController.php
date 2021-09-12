<?php

namespace App\Controller;

use App\Entity\Cours;
use App\Entity\Enseignant;
use App\Entity\Matiere;
use App\Form\EnseignantType;
use App\Repository\CoursRepository;
use App\Repository\EnseignantRepository;
use App\Repository\EtudiantRepository;
use App\Repository\PdfRepository;
use App\Repository\UserRepository;
use App\Repository\VideoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/enseignants")
 */
class EnseignantController extends AbstractController
{
    /**
     * @Route("/", name="enseignant", methods={"GET"})
     */
    public function index(EnseignantRepository $enseignantRepository): Response
    {
        return $this->render('enseignant/index.html.twig', [
            'enseignants' => $enseignantRepository->findAll(),
        ]);
    }

     /**
     * @Route("/{id}/cours", name="video_cours_enseignant", methods={"GET"})
     */
    public function videoBycours(VideoRepository $videoRepository,$id,CoursRepository $coursRepository): Response
    {
        $enseignant=new Enseignant();
        $cours=$coursRepository->findOneById($id);
        if($cours)
        {$matiere=$cours->getMatiere();
        if($matiere)
        $enseignant=$matiere->getEnseignant();}
        return $this->render('enseignant/video.html.twig', [
            'videos' => $videoRepository->findByCours($cours),
            'enseignant'=>$enseignant,
            'id'=>$id
        ]);
    }


    /**
     * @Route("/{id}/cours/pdf", name="pdf_cours_enseignant", methods={"GET"})
     */
    public function pdfBycours(PdfRepository $pdfRepository,$id,CoursRepository $coursRepository): Response
    {
        $enseignant=new Enseignant();
        $cours=$coursRepository->findOneById($id);
        if($cours)
        {$matiere=$cours->getMatiere();
        if($matiere)
        $enseignant=$matiere->getEnseignant();}
        return $this->render('enseignant/pdf.html.twig', [
            'pdfs' => $pdfRepository->findByCours($cours),
            'enseignant'=>$enseignant,
            'id'=>$id
        ]);
    }

    /**
     * @Route("/{id}/pdfvideo", name="enseignant_pdf_video", methods={"GET"})
     */
    public function pdfVideo(EnseignantRepository $enseignantRepository,$id): Response
    {
        
        return $this->render('enseignant/pdfvideo.html.twig', [
            'enseignants' => $enseignantRepository->findAll(),
            'id'=>$id
        ]);
    }


    /**
     * @Route("/{id}/cours/groupe", name="enseignant_groupe_etudiant" , methods={"GET"})
     */
    public function groupeetudiants(EtudiantRepository $etudiantRepository,EnseignantRepository $enseignantRepository,$id,CoursRepository $coursRepository){
        $enseignant =new Enseignant();
        $matiere=new Matiere();
        $cours=[];
        $user=$this->getUser();
        if($user){
        $enseignant=$enseignantRepository->findOneByEmail($user->getEmail());
        $cours=$coursRepository->findOneById($id);
        
     $etudiants=null;
     $groupes=   $cours->getGroupes();
     // dd($matiere);
       
        }
        return $this->render('enseignant/groupe_etudiants.html.twig', [
            'enseignant' => $enseignant,
           
             'cours'=>$cours,
             'groupes'=>$groupes
           
             
        ]);


    }




    /**
     * @Route("/cours", name="enseignant_mescours" , methods={"GET"})
     */
    public function cours(EnseignantRepository $enseignantRepository){
        $enseignant =new Enseignant();
        $matiere=new Matiere();
        $cours=[];
        $user=$this->getUser();
        if($user){
        $enseignant=$enseignantRepository->findOneByEmail($user->getEmail());
        $cours=$enseignant->getCours();
        
     
       
        }
        return $this->render('enseignant/cours.html.twig', [
            'enseignant' => $enseignant,
           
             'cours'=>$cours,
             
        ]);


    }

    /**
     * @Route("/new/{id}", name="enseignant_neww", methods={"GET","POST"})
     */
    public function new(Request $request,$id,UserRepository $userRepository): Response
    {
        $user=$userRepository->findOneById($id);
        $enseignant = new Enseignant();
        $form = $this->createForm(EnseignantType::class, $enseignant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $enseignant->setEmail($user->getEmail());
            $enseignant->setNom($user->getNom());
            $enseignant->setPrenom($user->getPrenom());
            $enseignant->setImage($user->getImage());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($enseignant);
            $entityManager->flush();

            return $this->redirectToRoute('enseignant_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('enseignant/new.html.twig', [
            'enseignant' => $enseignant,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="enseignant_showw", methods={"GET"})
     */
    public function show(Enseignant $enseignant): Response
    {
        return $this->render('enseignant/show.html.twig', [
            'enseignant' => $enseignant,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="enseignant_editt", methods={"GET","POST"})
     */
    public function edit(Request $request, Enseignant $enseignant): Response
    {
        $form = $this->createForm(EnseignantType::class, $enseignant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('enseignant_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('enseignant/edit.html.twig', [
            'enseignant' => $enseignant,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="enseignant_deletee", methods={"POST"})
     */
    public function delete(Request $request, Enseignant $enseignant): Response
    {
        if ($this->isCsrfTokenValid('delete'.$enseignant->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($enseignant);
            $entityManager->flush();
        }

        return $this->redirectToRoute('enseignant_index', [], Response::HTTP_SEE_OTHER);
    }


  
}
