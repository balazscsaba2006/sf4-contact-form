<?php

namespace App\Controller;

use App\Form\UploadType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UploadController
 * @package App\Controller
 */
class UploadController extends AbstractController
{
    /**
     * @Route("/upload", name="upload")
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function index(Request $request)
    {
        $form = $this->createForm(UploadType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $csvFile */
            $csvFile = $form['file']->getData();

            // todo: read and persist the CSV file entries
            // todo: add flash message with results (num succeeded / num failed / total rows)

            return $this->redirect($this->generateUrl('upload'));
        }

        return $this->render('upload/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
