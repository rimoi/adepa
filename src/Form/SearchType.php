<?php

namespace App\Form;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
               ->add('q', TextType::class, [
                   'required' => false,
                   'attr' => [
                       'placeholder' => 'Recherche (Ex: Educateur)',
                       'class' => 'js-meili-search'
                   ]
               ])
               ->add('categories', EntityType::class, [
                   'class' => Category::class,
                   'query_builder' => static function (CategoryRepository $repository) {
                       return $repository->createQueryBuilder('t')
                           ->innerJoin('t.parent', 'p')
                           ->addOrderBy('t.title', 'ASC');
                   },
                   'group_by' => static function (Category $choice) {
                       return $choice->getParent()->getTitle();
                   },
                   'mapped' => false,
                   'label' => 'Rechercher par type de post',
                   'multiple' => true,
                   'required' => false,
                   'attr' => [
                       'class' => 'js-select2',
//                       'data-add-select2' => true,
                       'style' => "width: 100%;",
                       'placeholder' => 'Rechercher par type de posts'
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
