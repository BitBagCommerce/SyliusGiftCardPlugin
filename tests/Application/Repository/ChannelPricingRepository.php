<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusGiftCardPlugin\Application\Repository;

use Setono\SyliusCatalogPromotionPlugin\Doctrine\ORM\ChannelPricingRepositoryTrait as CatalogPromotionChannelPricingRepositoryTrait;
use Setono\SyliusCatalogPromotionPlugin\Repository\ChannelPricingRepositoryInterface as CatalogPromotionChannelPricingRepositoryInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

class ChannelPricingRepository extends EntityRepository implements CatalogPromotionChannelPricingRepositoryInterface
{
    use CatalogPromotionChannelPricingRepositoryTrait;
}