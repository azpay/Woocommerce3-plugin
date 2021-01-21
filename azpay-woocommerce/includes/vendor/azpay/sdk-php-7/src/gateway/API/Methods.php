<?php
/**
 * Created by PhpStorm.
 * User: brunopaz
 * Date: 2018-12-28
 * Time: 01:27
 */

namespace Gateway\API;


/**
 * Class Methods
 *
 * @package Gateway\API
 */
abstract class Methods
{
    const CREDIT_CARD_NO_INTEREST             = 1;
    const CREDIT_CARD_INTEREST_BY_MERCHANT    = 2;
    const CREDIT_CARD_INTEREST_BY_ISSUER      = 3;
    const DEBIT_CARD                          = 4;
    const CIELO_SUBSCRIPTION_INITIAL          = 5;
    const PAGSEGUROV4_SUBSCRIPTION_INITIAL    = 6;
    const PAGSEGUROV4_SUBSCRIPTION_SUBSEQUENT = 7;
}