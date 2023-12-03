<?php

namespace App\Helpers;

/**
 * Class DirHelper
 * @package common\helpers
 */
class DirHelper
{
    /**
     * @param $dir
     */
    public static function recursiveClearDir(string $dir) {

        $includes = glob($dir.'/*');

        foreach ($includes as $include) {
            if (is_dir($include)) {
                static::recursiveClearDir($include);
            } else {
                unlink($include);
            }
        }
    }
}
