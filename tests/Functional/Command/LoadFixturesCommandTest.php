<?php

namespace App\Tests\Functional\Command;

use App\Tests\helper\FunctionalTrait;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class LoadFixturesCommandTest extends KernelTestCase
{
    use FunctionalTrait;

    public function testExecute(): void
    {
        self::bootKernel();

        $products = $this->getProductRepository()->findAll();
        foreach ($products as $product) {
            $this->getEntityManager()->remove($product);
        }
        $this->getEntityManager()->flush();

        $application = new Application(self::$kernel);

        $command       = $application->find('app:load-fixtures');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $commandTester->assertCommandIsSuccessful();

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Данные загружены.', $output);
    }
}
