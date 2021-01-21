<?php
    /**
     * Created by PhpStorm.
     * User: brunopaz
     * Date: 2018-12-28
     * Time: 01:27
     */

    namespace Gateway\API;


    /**
     * Class Environment
     *
     * @package Gateway\API
     */
    abstract class Environment
    {
        /**
         *
         */
        const SANDBOX = "SANDBOX";
        /**
         *
         */
        const PRODUCTION = "PRODUCTION";

        /**
         *
         */
        //const SANDBOX_URL = "http://0.0.0.0:8888";
        const SANDBOX_URL = "https://evaluation-api.azpay.services";
        /**
         *
         */
        const PRODUCTION_URL = "https://api.azpay.services";

        /**
         * @return string
         */
        public static function getSandboxUrl()
        {
            return self::SANDBOX_URL;
        }

        /**
         * @return string
         */
        public static function getProductionUrl()
        {
            return self::PRODUCTION_URL;
        }

    }
