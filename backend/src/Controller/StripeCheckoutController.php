<?php

namespace App\Controller;

use App\Entity\Pedido;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class StripeCheckoutController extends AbstractController
{
    #[Route('/api/stripe/create-checkout', methods: ['POST'])]
    public function createCheckout(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $pedidoId = $data['pedidoId'];

        $pedido = $em->getRepository(Pedido::class)->find($pedidoId);

        if (!$pedido) {
            return new JsonResponse(['error' => 'Pedido no encontrado'], 404);
        }

        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => "Pago pedido #$pedidoId",
                    ],
                    'unit_amount' => intval($pedido->getTotal() * 100),
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => 'http://localhost:5173/pago/success',
            'cancel_url' => 'http://localhost:5173/pago/cancel',
            'metadata' => [
                'pedidoId' => $pedidoId
            ]
        ]);

        return new JsonResponse(['id' => $session->id]);
    }
}
