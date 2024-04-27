<?php

namespace App\Form;

use App\Constant\CategoryType;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
               ->add('categories', EntityType::class, [
                   'class' => Category::class,
                   'query_builder' => static function (CategoryRepository $repository) {
                       return $repository->createQueryBuilder('t')
                           ->innerJoin('t.parent', 'p')
                           ->where('t.archived = :archived')
                           ->andWhere('t.type = :type')
                           ->setParameter('archived', false)
                           ->setParameter('type', CategoryType::MISSION)
                           ->addOrderBy('t.title', 'ASC');
                   },
                   'group_by' => static function (Category $choice) {
                       return $choice->getParent()->getTitle();
                   },
                   'mapped' => false,
                   'label' => 'Choisir plusieurs catégories de mission',
                   'multiple' => true,
                   'attr' => [
                       'class' => 'js-select2',
                       'style' => "width: 100%;",
                       'placeholder' => 'Vous pouvez choisir une/plusieurs catégories des missions'
                   ],
               ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
