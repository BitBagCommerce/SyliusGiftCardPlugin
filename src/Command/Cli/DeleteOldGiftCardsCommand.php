<?php

declare(strict_types=1);

namespace Setono\SyliusGiftCardPlugin\Command\Cli;

use Doctrine\Persistence\ObjectManager;
use Setono\SyliusGiftCardPlugin\Repository\GiftCardRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class DeleteOldGiftCardsCommand extends Command
{
    protected static $defaultName = 'setono:sylius-gift-card:delete-old-gift-cards';

    private GiftCardRepositoryInterface $giftCardRepository;

    private ObjectManager $giftCardManager;

    public function __construct(
        GiftCardRepositoryInterface $giftCardRepository,
        ObjectManager $giftCardManager,
        ?string $name = null
    ) {
        parent::__construct($name);

        $this->giftCardRepository = $giftCardRepository;
        $this->giftCardManager = $giftCardManager;
    }

    protected function configure(): void
    {
        $this->setDescription('Deletes gift cards older than the provided date.');
        $this->setHelp(<<<'EOF'
The <info>%command.name%</info> command deletes gift cards older than the provided date.

Date option accepts any date format accepted by PHP's <info>DateTime</info> class.
Such as "2020-01-01" or "2020-01-01 12:00:00" or "-3 years".

Period option accepts any period format accepted by PHP's <info>DateInterval</info> class.
Such as "P1Y" or "P1Y2M".
EOF
        );
        $this->addOption(
            'date',
            null,
            InputOption::VALUE_OPTIONAL,
            'The date to delete gift cards older than.',
            null
        );
        $this->addOption(
            'period',
            null,
            InputOption::VALUE_OPTIONAL,
            'The period to delete gift cards older than.',
            null
        );
    }

    public function run(InputInterface $input, OutputInterface $output): int
    {
        $date = $input->getOption('date');
        $period = $input->getOption('period');

        if (null === $date && null === $period) {
            $output->writeln('You must provide a date or a period.');

            return 0;
        }

        if (null !== $date && null !== $period) {
            $output->writeln('You must provide a date or a period, not both.');

            return 0;
        }

        try {
            if (null !== $date) {
                $thresholdDate = new \DateTimeImmutable($date);
            } else {
                $thresholdDate = new \DateTimeImmutable('now');
                $thresholdDate = $thresholdDate->sub(new \DateInterval($period));
            }
        } catch (\Exception $e) {
            $output->writeln('The provided date or period is not valid.');

            return 0;
        }

        $giftCards = $this->giftCardRepository->findCreatedBefore($thresholdDate);
        foreach ($giftCards as $giftCard) {
            $this->giftCardManager->remove($giftCard);
        }

        $this->giftCardManager->flush();

        $output->writeln(\sprintf('Deleted %d gift cards.', \count($giftCards)));

        return 1;
    }
}
