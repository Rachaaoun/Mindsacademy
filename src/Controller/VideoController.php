<?php

namespace App\Controller;

use App\Entity\Enseignant;
use App\Entity\Video;
use App\Form\VideoEnseignantType;
use App\Form\VideoType;
use App\Repository\CoursRepository;
use App\Repository\VideoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/videos")
 */
class VideoController extends AbstractController
{

    /**
     * @Route("/admin", name="video_list", methods={"GET"})
     */
    public function list(VideoRepository $videoRepository): Response
    {
        return $this->render('video/index.html.twig', [
            'videos' => $videoRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{id}/cours", name="video_cours", methods={"GET"})
     */
    public function videoBycours(VideoRepository $videoRepository,$id,CoursRepository $coursRepository): Response
    {
        $cours=$coursRepository->findOneById($id);
        
        return $this->render('video/listparcours.html.twig', [
            'videos' => $videoRepository->findByCours($cours),
            'enseignant'=>null
        ]);
    }

    /**
     * @Route("/new", name="video_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $video = new Video();
        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           // $file=$video->getVideo();
           // $fileName=md5(uniqid()).'.'.$file->guessExtension();
            $file2=$video->getImage();
            $fileName2=md5(uniqid()).'.'.$file2->guessExtension();
            try{
                $file2->move($this->getParameter('images_directory'),$fileName2);
            }catch(FileException $e){

            }
            $video->setImage($fileName2);
            try{
              //  $file->move($this->getParameter('videos_directory'),$fileName);
            }catch(FileException $e){

            }
          //  $video->setVideo($fileName);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($video);
            $entityManager->flush();

            return $this->redirectToRoute('video_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('video/new.html.twig', [
            'video' => $video,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/newByCours", name="video_new_enseignant", methods={"GET","POST"})
     */
    public function newbyCours(Request $request,$id,CoursRepository $coursRepository): Response
    {
        $cours=$coursRepository->findOneById($id);
        $video = new Video();
        $form = $this->createForm(VideoEnseignantType::class, $video);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file=$video->getVideo();
            $fileName=md5(uniqid()).'.'.$file->guessExtension();
            try{
                $file->move($this->getParameter('videos_directory'),$fileName);
            }catch(FileException $e){

            }
            $video->setVideo($fileName);
            $video->setCours($cours);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($video);
            $entityManager->flush();

            return $this->redirectToRoute('video_cours_enseignant', ['id'=>$id], Response::HTTP_SEE_OTHER);
        }

        return $this->render('video/new.html.twig', [
            'video' => $video,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="video_show", methods={"GET"})
     */
    public function show(Video $video): Response
    {
        return $this->render('video/show.html.twig', [
            'video' => $video,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="video_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Video $video): Response
    {
        $form = $this->createForm(VideoEnseignantType::class, $video);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('homeadmin', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('video/edit.html.twig', [
            'video' => $video,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="video_delete", methods={"POST"})
     */
    public function delete(Request $request, Video $video): Response
    {
        if ($this->isCsrfTokenValid('delete'.$video->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($video);
            $entityManager->flush();
        }

        return $this->redirectToRoute('homeadmin', [], Response::HTTP_SEE_OTHER);
    }
}
