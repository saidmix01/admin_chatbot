<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!defined('SITE_NAME')) {
    define('SITE_NAME', 'WebColSoluciones');
}

if (!function_exists('get_wompi_config')) {
    function get_wompi_config()
    {
        // Datos para entorno sandbox (pruebas)
        $sandbox = [
            'public_key'     => 'pub_test_6uCq9iOmKFsSBl4OObx4NOXCSfB84AC0',
            'integrity_key'    => 'test_integrity_Al1FA3L5lRVFrtt6WYbLoA0muczYytkq',
            'checkout_url'   => 'https://checkout.wompi.co/p/',
            'transaction_url'=> 'https://sandbox.wompi.co/v1/transactions/',
        ];

        // Datos para entorno producción
        $production = [
            'public_key'     => 'pub_prod_JvQW66OxePKu4aVKRf5ArUi9gOTANRwX',
            'integrity_key'    => 'prod_integrity_BNFgHJOFYPfsB7fkIGjfSubVOc2s3XT4',
            'checkout_url'   => 'https://checkout.wompi.co/p/',
            'transaction_url'=> 'https://production.wompi.co/v1/transactions/',
        ];

        return (ENVIRONMENT === 'production') ? $production : $sandbox;
    }
}

if (! function_exists('hash_pass')) {
    function hash_pass($plain, $cost = 12) {
        return password_hash($plain, PASSWORD_BCRYPT, ['cost' => $cost]);
    }
}

if (! function_exists('check_pass')) {
    function check_pass($plain, $hash) {
        return password_verify($plain, $hash);
    }
}

