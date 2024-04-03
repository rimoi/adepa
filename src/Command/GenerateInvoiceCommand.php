<?php

namespace App\Command;

use App\DTO\InvoiceDTO;
use App\Entity\Booking;
use App\Entity\Facture;
use App\Entity\Invoice;
use App\Entity\Reservation;
use App\helper\ArrayHelper;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Templating\EngineInterface;
use Twig\Environment;

#[AsCommand(
    name: 'app:generate-invoice',
    description: 'Generate invoice for website',
)]
class GenerateInvoiceCommand extends Command
{

    private $numberInvoice = 0;

    private const FORMAT_INVOICE = 'm-Y';

    public function __construct(
        private EntityManagerInterface $entityManager,
        private Environment $templating,
        private ParameterBagInterface $parameterBag
    )
    {
        parent::__construct(null);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $mount = (new \DateTime())->format(self::FORMAT_INVOICE);
        $invoice = $this->entityManager->getRepository(Invoice::class)->findOneBy(['month' => $mount]);

        if (!$invoice) {
            $invoice = new Invoice();
            $invoice->setMonth($mount);

            $this->entityManager->persist($invoice);

            // créer les factures services
            $services = $this->entityManager->getRepository(Reservation::class)->getTerminateReservated();

            $invoiceDTOs = $this->createInvoiceDTO($services, $mount);

            if ($invoiceDTOs) {
                $this->createInvoice($invoiceDTOs, $invoice);
                $io->note(sprintf('%d factures services ont été créer', count($invoiceDTOs)));
            }

            // créer les factures mission
            $bookings = $this->entityManager->getRepository(Booking::class)->getFinishedBooking();
           
            $invoiceDTOs = $this->createInvoiceDTOBooking($bookings, $mount);

            if ($invoiceDTOs) {
                $this->createInvoiceBooking($invoiceDTOs, $invoice);
                $io->note(sprintf('%d factures réservation ont été créer', count($invoiceDTOs)));
            }

            $invoice->setNumberInvoice($this->numberInvoice);

            $this->entityManager->flush();
        }


        $io->success('Tous est OKAY !');

        return Command::SUCCESS;
    }

    private function createInvoiceDTO(array $services, string $mount): ?array
    {
        $services = ArrayHelper::groupBy($services, 'owner.id');

        if (!$services) {
            return null;
        }
        
        $invoiceDTOs = [];


        foreach ($services as $services) {
            $reservations = [];
            foreach ($services as $service) {
                $reservations[] = $service;
            }
            $invoiceDTO = new InvoiceDTO();
            $invoiceDTO->setNumero(sprintf('%s-%s', $mount, $this->numberInvoice));
            $invoiceDTO->setCustomer($service->getOwner());
            $invoiceDTO->setReservations($reservations);

            $invoiceDTOs[] = $invoiceDTO;

            ++$this->numberInvoice;
        }


        return $invoiceDTOs;
    }

    private function createInvoiceDTOBooking(array $bookings, string $mount): ?array
    {
        $bookings = ArrayHelper::groupBy($bookings, 'user.id');

        if (!$bookings) {
            return null;
        }

        $invoiceDTOs = [];


        foreach ($bookings as $services) {
            $reserveds = [];
            foreach ($services as $booking) {
                $reserveds[] = $booking;
            }

            // A supprimé 👇
//            $booking->getMission()->setPrice(1253);
            // A supprimé 👆

            $invoiceDTO = new InvoiceDTO();
            $invoiceDTO->setNumero(sprintf('%s-%s', $mount, $this->numberInvoice));
            $invoiceDTO->setCustomer($booking->getUser());
            $invoiceDTO->setBookings($reserveds);

            $invoiceDTOs[] = $invoiceDTO;

            ++$this->numberInvoice;
        }


        return $invoiceDTOs;
    }

    /**
     * @param InvoiceDTO[] $invoiceDTOs
     */
    private function createInvoice(array $invoiceDTOs, Invoice $invoice)
    {
        foreach ($invoiceDTOs as $invoiceDTO) {
            // Créer une instance de Dompdf avec les options
            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $dompdf = new Dompdf($options);

            // Générer le contenu HTML de la facture
            $html = $this->templating->render('invoice/invoice_service.html.twig', [
                'facture' => $invoiceDTO,
            ]);

            // Charger le contenu HTML dans Dompdf
            $dompdf->loadHtml($html);

            // Rendre le document PDF
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            $filename = $invoiceDTO->getNumero().'.pdf';

            $directory = $this->parameterBag->get('app.invoice_directory') .'/'.$invoiceDTO->getCustomer()?->getId();

            if (!is_dir($directory)) {
                mkdir($directory, 0777, true);
            }

            file_put_contents(
                $directory.'/'.$filename,
                $dompdf->output()
            );

            $facture = new Facture();
            $facture->setName($filename);
            $facture->setUser($invoiceDTO->getCustomer());
            $facture->setReference($invoiceDTO->getNumero());

            $this->entityManager->persist($facture);

            $facture->setInvoice($invoice);
            $invoice->addFacture($facture);
        }
    }

    /**
     * @param InvoiceDTO[] $invoiceDTOs
     */
    private function createInvoiceBooking(array $invoiceDTOs, Invoice $invoice)
    {
        foreach ($invoiceDTOs as $invoiceDTO) {
            // Créer une instance de Dompdf avec les options
            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $dompdf = new Dompdf($options);

            // Générer le contenu HTML de la facture
            $html = $this->templating->render('invoice/invoice_booking.html.twig', [
                'facture' => $invoiceDTO,
            ]);

            // Charger le contenu HTML dans Dompdf
            $dompdf->loadHtml($html);

            // Rendre le document PDF
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            $filename = $invoiceDTO->getNumero().'.pdf';

            $directory = $this->parameterBag->get('app.invoice_directory') .'/'.$invoiceDTO->getCustomer()?->getId();

            if (!is_dir($directory)) {
                mkdir($directory, 0777, true);
            }

            file_put_contents(
                $directory.'/'.$filename,
                $dompdf->output()
            );

            $facture = new Facture();
            $facture->setName($filename);
            $facture->setUser($invoiceDTO->getCustomer());
            $facture->setReference($invoiceDTO->getNumero());

            $this->entityManager->persist($facture);

            $facture->setInvoice($invoice);
            $invoice->addFacture($facture);
        }
    }


}
