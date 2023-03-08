<?php

namespace App\Form;

use App\Entity\Booking;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ValidateTimeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /**
         * @var Booking $booking
         */
        $booking = $options['data'];

        $started = $booking->getConfirmStarted() ? $booking->getConfirmStarted()->format('d/m/Y H:i') : '';
        $ended = $booking->getConfirmEnded() ? $booking->getConfirmEnded()->format('d/m/Y H:i') : '';

        $builder
            ->add('debut', TextType::class, [
                'label' => 'Début mission',
                'attr' => [
                    'class' => 'datepicker'
                ],
                'data' => $started,
                'mapped' => false
            ])
            ->add('fin', TextType::class, [
                'label' => 'Fin mission',
                'attr' => [
                    'class' => 'datepicker'
                ],
                'data' => $ended,
                'mapped' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}
