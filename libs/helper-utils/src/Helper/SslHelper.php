<?php
/**
 * Created by PhpStorm.
 * User: Inhere
 * Date: 2018/5/4 0004
 * Time: 00:01
 */

namespace Toolkit\Helper;

/**
 * Class SslHelper
 * @package Toolkit\Helper
 * @from Wrench\Util\Ssl
 */
class SslHelper
{
    /**
     * Generates a new PEM File given the information
     *
     * @param string $pem_file the path of the PEM file to create
     * @param string $pem_passphrase the passphrase to protect the PEM file or if you don't want to use a
     *                                         passphrase
     * @param string $country_name the country code of the new PEM file. e.g.: EN
     * @param string $state_or_province_name the state or province name of the new PEM file
     * @param string $locality_name the name of the locality
     * @param string $organization_name the name of the organisation. e.g.: MyCompany
     * @param string $organizational_unit_name the organisation unit name
     * @param string $common_name the common name
     * @param string $email_address the email address
     * @return bool
     */
    public static function createPemFile(
        string $pem_file,
        string $pem_passphrase,
        string $country_name,
        string $state_or_province_name,
        string $locality_name,
        string $organization_name,
        string $organizational_unit_name,
        string $common_name,
        string $email_address
    ): bool {
        // Generate PEM file
        $dn = [
            'countryName' => $country_name,
            'stateOrProvinceName' => $state_or_province_name,
            'localityName' => $locality_name,
            'organizationName' => $organization_name,
            'organizationalUnitName' => $organizational_unit_name,
            'commonName' => $common_name,
            'emailAddress' => $email_address,
        ];

        // private key
        $priKey = openssl_pkey_new();
        $cert = openssl_csr_new($dn, $priKey);
        $cert = openssl_csr_sign($cert, null, $priKey, 365);

        $pem = [];

        openssl_x509_export($cert, $pem[0]);
        openssl_pkey_export($priKey, $pem[1], $pem_passphrase);

        $pem = implode($pem);

        return false !== file_put_contents($pem_file, $pem);
    }
}
