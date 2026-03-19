<?php
/**
 * KumbiaPHP web & app Framework
 *
 * ライセンス
 *
 * このソースファイルは、同梱されている LICENSE ファイルに記載された
 * New BSD ライセンスの条件に従います。
 *
 * @category   Kumbia Tests
 * @package    Controller
 *
 * @copyright  Copyright (c) 2005 - 2023 KumbiaPHP Team (http://www.kumbiaphp.com)
 * @license    https://github.com/KumbiaPHP/KumbiaPHP/blob/master/LICENSE   New BSD License
 */

use PHPUnit\Framework\TestCase;

/**
 * PagesControllerTest クラス
 */
class PagesControllerTest extends TestCase
{
    use KumbiaTestTrait;

    /**
     * ページ表示のテスト
     *
     * @return void
     */
    public function testDisplayPage()
    {
        $actual = $this->get('/pages/kumbia/status');
        $this->assertStringContainsString('<h2>config.', $actual);
        //$test = $this->get('/pages/show/kumbia/status/');
        $this->assertResponseCode(200);
    }

    /**
     * 存在しないページを表示しようとした場合のテスト
     * expectedException KumbiaException
     */
    //public function testDisplayNoPage()
    //{
        //$this->expectException(KumbiaException::class);
        //$actual = $this->get('/pages/no_page/');
        //$this->assertResponseCode(404);
        //$this->assertContains('<h1>Vista "pages/no_page.phtml" no encontrada</h1>', $actual);
        //$this->assertResponseCode(404);
        //$this->expectException(KumbiaException::class);

    //}
}
