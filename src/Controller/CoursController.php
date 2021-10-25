<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Cours;
use App\Form\ConfirmationFormType;
use App\Form\CoursType;
use App\Repository\CoursRepository;
use App\Repository\EtudiantRepository;
use App\Repository\MatiereRepository;
use App\Repository\NiveauRepository;
use App\Repository\PdfRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/cours")
 */
class CoursController extends AbstractController
{
    /**
     * @Route("/{idniveau}", name="cours_index", methods={"GET"})
     */
    public function index(CoursRepository $coursRepository,$idniveau,MatiereRepository $matiereRepository,NiveauRepository $niveauRepository): Response
    {
        $niveau=$niveauRepository->findOneById($idniveau);
        $matieres=$matiereRepository->findByNiveau($niveau);
        
        
        return $this->render('cours/index.html.twig', [
           
            'cours' => $coursRepository->findByMatiere($matieres),
            'matieres'=>$matieres
        ]);
    }

    /**
     * @Route("/matiere/{id}", name="cours_matieree", methods={"GET"})
     */
    public function coursparmatiere(CoursRepository $coursRepository,$id,MatiereRepository $matiereRepository): Response
    {
        $matieres=$matiereRepository->findOneById($id);
       // dd($matieres);
        return $this->render('cours/coursparmatiere.html.twig', [
            'cours' => $matieres->getCours(),
            'matieres'=>$matieres
        ]);
    }

    /**
     * @Route("/matiere/admin/{id}", name="cours_matieree_admin", methods={"GET"})
     */
    public function coursparmatiereadmin(CoursRepository $coursRepository,$id,MatiereRepository $matiereRepository): Response
    {
        $matieres=$matiereRepository->findOneById($id);
       // dd($matieres);
        return $this->render('cours/coursparmatiereadmin.html.twig', [
            'cours' => $matieres->getCours(),
            'matieres'=>$matieres
        ]);
    }




    /**
     * @Route("/{id}/detail", name="cours_detail", methods={"GET","POST"})
     */
    public function detail(Request $request,CoursRepository $coursRepository,EtudiantRepository $etudiantRepository,$id,PdfRepository $pdfRepository): Response
    {
        $cours=$coursRepository->findOneById($id);
        $user = $this->getUser();
        $contact = new Contact();   
        $cours=$coursRepository->findOneById($id);
        $user=$this->getUser();
        //dd($cours);
        $forms = $this->createForm(ConfirmationFormType::class);
        $forms->handleRequest($request);
        if($forms->isSubmitted()) {
          
          
               if($user){
                $contact->setSujet('Paiement');
                $ch ='Un etudiant : '.$user->getEmail().'Veut payer le cours :' .$cours->getTitre();
                $contact->setMessage((String)$ch);
               $contact->setNom($user->getNom());
               $contact->setEmail($user->getEmail());
               
               $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($contact);
                $entityManager->flush();
                
            return $this->redirectToRoute('paiement_cours');
            }
            else  return $this->redirectToRoute('app_login');
        }
        if($user){
            
            $email =$user->getEmail();
            $etudiant=$etudiantRepository->findOneByEmail($email);
            $form = $this->createForm(ConfirmationFormType::class);
            $form->handleRequest($request);

            if ($this->isCsrfTokenValid('commencercours'.$cours->getId(), $request->request->get('_token'))) {
              
                if($etudiant){
                $cours->addEtudiant($etudiant);
                $etudiant->addCour($cours);
                }
                $entityManager = $this->getDoctrine()->getManager();
              
                $entityManager->persist($cours,$etudiant);
                $entityManager->flush();
                return $this->redirectToRoute('pdf_video',['id'=>$id], Response::HTTP_SEE_OTHER);
          
            }

           
        }
        else{
            return $this->redirectToRoute('app_login');
        }
        $cours=$coursRepository->findOneById($id);
        return $this->render('cours/detail.html.twig', [
            'cours' => $cours,
            'matiere'=>$cours,
            'pdfs' =>$pdfRepository->findByCours($cours),
            'form' => $form->createView(),
            'forms'=>$forms->createView()
        ]);
    }

    /**
     * @Route("/list", name="cours_list", methods={"GET"})
     */
    public function list(CoursRepository $coursRepository): Response
    {
        $matieres=$coursRepository->findAll();
        return $this->render('cours/list.html.twig', [
            'cours' => $coursRepository->findAll(),
            'matieres'=>$matieres
        ]);
    }

    /**
     * @Route("/coursparmatiere/{id}", name="cours_matiere", methods={"GET"})
     */
    public function listParMatiere(CoursRepository $coursRepository,$id): Response
    {
        $matieres=$coursRepository->findOneById($id);
        return $this->render('cours/list.html.twig', [
            'cours' => $matieres,
            'matieres'=>$matieres
        ]);
    }

    /**
     * @Route("/popCours", name="cours_p", methods={"GET"})
     */
    public function Pop(CoursRepository $coursRepository): Response
    {
    $cours= $coursRepository->findByPopulaire(true);
        $matieres=$coursRepository->findall();
        return $this->render('cours/populaire.html.twig', [
            'cours' =>$cours,
            'matieres'=>$matieres
        ]);
    }


    /**
     * @Route("/new/{idniveau}/{id}", name="cours_new", methods={"GET","POST"})
     */
    public function new(NiveauRepository $niveauRepository, Request $request,MatiereRepository $matiereRepository,$idniveau,$id): Response
    {
        $niveau=$niveauRepository->findOneById($idniveau);
        $matiere=$matiereRepository->findOneById($id);
        $cour = new Cours();
        $form = $this->createForm(CoursType::class, $cour);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $cour->setNiveau($niveau);
            $cour->setMatiere($matiere);
        //     $matiere=$form->get('matiere')->getData();
        //    $titrematiere= $matiere->getTitre();
        //     $titre=$form->get('titre')->getData();
           
        //     $ch=$titre . "-" . $titrematiere;
         
        //     $cour->setTitre( $ch );
            $cour->setPopulaire(false);
            $file=$cour->getImage();
            $fileName=md5(uniqid()).'.'.$file->guessExtension();
            try{
                $file->move($this->getParameter('images_directory'),$fileName);
            }catch(FileException $e){

            }
            $cour->setImage($fileName);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($cour);
            $entityManager->flush();

            return $this->redirectToRoute('homeadmin', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('cours/new.html.twig', [
            'cour' => $cour,
            'form' => $form->createView(),
            'matieres'=>$matiereRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{id}", name="cours_show", methods={"GET"})
     */
    public function show(Cours $cour,NiveauRepository $niveauRepository ,CoursRepository $coursRepository): Response
    {
        return $this->render('cours/show.html.twig', [
            'cour' => $cour,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="cours_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Cours $cour): Response
    {
        $form = $this->createForm(CoursType::class, $cour);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $file=$cour->getImage();
            $fileName=md5(uniqid()).'.'.$file->guessExtension();
            $cour->setImage($fileName);   
            try{
                $file->move($this->getParameter('images_directory'),$fileName);
                
            }catch(FileException $e){   }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($cour);
            $entityManager->flush();

            return $this->redirectToRoute('homeadmin', [], Response::HTTP_SEE_OTHER);
        }
     

        return $this->render('cours/edit.html.twig', [
            'cour' => $cour,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="cours_delete", methods={"POST"})
     */
    public function delete(Request $request, Cours $cour): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cour->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($cour);
            $entityManager->flush();
        }

        return $this->redirectToRoute('homeadmin', [], Response::HTTP_SEE_OTHER);
    }
    

    /**
     * @Route("/{id}/populaire", name="cours_populaire", methods={"GET","POST"})
     */
    public function activePopulaire( Request $request, Cours $cour,CoursRepository $coursRepository,$id)
    {
        if ($this->isCsrfTokenValid('populaire'.$cour->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $cour->setPopulaire(true);
            $entityManager->persist($cour);
            $entityManager->flush();
        }

        return $this->redirectToRoute('homeadmin', [], Response::HTTP_SEE_OTHER);

    }


    
    /**
     * @Route("/{id}/nonpopulaire", name="cours_nonpopulaire",methods={"GET","POST"})
     */
    public function desactivePopulaire( Request $request, Cours $cour,CoursRepository $coursRepository,$id)
    {
        if ($this->isCsrfTokenValid('nonpopulaire'.$cour->getId(), $request->request->get('_token'))) {
            $cour=$coursRepository->findOneById($id);
            $entityManager = $this->getDoctrine()->getManager();
            $cour->setPopulaire(false);
            $entityManager->persist($cour);
            $entityManager->flush();
        }

        return $this->redirectToRoute('homeadmin', [], Response::HTTP_SEE_OTHER);

    }
    
     /**
     * @Route("/mescours", name="mes_cours", methods={"GET"})
     */
    public function mesCours( Request $request, Cours $cour,CoursRepository $coursRepository,$id,EtudiantRepository $etudiantRepository)
    {

        $user=$this->getUser();
        if($user){
        $email=$user->getEmail();
        $etudiant=$etudiantRepository->findOneByEmail($email);
        $mescours=$etudiant->getCours();
        }
        return $this->render('cours/mescours.html.twig',[
            'mescours'=>$mescours,
        ]);

    }
       
    /**
     * @Route("/{id}/pdfvideo", name="pdf_video", methods={"GET"})
     */
    public function pdfVideo(CoursRepository $coursRepository,$id):Response
    {


        return $this->render('matiere/pdfvideo.html.twig', [
            'cours' => $coursRepository->findAll(),
            'id'=>$id
           
        ]);
    }

    /**
     * @Route("/etudiant/paiement", name="paiement_cours", methods={"GET"})
     */
    public function paiement(CoursRepository $coursRepository,Request $request):Response
    {
       
        return $this->render('cours/paiement.html.twig', [
            'cours' => $coursRepository->findAll(),
         
           
        ]);
    }

}
