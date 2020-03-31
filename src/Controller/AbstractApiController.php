<?php

namespace App\Controller;

use App\Entity\SerializerContextInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class AbstractApiController extends AbstractController
{
    /**
     * @inheritDoc
     */
    protected function json($data, int $status = 200, array $headers = [], array $context = []): JsonResponse
    {
        if (empty ($context) && $data instanceof SerializerContextInterface) {
            $context = $data::getSerializationContext();
        }

        return parent::json($data, $status, $headers, $context);
    }

    /**
     * @param FormInterface $form
     * @return array
     */
    protected function getValidationErrors(FormInterface $form): array
    {
        $errors = [];
        $formErrors = $form->getErrors(true);
        foreach ($formErrors as $error) {
            $path = $error->getOrigin() ? $error->getOrigin()->getName() : '_all';
            $errors[$path] = $error->getMessage();
        }

        return $errors;
    }
}