<?php

namespace App\Controller\Rest;

use App\Csv\HandlerInterface;
use App\Form\UploadType;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UploadController.
 */
class UploadController extends AbstractFOSRestController
{
    /**
     * @Rest\Post("/upload")
     *
     * @param Request          $request
     * @param HandlerInterface $csvHandler
     *
     * @return Response
     */
    public function postUpload(Request $request, HandlerInterface $csvHandler): Response
    {
        $form = $this->createForm(UploadType::class);
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
                return $this->handleView(View::create(['status' => 'ok'], Response::HTTP_CREATED));
            }

            // no row could be saved
            if ($csv->count() === $result->countErrors()) {
                $errors = $result->getErrorsAsArray();
                foreach ($errors as $error) {
                    $form['file']->addError(new FormError($error));
                }

                return $this->handleView(View::create($form));
            }

            // partially saved rows
            $data = [
                'status' => 'partially_ok',
                'message' => 'Validation Partially Failed',
                'errors' => [
                    'children' => [
                        'file' => [
                            'errors' => [],
                        ],
                    ],
                ],
            ];

            $errors = $result->getErrorsAsArray();
            foreach ($errors as $error) {
                $data['errors']['children']['file']['errors'][] = $error;
            }

            return $this->handleView(View::create($data, Response::HTTP_CREATED));
        }

        // empty requests are not submitted; trigger submission manually to return errors
        if (!$form->isSubmitted()) {
            $form->submit([]);
        }

        return $this->handleView(View::create($form));
    }
}
