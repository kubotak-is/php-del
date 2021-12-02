<?php
declare(strict_types=1);

class Test1
{
    public function hoge()
    {
        /** php-del start flag_1 */
        $a = 1;
        /** php-del end flag_1 */
    }

    public function fuga()
    {
        /** php-del start flag_1 */
        $b = 1;
        /** php-del ignore start */
        $d = 2;
        /** php-del ignore end */
        /** php-del ignore start */
        $c = 3;
        /** php-del ignore end */
        /** php-del end flag_1 */
    }
}
