<?php
declare(strict_types = 1);

namespace App\Form\Product;

use App\DTO\Product\PurchaseDataDTO;
use App\Validator\Coupon\CouponCodeConstraint;
use App\Validator\Product\ProductIdConstraint;
use App\Validator\TaxNumber\TaxNumberConstraint;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class PurchaseDataForm extends AbstractType
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
                        new ProductIdConstraint(),
                    ],
                ],
            )
            ->add(
                'taxNumber',
                TextType::class,
                [
                    'constraints' => [
                        new NotBlank(),
                        new TaxNumberConstraint(),
                    ],
                ],
            )
            ->add(
                'couponCode',
                TextType::class,
                [
                    'constraints' => [
                        new CouponCodeConstraint()
                    ],
                ],
            )
            ->add('paymentProcessor', TextType::class);
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'data_class' => PurchaseDataDTO::class,
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getBlockPrefix(): string
    {
        return '';
    }
}
