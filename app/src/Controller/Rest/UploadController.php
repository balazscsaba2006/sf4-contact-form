<?php

namespace App\Controller\Rest;

use App\Form\UploadType;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UploadController
 * @package App\Controller\Rest
 */
class UploadController extends AbstractFOSRestController
{
    /**
     * @Rest\Post("/upload")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function postUpload(Request $request): Response
    {
        $form = $this->createForm(UploadType::class);
        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->handleView(View::create($form));
        }

        // todo: read and persist the CSV file entries
        // todo: add flash message with results (num succeeded / num failed / total rows)

        return $this->handleView(View::create(['status' => 'ok'], Response::HTTP_CREATED));
    }
}
