<?php

declare(strict_types=1);

namespace Setono\SyliusGiftCardPlugin\Api\Controller\Action;

use Setono\SyliusGiftCardPlugin\EmailManager\GiftCardEmailManagerInterface;
use Setono\SyliusGiftCardPlugin\Model\GiftCardInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class ResendGiftCardEmailAction
{
    private GiftCardEmailManagerInterface $giftCardEmailManager;

    public function __construct(GiftCardEmailManagerInterface $giftCardEmailManager)
    {
        $this->giftCardEmailManager = $giftCardEmailManager;
    }

    public function __invoke(GiftCardInterface $data): Response
    {
        $giftCard = $data;
        if (($order = $giftCard->getOrder()) !== null) {
            $this->giftCardEmailManager->sendEmailWithGiftCardsFromOrder($order, [$giftCard]);
        } elseif (($customer = $giftCard->getCustomer()) !== null) {
            $this->giftCardEmailManager->sendEmailToCustomerWithGiftCard($customer, $giftCard);
        } else {
            throw new BadRequestHttpException();
        }

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
