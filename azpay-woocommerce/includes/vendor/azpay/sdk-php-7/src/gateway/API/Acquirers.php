<?php
/**
 * Created by PhpStorm.
 * User: brunopaz
 * Date: 2018-12-28
 * Time: 01:07
 */

namespace Gateway\API;


abstract class Acquirers
{
    const CIELO_BUY_PAGE_LOJA         = 1;
    const CIELO_BUY_PAGE_CIELO        = 2;
    const REDE_KOMERCI_WEBSERVICE     = 3;
    const REDE_KOMERCI_INTEGRADO      = 4;
    const VERANCARD                   = 5;
    const ELAVON                      = 6;
    const PAGSEGURO                   = 7;
    const PAYPAL_EXPRESS_CHECKOUT     = 8;
    const PAGSEGURO_CHECKOUT_EXPRESSO = 9;
    const BRADESCO                    = 10;
    const BRADESCO_SHOPFACIL          = 19;
    const BRADESCO_SHOPFACIL_BOLETO   = 18;
    const ITAU_SHOPLINE               = 20;
    const STONE                       = 20;
    const GETNET                      = 22;
    const GLOBAL_PAYMENT              = 24;
    const FIRSTDATA                   = 25;
    const CIELO_V3                    = 26;
    const REDE_E_REDE                 = 27;
    const ADIQ                        = 28;
    const PAYPAL_PLUS                 = 29;
    const GETNET_V1                   = 30;
    const WORLDPAY                    = 31;
    const GRANITO                     = 32;
    const AZPAY                       = 33;

}