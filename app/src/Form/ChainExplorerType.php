<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChainExplorerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('asset', ChoiceType::class, [
                'choices' => [
                    'BTC' => 'btc',
                    'ETH' => 'eth'
                ]
            ])
            ->add('address', TextType::class)
            ->add('dateFrom', DateType::class, [
                'widget'    => 'single_text',
                'format'    => 'yyyy-MM-dd'
            ])
            ->add('dateTo', DateType::class, ['widget' => 'single_text'])
            ->add('threshold', NumberType::class)
            ->add('search', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
