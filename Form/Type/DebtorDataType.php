<?php

namespace Oro\Bundle\InfinitePayBundle\Form\Type;

use Oro\Bundle\ValidationBundle\Validator\Constraints\Email;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * {@inheritdoc}
 */
class DebtorDataType extends AbstractType
{
    const BLOCK_PREFIX = 'oro_infinite_pay_debtor_data';


    public static function getAvailableLegalTypes()
    {
        return [
            'AG' => 'ag',
            'eG' => 'eg',
            'EK' => 'ek',
            'e.V.' => 'ev',
            'Freelancer' => 'freelancer',
            'GbR' => 'gbr',
            'GmbH' => 'gmbh',
            'GmbH iG' => 'gmbh_ig',
            'GmbH & Co. KG' => 'gmbh_co_kg',
            'KG' => 'kg',
            'KgaA' => 'kgaa',
            'Ltd' => 'ltd',
            'Ltd co KG' => 'ltd_co_kg',
            'OHG' => 'ohg',
            'öffl. Einrichtung' => 'offtl_einrichtung',
            'Sonst. KapitalGes' => 'sonst_pers_ges',
            'Stiftung' => 'stiftung',
            'UG' => 'ug',
            'Einzelunternehmen, Kleingewerbe, Handelsvetreter' => 'einzel',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'email',
                TextType::class,
                [
                    'constraints' => [new NotBlank(), new Email()],
                ]
            )
            ->add(
                'legal_form',
                ChoiceType::class,
                [
                    'choices' => self::getAvailableLegalTypes(),
                ]
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' => 'oro.infinite_pay.methods.debtor_data.label',
            'csrf_protection' => false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return self::BLOCK_PREFIX;
    }
}
