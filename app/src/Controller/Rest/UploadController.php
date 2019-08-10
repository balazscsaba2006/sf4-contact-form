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
     * @return View
     */
    public function postUpload(Request $request): View
    {
        $form = $this->createForm(UploadType::class);
        $data = json_decode($request->getContent(), true);
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
            // todo: read and persist the CSV file entries
            // todo: add flash message with results (num succeeded / num failed / total rows)

            return View::create(['status' => 'ok'], Response::HTTP_CREATED);
        }

        return View::create($form->getErrors());
    }
}
