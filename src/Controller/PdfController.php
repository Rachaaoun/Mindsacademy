<?php

namespace App\Controller;

use App\Entity\Enseignant;
use App\Entity\Pdf;
use App\Form\PdfType;
use App\Repository\CoursRepository;
use App\Repository\PdfRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/pdf")
 */
class PdfController extends AbstractController
{
    /**
     * @Route("/", name="pdf_index", methods={"GET"})
     */
    public function index(PdfRepository $pdfRepository,CoursRepository $coursRepository): Response
    {
      
        return $this->render('pdf/index.html.twig', [
            'pdfs' => $pdfRepository->findAll(),
        ]);
    }


    /**
     * @Route("/{id}/cours", name="pdf_cours", methods={"GET"})
     */
    public function pdfBycours(PdfRepository $pdfRepository,$id,CoursRepository $coursRepository): Response
    {
        $cours=$coursRepository->findOneById($id);
    
        return $this->render('pdf/listparcours.html.twig', [
            'pdfs' => $pdfRepository->findByCours($cours),
            'enseignant'=>null
        ]);
    }
    /**
     * @Route("/new", name="pdf_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $pdf = new Pdf();
        $form = $this->createForm(PdfType::class, $pdf);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $brochureFile = $form->get('pdffile')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
               // $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $originalFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile->move(
                        $this->getParameter('brochures_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {

                }

                $pdf->setPdffile($newFilename);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($pdf);
            $entityManager->flush();

            return $this->redirectToRoute('pdf_index', [], Response::HTTP_SEE_OTHER);
        }
    }
        return $this->render('pdf/new.html.twig', [
            'pdf' => $pdf,
            'form' => $form->createView(),
        ]);
    }



    /**
     * @Route("/{id}", name="pdf_show", methods={"GET"})
     */
    public function show(Pdf $pdf): Response
    {
        return $this->render('pdf/show.html.twig', [
            'pdf' => $pdf,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="pdf_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Pdf $pdf): Response
    {
        $form = $this->createForm(PdfType::class, $pdf);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('pdf_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pdf/edit.html.twig', [
            'pdf' => $pdf,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="pdf_delete", methods={"POST"})
     */
    public function delete(Request $request, Pdf $pdf): Response
    {
        if ($this->isCsrfTokenValid('delete'.$pdf->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($pdf);
            $entityManager->flush();
        }

        return $this->redirectToRoute('pdf_index', [], Response::HTTP_SEE_OTHER);
    }
}
