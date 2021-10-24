<?php

namespace App\Controller;

use App\Entity\Enseignant;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\EnseignantRepository;
use App\Repository\EtudiantRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user_profile")
     */
    public function index(EtudiantRepository $etudiantRepository): Response
    {
        $mescours=null;
        $user=$this->getUser();
        if($user){
        $email=$user->getEmail();
        $etudiant=$etudiantRepository->findOneByEmail($email);
        if($etudiant){
        $mescours=$etudiant->getCours();}
        }
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
            'mescours'=>$mescours,
        ]);
    }


    /**
     * @Route("/user/index", name="user_index")
     */
    public function list(UserRepository $userRepository): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'UserController',
            'users' =>$userRepository->findAll()
        ]);
    }


    /**
     * @Route("/enseignant", name="enseignant_index")
     */
    public function listEnseignant(UserRepository $userRepository): Response
    {
      $users=  $userRepository->findAll();
     
      $enseignants = $this->getDoctrine()
      ->getRepository(Enseignant::class)
      ->findAll();
       // dd($users);
        return $this->render('user/enseignant_list.html.twig', [
           
            'enseignants'=>$userRepository->findByRoles(['["ROLE_ADMIN"]']),
           //'enseignants'=>$enseignants
        ]);
    }




    
    /**
     * @Route("/etudiant", name="etudiant_index")
     */
    public function listEtudiant(UserRepository $userRepository): Response
    {
        $users=$userRepository->findOneById(50);
       // dd($users);
        return $this->render('user/etudiant_list.html.twig', [
            'controller_name' => 'UserController',
            'enseignants'=>$userRepository->findBy(["ROLE_USER"])
         //   'enseignants'=>$userRepository->findAll()
        ]);
    }




    /**
     * @Route("/enseignant/new", name="enseignant_new")
     */
    public function newEnseignant(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setRoles(array("ROLE_ADMIN"));
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $file=$user->getImage();
            $fileName=md5(uniqid()).'.'.$file->guessExtension();
            try{
                $file->move($this->getParameter('images_directory'),$fileName);
            }catch(FileException $e){

            }
            $user->setImage($fileName);


            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();


            return $this->redirectToRoute('enseignant_neww',['id'=>$user->getId()]);
        }

        return $this->render('user/enseignant_new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/etudiant/new", name="etudiant_new")
     */
    public function newEtudiant(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setRoles(array("ROLE_USER"));
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $file=$user->getImage();
            $fileName=md5(uniqid()).'.'.$file->guessExtension();
            try{
                $file->move($this->getParameter('images_directory'),$fileName);
            }catch(FileException $e){

            }
            $user->setImage($fileName);


            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();


            return $this->redirectToRoute('etudiant_neww',['id'=>$user->getId()]);
        }

        return $this->render('user/etudiant_new.html.twig', [
            'form' => $form->createView(),
        ]);
    }



    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(RegistrationFormType::class,  $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
             $file= $user->getImage();
            $fileName=md5(uniqid()).'.'.$file->guessExtension();
            try{
                $file->move($this->getParameter('images_directory'),$fileName);
            }catch(FileException $e){

            }
            $user->setImage($fileName);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' =>  $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/editt", name="user_editt", methods={"GET","POST"})
     */
    public function editt(Request $request, User $user): Response
    {
        $form = $this->createForm(RegistrationFormType::class,  $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
             $file= $user->getImage();
            $fileName=md5(uniqid()).'.'.$file->guessExtension();
            try{
                $file->move($this->getParameter('images_directory'),$fileName);
            }catch(FileException $e){

            }
            $user->setImage($fileName);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/editt.html.twig', [
            'user' =>  $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }


    /**
     * @Route("/{id}", name="user_delete", methods={"POST"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'. $user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove( $user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('homeadmin', [], Response::HTTP_SEE_OTHER);
    }

}
