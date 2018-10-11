<?php
namespace App\Utils;
use Illuminate\Support\Facades\Config;
class AES_Engine{
    private static $Encraption = true;
    public static function getEncrypt($input, $key) {
        $size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);

        $input = AES_Engine::pkcs5_pad($input, $size);
        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, '');
        $iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        mcrypt_generic_init($td, $key, $iv);
        $data = mcrypt_generic($td, $input);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $data = base64_encode($data);
        return $data;
    }
    private static function pkcs5_pad ($text, $blocksize) {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }

    public static function getDecrypt($sStr, $sKey) {
        $decrypted= mcrypt_decrypt(
            MCRYPT_RIJNDAEL_128,
            $sKey,
            base64_decode($sStr),
            MCRYPT_MODE_ECB
        );
        $dec_s = strlen($decrypted);
        $padding = ord($decrypted[$dec_s-1]);
        $decrypted = substr($decrypted, 0, -$padding);
        return $decrypted;
    }

    public static function encrypt($str)
    {
        return openssl_encrypt($str, 'aes-128-cbc', Config::get('app.encryption_key'), OPENSSL_RAW_DATA, self::pkcs5_pad('0',16));
    }

    public static function decrypt($str)
    {
        return openssl_decrypt($str, 'aes-128-cbc', Config::get('app.encryption_key'), OPENSSL_RAW_DATA, self::pkcs5_pad('0',16));
    }

    /*public static function pkcs5_pad ($text, $blocksize) {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }*/
}