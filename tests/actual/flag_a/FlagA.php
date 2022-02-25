<?php
declare(strict_types=1);

class FlagA
{
    public string $foo = ''; // php-del line flag_a
    public string $bar = ''; //php-del line flag_a
    public string $baz = ''; /* php-del line flag_a */
    public string $qux = ''; /*php-del line flag_a*/
    public string $quux = ''; /** php-del line flag_a */
    public string $corge = ''; /**php-del line flag_a*/
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
        $h = 4;
        $i = 5; // php-del line flag_a
        if ($f && $h/** php-del start flag_a */ && $g/** php-del end flag_a */) {
            // something
        }
        $arr = [
            'a' => 'a',
            'b' => /** php-del start flag_a */ true ? 'x' : /** php-del ignore start */'b'/** php-del ignore end *//** php-del end flag_a */,
            'c' => 'c',
        ];
    }

    public function commentNextToComment()
    {
        $a = 1;
        // php-del start flag_a
        // NOTE startに続けてコメント
        $b = 2;
        // php-del ignore start
        // NOTE ignore startに続けてコメント
        $c = 3;
        // php-del ignore end
        // NOTE ignore endに続けてコメント
        // php-del end flag_a
        // NOTE endに続けてコメント
    }
}
