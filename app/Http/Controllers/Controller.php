<?php namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Genereate GUID
     * @param  boolean $prefix
     * @param  boolean $braces
     * @return GUID string
     */
    public function generateGUID($prefix = false, $braces = false)
    {
        mt_srand((double) microtime() * 10000);
        $charid = strtoupper(md5(uniqid($prefix === false ? rand() : $prefix, true)));
        $hyphen = chr(45); // "-"
        $uuid = substr($charid, 0, 8) . $hyphen
        . substr($charid, 8, 4) . $hyphen
        . substr($charid, 12, 4) . $hyphen
        . substr($charid, 16, 4) . $hyphen
        . substr($charid, 20, 12);

        // Add brackets or not? "{" ... "}"
        return $braces ? chr(123) . $uuid . chr(125) : $uuid;
    }
}
