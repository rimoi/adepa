<?php

namespace App\helper;

use Symfony\Component\Form\FormInterface;

class FormHelper
{
    // https://symfonycasts.com/screencast/symfony-rest2/validation-errors-response
    public static function getErrorsFromForm(FormInterface $form, bool $label = false): array
    {
        $errors = [];
        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }

        foreach ($form->all() as $childForm) {
            if ($childForm instanceof FormInterface) {
                if ($childErrors = self::getErrorsFromForm($childForm)) {
                    if ($label && $childForm->getConfig()->getOption('label')) {
                        $errors[$childForm->getConfig()->getOption('label')] = $childErrors;
                    } else {
                        $errors[$childForm->getName()] = $childErrors;
                    }
                }
            }
        }

        return $errors;
    }

}