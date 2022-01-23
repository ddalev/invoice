<?php

namespace App\Service\Invoice;

use Symfony\Component\Form\FormInterface;

class InvoiceService
{
    /**
     * @param FormInterface $form
     * @throws \Exception
     */
    public function loadInvoice(array $fileData, array $currencies, Currency $outputCurrency, $filterByVat = '') {
        $instance = new Invoice();
        $instance->setData($fileData);
        $instance->setCurrencies($currencies);
        $instance->setOutputCurrency($outputCurrency);
        return $instance->getTotals($filterByVat);
    }

    /**
     * @param FormInterface $form
     * @return array
     * @throws \Exception
     */
    public function sanitizeCurrencies(FormInterface $form) {
        $currencies = \App\Service\Invoice\Currency::getSupportedCurrencies();

        $fieldCurrencies = [];
        foreach ($currencies as $currency) {
            $exchangeRate = $form->get($currency)->getData();
            $fieldCurrencies[$currency] = new Currency($currency, $exchangeRate);
        }

        return $fieldCurrencies;
    }

    /**
     * @param FormInterface $form
     * @return array
     */
    public function sanitizeFile(FormInterface $form) {
        $file = $form->get('file')->getData();

        // Open the file
        if (($fileData = file($file->getPathname())) !== false) {

            $rows = array_map('str_getcsv', $fileData);
            // Read and process the lines.
            // Skip the first line if the file includes a header
            $header = array_shift($rows);
            $csv = [];
            foreach($rows as $row) {
                $csv[] = array_combine($header, $row);
            }
        }

        return $csv;
    }
}