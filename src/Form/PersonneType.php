<?php

namespace App\Form;

use App\Entity\Hobby;
use App\Entity\Personne;
use App\Entity\Profile;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class PersonneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('prenom')
            ->add('age')
            ->add('image', FileType::class, [
                'label' => 'Déposez votre image de profil',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/gif',
                            'image/jpg',
                            'image/jpeg',
                            'image/png'
                        ],
                        'mimeTypesMessage' => 'Veuillez insérer une image dans une des extensions suivantes: .gif, .jpg, .jpeg, .png',
                    ])
                ]
            ])
            ->add('profile', EntityType::class, [
                'expanded' => true,
                'required' => false,
                'class' => Profile::class,
                'multiple' => false
            ])
            ->add('hobbies', EntityType::class, [
                'expanded' => false,
                'class' => Hobby::class,
                'multiple' => true,
                'query_builder' => function (EntityRepository $entityRepo) {
                    return $entityRepo->createQueryBuilder('hobbyAlias')
                        ->orderBy('hobbyAlias.designation', 'ASC');
                },
                'choice_label' => 'designation' //permet de remplacer la fonction _toString dans les entités (ici seulement pour hobby)
            ])
            ->add('job')
            ->add('ajouter', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Personne::class,
        ]);
    }
}