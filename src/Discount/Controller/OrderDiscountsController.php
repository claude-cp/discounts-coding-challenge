<?php

declare(strict_types=1);

namespace App\Discount\Controller;

use App\Discount\DTO\Input\OrderInput;
use App\Discount\Event\OrderReceivedEvent;
use App\Discount\Manager\OrderDiscountsManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * @Route("/order-discounts", name="order-discounts")
 */
class OrderDiscountsController extends AbstractController
{
    public function __construct(
        private readonly EventDispatcherInterface $dispatcher,
        private readonly OrderDiscountsManager $manager,
    ){
    }

    /**
     * @Route("/apply", methods={"POST"})
     * @ParamConverter(name="orderInput", options={"validate": true})
     */
    public function applyOrderDiscounts(OrderInput $orderInput): JsonResponse
    {
        $this->dispatcher->dispatch(new OrderReceivedEvent($orderInput));

        $orderOutput = $this->manager->processOrderAndApplyDiscounts($orderInput);

        return $this->json($orderOutput);
    }
}
