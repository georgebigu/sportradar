<?php

namespace App\Form;

use App\Entity\Game;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('homeTeam', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Home Team',
                'label_attr' => ['class' => 'form-label']
            ])
            ->add('awayTeam', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Away Team',
                'label_attr' => ['class' => 'form-label']
            ])
            ->add('homeTeamScore', IntegerType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Home Team Score',
                'label_attr' => ['class' => 'form-label']
            ])
            ->add('awayTeamScore', IntegerType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Away Team Score',
                'label_attr' => ['class' => 'form-label']
            ])
            ->add('isFinished', ChoiceType::class, [
                'choices' => [
                    'Finished' => true,
                    'Not Finished' => false
                ],
                'attr' => ['class' => 'form-select'],
                'label' => 'Game Status',
                'label_attr' => ['class' => 'form-label']
            ])
            ->add('createdAt', DateTimeType::class, [
                'date_label' => 'Started On',
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'form-label']
            ])
            ->add('submit', SubmitType::class, [
                'attr' => ['class' => 'w-100 btn btn-primary btn-lg']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Game::class,
        ]);
    }
}
