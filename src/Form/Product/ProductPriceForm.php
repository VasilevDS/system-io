<?php
declare(strict_types = 1);

namespace App\Form\Product;

use App\DTO\Product\ProductPriceDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProductPriceForm extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->add(
                'product',
                TextType::class,
                [
                    'constraints' => [
                        new NotBlank(),
                    ],
                ],
            )
            ->add(
                'taxNumber',
                TextType::class,
                [
                    'constraints' => [
                        new NotBlank(),
                    ],
                ],
            )
            ->add('couponCode', TextType::class)
            ->add('paymentProcessor', TextType::class);
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'data_class' => ProductPriceDTO::class,
        ]);
    }
}
