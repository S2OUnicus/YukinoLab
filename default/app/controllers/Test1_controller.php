<?php

/**
 * テストコントローラー
 *
 */
class Test1Controller extends AppController
{
    public function index()
    {

    }

    public function func2($pass1, $pass2)
    {
        $this->pass1 = $pass1;
        $this->pass2 = $pass2;
    }
}
