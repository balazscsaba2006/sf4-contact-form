<?php

namespace App\Controller\Api;

use App\Form\UploadType;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UploadController
 * @package App\Controller
 *
 * @Route("/api", name="api_")
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
    public function postUploadAction(Request $request): Response
    {
        $form = $this->createForm(UploadType::class);
        $data = json_decode($request->getContent(), true);
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
            // todo: read and persist the CSV file entries
            // todo: add flash message with results (num succeeded / num failed / total rows)

            return $this->handleView($this->view(['status' => 'ok'], Response::HTTP_CREATED));
        }

        return $this->handleView($this->view($form->getErrors()));
    }
}
