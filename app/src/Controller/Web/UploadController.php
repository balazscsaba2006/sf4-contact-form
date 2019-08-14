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
            $result = $csvHandler->validateAndSave($records);
            $recordsCount = $csv->count();

            // all records saved successfully
            if ($recordsCount === $result->countSaved()) {
                $this->addFlash('success', 'Nice job! The uploaded file was successfully saved.');
            }
            // no row could be saved
            elseif ($csv->count() === $result->countErrors()) {
                $this->addFlash('danger', 'Whoops. All records are invalid, nothing could be saved.');
            }
            // partially saved rows
            else {
                $this->addFlash('warning', sprintf('Successfully saved %d of %d records. Check rows: %s',
                    $result->countSaved(),
                    $recordsCount,
                    '#'.implode(', #', $result->getErrors()->keys())
                ));
            }

            return $this->redirect($this->generateUrl('upload'));
        }

        return $this->render('upload/index.html.twig', [
            'form' => $form->createView(),
            'columnsCount' => $this->getParameter('csv_columns_count'),
            'firstLineAsHeader' => $this->getParameter('csv_first_line_as_header'),
            'delimiter' => $this->getParameter('csv_delimiter'),
        ]);
    }
}
