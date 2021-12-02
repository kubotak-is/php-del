<?php
declare(strict_types=1);

class Test2
{
    public function hoge()
    {
        /** php-del start flag_1 */
        $a = 2;
        /** php-del end flag_1 */
    }

    public function fuga()
    {
        /** php-del start flag_2 */
        $b = 2;
        /** php-del end flag_2 */
    }

    public function piyo()
    {
        /** php-del start flag_2 */
        $c = 2;
        /** php-del end flag_2 */
    }
}
