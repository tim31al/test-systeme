<?php

namespace App\Command;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:load-fixtures',
    description: 'Фикстуры продукт.',
)]
class LoadFixturesCommand extends Command
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        foreach ($this->productGenerator() as $row) {
            [$name, $price] = $row;

            $product = new Product();
            $product
                ->setName($name)
                ->setPrice($price);

            $this->entityManager->persist($product);
        }

        $this->entityManager->flush();

        $io->success('Данные загружены.');

        return Command::SUCCESS;
    }

    private function productGenerator(): Generator
    {
        yield ['Iphone', 100];
        yield ['Наушники', 20];
        yield ['Чехол', 10];
    }
}
