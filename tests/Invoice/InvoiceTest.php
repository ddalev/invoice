<?php


namespace App\Tests\Invoice;

use App\Service\Invoice\Currency;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class InvoiceTest extends KernelTestCase
{

    public function testSomething()
    {
        self::bootKernel();

        $container = static::getContainer();
        /** @var \App\Service\Invoice\InvoiceService $invoiceService */
        $invoiceService = $container->get(\App\Service\Invoice\InvoiceService::class);

        $currency = 'EUR';
        $exchangeRate = 1;
        $fieldCurrencies = [];
        $fieldCurrencies[$currency] = new Currency($currency, $exchangeRate);


        $csvData = [
            [
                'Customer' => 'Vendor 1',
                'Vat number' => '123456789',
                'Document number' => '1000000264',
                'Type' => '1',
                'Parent document' => '1000000265',
                'Currency' => 'EUR',
                'Total' => '1600',
            ],
        ];


        $this->expectException(\Exception::class);
        $invoiceService->loadInvoice($csvData, $fieldCurrencies, $fieldCurrencies[$currency]);
    }
}