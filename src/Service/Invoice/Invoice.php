<?php


namespace App\Service\Invoice;


class Invoice
{

    /**
     * @var string[]
     */
    private $fileFormat = [
        'Customer',
        'Vat number',
        'Document number',
        'Type',
        'Parent document',
        'Currency',
        'Total',
    ];

    /**
     * @var array
     */
    private $invoiceData;

    /**
     * @var array
     */
    private $currencies;

    /**
     * @var Currency
     */
    private $outputCurrency;

    /**
     * @param array $invoiceData
     */
    public function setData(array $invoiceData)
    {
        $this->invoiceData = $invoiceData;
    }

    /**
     * @param array $currencies
     */
    public function setCurrencies(array $currencies)
    {
        $this->currencies = $currencies;
    }

    /**
     * @param Currency $currency
     */
    public function setOutputCurrency(Currency $currency)
    {
        $this->outputCurrency = $currency;
    }

    public function getTotals($vat = '')
    {
        $total = [];
        $client = [];
        $documents = [];
        foreach ($this->invoiceData as $key => $invoiceItem) {

            $documents[] = $invoiceItem['Document number'];
            if (!empty($invoiceItem['Parent document']) && !in_array($invoiceItem['Parent document'], $documents)) {
                throw new \Exception('Missing main invoice');
            }

            if (!isset($total[$invoiceItem['Vat number']])) {
                $total[$invoiceItem['Vat number']] = 0;
            }

            if (!isset($client[$invoiceItem['Vat number']])) {
                $client[$invoiceItem['Vat number']] = $invoiceItem['Customer'];
            }

            $exchangeRate = 1;
            $mainCurrency = '';
            if (isset($this->currencies[$invoiceItem['Currency']])) {
                $exchangeRate = $this->currencies[$invoiceItem['Currency']]->getExchangeRate();

                if (1 === $exchangeRate) {
                    $mainCurrency = $this->currencies[$invoiceItem['Currency']]->getAbbreviation();
                }
            } else {
                throw new \Exception('Unsupported Currency!');
            }

            switch ($invoiceItem['Type']) {
                case 1:
                case 3:
                    $total[$invoiceItem['Vat number']] += ($invoiceItem['Total'] * $exchangeRate);
                    break;
                case 2:
                    $total[$invoiceItem['Vat number']] -= ($invoiceItem['Total'] * $exchangeRate);
                    break;
            }
        }


        foreach ($total as $key => $totalItem) {
            $total[$key] = $totalItem * $this->outputCurrency->getExchangeRate();
        }

        return [
            'clients' => $client,
            'totals' => $total,
            'mainCurrency' => $mainCurrency,
            'outputCurrency' => $this->outputCurrency->getAbbreviation(),
        ];
    }
}