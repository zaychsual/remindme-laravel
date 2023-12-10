<?php

namespace App\Helpers;

use Exception;
use Illuminate\Support\Facades\DB;

/**
 * Trait ResponderHelper
 *
 * @package App\Helpers
 */
trait GlobalHelper
{
    /**
     * encrypt data
     *
     * @param string $string
     * @return string
     */
    public function encrypt($string): string
    {
        $cryptKey  = env('CRYPT_KEY');
        return base64_encode(openssl_encrypt($string, 'AES-256-CBC', $cryptKey, 0, str_pad(substr($cryptKey, 0, 16), 16, '0', STR_PAD_LEFT)));
    }

    /**
     * decrypt data
     *
     * @param string $string
     * @return string
     */
    public function decrypt($string): string
    {
        $cryptKey  = env('CRYPT_KEY');
        return openssl_decrypt(base64_decode($string), 'AES-256-CBC', $cryptKey, 0, str_pad(substr($cryptKey, 0, 16), 16, '0', STR_PAD_LEFT));
    }

    /**
     * convert time to seconds
     *
     * @param string $time
     * @return integer
     */
    public function timeToSeconds(string $time): int
    {
        $arr = explode(':', $time);
        if (count($arr) === 3) {
            return $arr[0] * 3600 + $arr[1] * 60 + $arr[2];
        }
        return $arr[0] * 60 + $arr[1];
    }

    /**
     * convert to romawi
     *
     * @param [type] $number
     * @return string
     */
    public function convertToRoman(int $number): string
    {
        $romans = [
            1000 => 'M',
            900 => 'CM',
            500 => 'D',
            400 => 'CD',
            100 => 'C',
            90 => 'XC',
            50 => 'L',
            40 => 'XL',
            10 => 'X',
            9 => 'IX',
            5 => 'V',
            4 => 'IV',
            1 => 'I',
        ];

        $result = '';

        foreach ($romans as $value => $roman) {
            while ($number >= $value) {
                $result .= $roman;
                $number -= $value;
            }
        }

        return $result;
    }

    /**
     * Delete only when there is no reference to other models.
     *
     * @param array $relations
     * @return response
     */
    public function secureDelete(array $tables, String $foreignKey, Int $foreignKeyValue, String $module, Bool $forceDelete = false)
    {
        $hasRelation = false;
        foreach ($tables as $table) {
            if (DB::table($table)->where($foreignKey, $foreignKeyValue)->count()) {
                $hasRelation = true;
            }
        }
        if ($forceDelete) {
            foreach ($tables as $table) {
                DB::table($table)->where($foreignKey, $foreignKeyValue)->delete();
            }
            $this->delete();
        } else {
            if ($hasRelation) {
                return throw new Exception("Gagal menghapus data {$module}, karena sudah ada transaksi yang sudah terhubung dengan data tersebut, jika tetap mau menghapus data tersebut silahkan hubung IT Support/Helpdesk");
            } else {
                return $this->delete();
            }
        }
    }
}
