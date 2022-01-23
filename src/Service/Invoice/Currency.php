<?php


namespace App\Service\Invoice;


class Currency
{

    /**
     * @var string[]
     */
    static $supporterCurrencies = [
        'EUR',
        'USD',
        'GBP',
        'BGN',
    ];

    /**
     * @var string
     */
    private $abb;

    /**
     * @var float
     */
    private $exchangeValue;

    /**
     * Currency constructor.
     * @param string $abb
     * @param float $exchangeValue
     * @throws \Exception
     */
    public function __construct(string $abb, float $exchangeValue)
    {
        if (!in_array($abb, self::$supporterCurrencies)) {
            throw new \Exception('Unsupported Currency');
        }

        $this->abb = $abb;
        $this->exchangeValue = (float) $exchangeValue;
    }

    /**
     * @return string
     */
    public function getAbbreviation()
    {
        return $this->abb;
    }

    /**
     * @return float
     */
    public function getExchangeRate()
    {
        return $this->exchangeValue;
    }

    /**
     * @return string[]
     */
    public static function getSupportedCurrencies()
    {
        return self::$supporterCurrencies;
    }
}