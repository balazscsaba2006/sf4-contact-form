<?php

namespace App\Controller\Web;

use App\Csv\HandlerInterface;
use App\Form\UploadType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UploadController.
 */
class UploadController extends AbstractController
{
    /**
     * @Route("/upload", name="upload")
     *
     * @param Request          $request
     * @param HandlerInterface $csvHandler
     *
     * @return RedirectResponse|Response
     */
    public function index(Request $request, HandlerInterface $csvHandler)
    {
        $form = $this->createForm(UploadType::class);
        $form->add('upload', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $csvFile */
            $csvFile = $form['file']->getData();

            $csv = $csvHandler->parse($csvFile->getPathname());
            $records = $csvHandler->getRecords($csv);
            $errors = $csvHandler->validateAndSave($records);

            // todo: read and persist the CSV file entries
            // todo: add flash message with results (num succeeded / num failed / total rows)

            $this->addFlash('success', 'Nice job! The uploaded file was successfully saved.');

            return $this->redirect($this->generateUrl('upload'));
        }

        return $this->render('upload/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
