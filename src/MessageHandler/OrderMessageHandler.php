<?php

namespace App\MessageHandler;

use App\Message\OrderMessage;
use App\Repository\OrderRepository;
use App\Service\Mailer;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
#[WithMonologChannel('order')]
class OrderMessageHandler
{

    public function __construct(
        private readonly Mailer $mailer,
        private readonly OrderRepository $orderRepository,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(OrderMessage $orderMessage): void
    {
        $order_data = $orderMessage->getContent();
        $order = $this->orderRepository->find($order_data);

        $this->mailer->notifyOrderMessage($order);

        //$this->logger->info('Check order data @order_data', ['order_data' => $order_data]);
        $this->logger->info('Check order ' . $order_data);

        //dd($order_data);
    }

}