<?php
declare(strict_types=1);

class FlagA
{
    public function hoge()
    {
        /** php-del start flag_a */
        $a = 1;
        /** php-del end flag_a */
    }

    public function fuga()
    {
        /** php-del start flag_a */
        $b = 1;
        /** php-del ignore start */
        $d = 2;
        /** php-del ignore end */
        /** php-del ignore start */
        $c = 3;
        /** php-del ignore end */
        /** php-del end flag_a */
    }

    public function piyo()
    {
        /**
         * php-del  start  flag_a
         */
        $e = 1;
        // php-del  ignore start
        $f = 2;
        /**
         * php-del ignore  end
         */
        $g = 3;
        // php-del end flag_a
    }
}
