<?php

namespace AppBundle\Twig;

use Money\Currencies\ISOCurrencies;
use Money\Formatter\IntlMoneyFormatter;

class AppExtension extends \Twig_Extension {
    public function getFilters() {
        return array (
                new \Twig_SimpleFilter('money', array (
                        $this,
                        'moneyFilter' 
                )) 
        );
    }
    public function moneyFilter($money) {
        $currencies = new ISOCurrencies();
        
        $numberFormatter = new \NumberFormatter('de_DE', \NumberFormatter::CURRENCY);
        $moneyFormatter = new IntlMoneyFormatter($numberFormatter, $currencies);
        
        return $moneyFormatter->format($money);
    }
    public function getName() {
        return 'app_extension';
    }
}