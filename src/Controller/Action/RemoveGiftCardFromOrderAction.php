<?php

declare(strict_types=1);

namespace Setono\SyliusGiftCardPlugin\Controller\Action;

use Setono\SyliusGiftCardPlugin\Applicator\GiftCardApplicatorInterface;
use Setono\SyliusGiftCardPlugin\Model\GiftCardInterface;
use Setono\SyliusGiftCardPlugin\Model\OrderInterface;
use Setono\SyliusGiftCardPlugin\Resolver\RedirectUrlResolverInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Webmozart\Assert\Assert;

final class RemoveGiftCardFromOrderAction
{
    private CartContextInterface $cartContext;

    private GiftCardApplicatorInterface $giftCardApplicator;

    private RedirectUrlResolverInterface $redirectRouteResolver;

    public function __construct(
        CartContextInterface $cartContext,
        GiftCardApplicatorInterface $giftCardApplicator,
        RedirectUrlResolverInterface $redirectRouteResolver
    ) {
        $this->cartContext = $cartContext;
        $this->giftCardApplicator = $giftCardApplicator;
        $this->redirectRouteResolver = $redirectRouteResolver;
    }

    public function __invoke(Request $request): Response
    {
        /** @var OrderInterface|null $order */
        $order = $this->cartContext->getCart();
        Assert::notNull($order);

        /** @var string|GiftCardInterface $giftCard */
        $giftCard = $request->attributes->get('giftCard');

        $this->giftCardApplicator->remove($order, $giftCard);

        $session = $request->getSession();
        if ($session instanceof Session) {
            $session->getFlashBag()->add('success', 'setono_sylius_gift_card.gift_card_removed');
        }

        return new RedirectResponse($this->redirectRouteResolver->getUrlToRedirectTo($request, 'sylius_shop_cart_summary'));
    }
}
