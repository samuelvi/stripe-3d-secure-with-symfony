<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Stripe;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @Route("/", name="payment_")
 */
class PaymentController extends AbstractController
{
    private $stripePk;
    private $stripeSk;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->stripePk = $parameterBag->get('STRIPE_PUBLISHABLE_KEY');
        $this->stripeSk = $parameterBag->get('STRIPE_SECRET_KEY');
    }

    /**
     * @Route("/", name="payment_index")
     */
    public function index()
    {
        Stripe\Stripe::setApiKey($this->stripeSk);

        $session = Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'name' => 'My title',
                'description' => 'description of added items',
                'images' => ['https://image.flaticon.com/icons/svg/352/352776.svg'],
                'amount' => 500,
                'currency' => 'usd',
                'quantity' => 1,
            ]],
            'success_url' => $this->generateUrl('payment_success', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('payment_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        $params = [
            'stripe_pk' => $this->stripePk,
            'CHECKOUT_SESSION_ID' => $session->id,
        ];
        return $this->render('payment.html.twig', $params);
    }

    /**
     * @Route("/success", name="success")
     */
    public function success()
    {
        return new Response('SUCESS');
    }

    /**
     * @Route("/cancel", name="cancel")
     */
    public function cancel()
    {
        return new Response('CANCEL');
    }
}