<?php

namespace App\Service;

class PdfService
{
    public function generateInvoice()
    {
        // Créer une instance de Dompdf avec les options
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $dompdf = new Dompdf($options);

        // Générer le contenu HTML de la facture
        $html = $this->renderView('facture/facture.html.twig', [
            'facture' => $factureData,
        ]);

        // Charger le contenu HTML dans Dompdf
        $dompdf->loadHtml($html);

        // Rendre le document PDF
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Envoyer le PDF en réponse
        $response = new Response($dompdf->output());
        $response->headers->set('Content-Type', 'application/pdf');

        return $response;
    }
}