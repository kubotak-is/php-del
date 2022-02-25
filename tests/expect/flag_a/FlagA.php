<?php
declare(strict_types=1);

class FlagA
{
    public function hoge()
    {
    }

    public function fuga()
    {
        $d = 2;
        $c = 3;
    }

    public function piyo()
    {
        $f = 2;
        $h = 4;
        if ($f && $h) {
            // something
        }
        $arr = [
            'a' => 'a',
            'b' => 'b',
            'c' => 'c',
        ];
    }

    public function commentNextToComment()
    {
        $a = 1;
        // NOTE ignore startに続けてコメント
        $c = 3;
        // NOTE endに続けてコメント
    }
}
