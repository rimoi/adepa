<?php

namespace App\DTO;

use App\Entity\Booking;
use App\Entity\Reservation;
use App\Entity\User;

class InvoiceDTO
{
    /** @var ?string */
    private $numero;

    /** @var ?\DateTime */
    private $invoiceDate;

    /** @var ?User */
    private $customer;

    /** @var ?Reservation[] */
    private $reservations;

    /** @var ?Booking[] */
    private $bookings;

    public function __construct()
    {
        $this->invoiceDate = new \DateTime();
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(?string $numero): void
    {
        $this->numero = $numero;
    }

    public function getInvoiceDate(): ?\DateTime
    {
        return $this->invoiceDate;
    }

    public function getCustomer(): ?User
    {
        return $this->customer;
    }

    public function setCustomer(?User $customer): void
    {
        $this->customer = $customer;
    }

    public function getReservations(): ?array
    {
        return $this->reservations;
    }

    public function setReservations(?array $reservations): void
    {
        $this->reservations = $reservations;
    }

    public function getBookings(): ?array
    {
        return $this->bookings;
    }

    public function setBookings(?array $bookings): void
    {
        $this->bookings = $bookings;
    }
}