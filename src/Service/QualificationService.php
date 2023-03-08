<?php

namespace App\Service;

use App\Entity\File;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class QualificationService
{
    private EntityManagerInterface $entityManager;
    private FileUploader $fileUploader;

    public function __construct(
        EntityManagerInterface $entityManager,
        FileUploader $fileUploader
    ){
        $this->entityManager = $entityManager;
        $this->fileUploader = $fileUploader;
    }

    public function addElement(FormInterface $form, string $type): void
    {
        $user = $form->getData();

        $getter = 'get' . ucfirst($type);
        $setter = 'set' . ucfirst($type);

        if (
            $form->has($type)
            && $form->get($type)->getData()
            && ($file = $form->get($type)->get('name')->getData())
        ) {
            $filename = $this->fileUploader->upload($file);

            if ($user->$getter()) {
                $newFile = $user->$getter();
            } else {
                $newFile = new File();
                $this->entityManager->persist($newFile);
            }
            $newFile->setName($filename);
            $user->$setter($newFile);
        }
    }

    public function addExperience(FormInterface $form, string $type): void
    {
        $user = $form->getData();

        foreach ($form->get($type) as $formExp) {

            $experience = $formExp->getData();

            if (
                $formExp->has('file')
                && ($file = $formExp->get('file')->get('name')->getData())
            ) {
                $filename = $this->fileUploader->upload($file);

                if ($formExp->get('file')->getData()->getId()) {
                    $newFile = $formExp->get('file')->getData();
                } else {
                    $newFile = new File();
                    $this->entityManager->persist($newFile);
                }
                $newFile->setName($filename);
                $experience->setFile($newFile);
            }

            $getter = ucfirst(substr($type,0,-1));

            $user->{'add' . $getter}($experience);
            $experience->setUser($user);
        }

    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }
}