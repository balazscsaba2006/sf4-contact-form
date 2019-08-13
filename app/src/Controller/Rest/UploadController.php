<?php

namespace App\Controller\Rest;

use App\Csv\HandlerInterface;
use App\Form\UploadType;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
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
            // todo: read and persist the CSV file entries
            // todo: add flash message with results (num succeeded / num failed / total rows)

            return $this->handleView(View::create(['status' => 'ok'], Response::HTTP_CREATED));
        }

        // empty requests are not submitted; trigger submission manually to return errors
        if (!$form->isSubmitted()) {
            $form->submit([]);
        }

        return $this->handleView(View::create($form));
    }
}
