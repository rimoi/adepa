<?php

namespace App\Controller\BackOffice;

use App\Constant\Days;
use App\Entity\Facture;
use App\helper\ArrayHelper;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/invoice', name: 'admin_invoice_')]
#[IsGranted('ROLE_ADMIN')]
class InvoiceController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $invoices = $entityManager->getRepository(Facture::class)->findBy([]);

        $invoices = ArrayHelper::groupBy($invoices, 'invoice.month');

        $resultats = [];
        foreach ($invoices as $mount => $invoice) {
            $dateObj = date_create_from_format('m-Y', $mount);

            $mount = Days::MONTH[$dateObj->format('F')];

            $format = sprintf('%s - %s', $mount, $dateObj->format('Y'));

            $resultats[$format] = $invoice;
        }

        return $this->render('back_office/invoice/index.html.twig', [
            'factures' => $resultats,
        ]);
    }

    #[Route('/show/{id}', name: 'show_pdf')]
    public function showPdf(Facture $facture): Response
    {
        // Récupérez le chemin absolu du fichier PDF à afficher
        $pdfFilePath = $this->getParameter('app.invoice_directory') . '/' .$facture->getUser()?->getId().'/'.$facture->getName();

        // Afficher le PDF dans le navigateur
        return new BinaryFileResponse($pdfFilePath);
    }

    #[Route('/download/{id}', name: 'download_pdf')]
    public function downloadPdf(Facture $facture): Response
    {
        // Récupérez le chemin absolu du fichier PDF à afficher
        $pdfFilePath = $this->getParameter('app.invoice_directory') . '/' .$facture->getUser()?->getId().'/'.$facture->getName();
        $response = new BinaryFileResponse($pdfFilePath);

        // Définissez le nom de fichier à télécharger
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $facture->getName());

        return $response;
    }
}
