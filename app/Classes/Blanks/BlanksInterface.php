<?php

namespace App\Classes\Blanks;

/**
 * Class BlanksInterface
 * @package App\Classes\Blanks;
 */
interface BlanksInterface
{
    /**
     * @return array
     */
    public function config(): array;
    public function footerConfig(): array;
    public function imgConfig(): array;
}
