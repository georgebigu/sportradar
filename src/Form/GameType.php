<?php

namespace App\Form;

use App\Entity\Game;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
                'attr' => ['class' => 'text']
            ])
            ->add('awayTeam', TextType::class, [
                'attr' => ['class' => 'text']
            ])
            ->add('homeTeamScore', IntegerType::class, [
                'attr' => ['class' => 'text']
            ])
            ->add('awayTeamScore', IntegerType::class, [
                'attr' => ['class' => 'text']
            ])
            ->add('isFinished', ChoiceType::class, [
                'choices' => [
                    'Yes' => true,
                    'No' => false
                ],
                'attr' => ['class' => 'text']
            ])
            ->add('submit', SubmitType::class, [
                'attr' => ['class' => 'w-100 btn btn-sm btn-outline-primary mb-2']
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
