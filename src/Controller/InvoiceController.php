<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Service\Invoice\InvoiceService;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class InvoiceController extends AbstractController
{
    /**
     * @Route("/invoice", name="invoice")
     */
    public function index(Request $request, InvoiceService $invoiceService): Response
    {
        //Get available currencies
        $currencies = \App\Service\Invoice\Currency::getSupportedCurrencies();

        $buildForm = $this->createFormBuilder()->add('file', FileType::class);

        //Generate currency exchange input fileds
        $buildCurrencySelect = [];
        foreach ($currencies as $currency) {
            $buildForm->add($currency, NumberType::class, ['label' => 'Exchange rate for '.$currency]);
            $buildCurrencySelect[$currency] = $currency;
        }

        //Select output currency
        $buildForm->add('outputCurrency', ChoiceType::class, [
            'choices'  => $buildCurrencySelect,
        ]);

        $buildForm->add('save', SubmitType::class, ['label' => 'Upload Invoice']);
        $form = $buildForm->getForm();

        $form->handleRequest($request);
        $invoiceCalculations = [];
        //Handle submit.
        if ($form->isSubmitted() && $form->isValid()) {
            $currencies = $invoiceService->sanitizeCurrencies($form);
            $invoiceData = $invoiceService->sanitizeFile($form);
            $invoiceCalculations = $invoiceService->loadInvoice($invoiceData, $currencies, $currencies[$form->get('outputCurrency')->getData()]);
        }

        return $this->renderForm('invoice/new.html.twig', [
            'form' => $form,
            'invoiceCalculations' => $invoiceCalculations,
        ]);
    }
}
