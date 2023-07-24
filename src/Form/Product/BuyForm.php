<?php
declare(strict_types = 1);

namespace App\Form\Product;

use App\DTO\Product\BuyDTO;
use App\Service\Payment\PaymentService;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class BuyForm extends PurchaseDataForm
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder->add(
            'paymentProcessor',
            ChoiceType::class,
            [
                'choices' => [
                    PaymentService::PAYMENT_PROCESSOR_PAYPAL,
                    PaymentService::PAYMENT_PROCESSOR_STRIPE,
                ],
                'constraints' => [
                    new NotBlank(),
                ],
            ],
        );
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'data_class' => BuyDTO::class,
        ]);
    }
}
