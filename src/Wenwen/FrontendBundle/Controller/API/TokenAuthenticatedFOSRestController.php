<?php

namespace Wenwen\FrontendBundle\Controller\API;

use FOS\RestBundle\Controller\FOSRestController;

class TokenAuthenticatedFOSRestController extends FOSRestController implements TokenAuthenticatedController
{
    public function getErrorMessages(\Symfony\Component\Form\Form $form) {
        $errors = array();

        if ($form->hasChildren()) {
            foreach ($form->getChildren() as $child) {
                if (!$child->isValid()) {
                    $errors[$child->getName()] = $this->getErrorMessages($child);
                }
            }
        } else {
            foreach ($form->getErrors() as $key => $error) {
                $errors[] = $error->getMessage();
            }
        }

        return $errors;
    }
}