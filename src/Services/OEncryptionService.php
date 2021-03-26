<?php

namespace Oapiconfig\Services;

use Oapiconfig\DI\ServiceInjector;

class OEncryptionService
{
    private const BYTE_SIZE = 5;
    private const MULTIPLICATION_FACTOR = 2;

    public function hyperlinkEncodedKey(): string
    {
        $keySeperatorSalt = ServiceInjector::oFileManager()->getConfigValue('hyperlink_security_salt', 'api');
        $apiKeySecurityOne = ServiceInjector::oFileManager()->getConfigValue('hyperlink_api_key_security_one', 'api');
        $apiKeySecurityTwo = ServiceInjector::oFileManager()->getConfigValue('hyperlink_api_key_security_two', 'api');
        $apiKey = ServiceInjector::oFileManager()->getConfigValue('api_key', 'api');
        $jwt = ServiceInjector::oJwtizer()->getOjwt();

        $secureKey = $apiKeySecurityOne . $apiKey . $apiKeySecurityTwo . $keySeperatorSalt . $jwt;

        $encodedSecureKey = base64_encode($secureKey);

        return $encodedSecureKey;
    }

    public function hyperlinkDecodedKey(string $encodedKey): array
    {
        $keySeperator = ServiceInjector::oFileManager()->getConfigValue('hyperlink_security_salt', 'api');
        $apiKeySecurityOne = ServiceInjector::oFileManager()->getConfigValue('hyperlink_api_key_security_one', 'api');
        $apiKeySecurityTwo = ServiceInjector::oFileManager()->getConfigValue('hyperlink_api_key_security_two', 'api');
        $length1 = (int)strlen($apiKeySecurityOne);
        $length2 = (int)strlen($apiKeySecurityTwo);
        $baseDecodedKey = base64_decode($encodedKey);
        list($salted_api_key, $jwt) = explode($keySeperator, $baseDecodedKey);
        $api_key = substr($salted_api_key, $length1, -$length2);

        return [$api_key, $jwt];
    }

    public function keyEncoder(string $valueToEncode): string
    {
        $encodedValue = base64_encode($valueToEncode);
        $keySecuritySalt = ServiceInjector::oFileManager()->getConfigValue('key_security_salt', 'api');
        $keySecurityOne = ServiceInjector::oFileManager()->getConfigValue('key_security_one', 'api');
        $keySecurityTwo = ServiceInjector::oFileManager()->getConfigValue('key_security_two', 'api');

        $randomByte1 = bin2hex(random_bytes($this::BYTE_SIZE));
        $randomByte2 = bin2hex(random_bytes($this::BYTE_SIZE));

        $secureKey = $keySecurityOne . $keySecuritySalt . $randomByte1 . $encodedValue . $randomByte2 . $keySecurityTwo;

        $encodedSecureKey = base64_encode($secureKey);

        return $encodedSecureKey;
    }

    public function keyDecoder(string $encodedKey): string
    {
        $keySecuritySalt = ServiceInjector::oFileManager()->getConfigValue('key_security_salt', 'api');
        $keySecurityOne = ServiceInjector::oFileManager()->getConfigValue('key_security_one', 'api');
        $keySecurityTwo = ServiceInjector::oFileManager()->getConfigValue('key_security_two', 'api');
        $baseDecodedKey = base64_decode($encodedKey);
        $length1 = (int)strlen($keySecurityOne);
        $length2 = (int)strlen($keySecurityTwo);

        $randomByteLength = (int)($this::BYTE_SIZE * $this::MULTIPLICATION_FACTOR);

        $salted_key_with_random_bytes = substr($baseDecodedKey, $length1, -$length2);
        $value_with_random_bytes = str_replace($keySecuritySalt, '', $salted_key_with_random_bytes);
        $encodedValue = substr($value_with_random_bytes, $randomByteLength, -$randomByteLength);

        $value = base64_decode($encodedValue);

        return $value;
    }

    public function randomKey(int $keyLength = 32): string
    {
        $byteSize = round($keyLength / 2);
        return bin2hex(random_bytes($byteSize));
    }

    public function randomOTP(int $otpLength = 6): string
    {
        $val = time();
        $otp = substr(str_shuffle($val), 0, $otpLength);
        return $otp;
    }
}
