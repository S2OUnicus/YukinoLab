<?php
/**
 * KumbiaPHP web & app Framework
 *
 * LICENSE
 *
 * このソースファイルは、同梱されている LICENSE ファイルに記載された
 * New BSD ライセンスの条件に従います。
 *
 * @category   Test        テスト
 * @package    Flash       フラッシュメッセージ
 *
 * @copyright  Copyright (c) 2005 - 2023 KumbiaPHP Team
 * @license    https://github.com/KumbiaPHP/KumbiaPHP/blob/master/LICENSE   New BSD License
 */

/**
 * Flash クラスのテスト
 *
 * @category    Test
 * @package     Flash
 */
class FlashTest extends PHPUnit\Framework\TestCase
{
    /**
     * 各テストの前に実行される初期化処理
     */
    public function setUp(): void
    {
        // サーバーソフトウェア名をテスト用に固定
        $_SERVER['SERVER_SOFTWARE'] = 'Test';
    }

    /**
     * 任意タイプ "test" を指定したときの出力を検証
     */
    public function testShowTypeTest()
    {
        $this->expectOutputString('<div class="test flash">Test Content</div>'.PHP_EOL);
        Flash::show('test', 'Test Content');
    }

    /**
     * タイプ "success" を指定したときの出力を検証
     */
    public function testShowTypeSuccess()
    {
        $this->expectOutputString('<div class="success flash">Test Content</div>'.PHP_EOL);
        Flash::show('success', 'Test Content');
    }

    /**
     * Flash::valid() の出力を検証
     */
    public function testValid()
    {
        $this->expectOutputString('<div class="valid flash">Test content for valid</div>'.PHP_EOL);
        Flash::valid('Test content for valid');
    }

    /**
     * Flash::error() の出力を検証
     */
    public function testError()
    {
        $this->expectOutputString('<div class="error flash">Test content for error</div>'.PHP_EOL);
        Flash::error('Test content for error');
    }

    /**
     * Flash::info() の出力を検証
     */
    public function testInfo()
    {
        $this->expectOutputString('<div class="info flash">Test content for info</div>'.PHP_EOL);
        Flash::info('Test content for info');
    }

    /**
     * Flash::warning() の出力を検証
     */
    public function testWarning()
    {
        $this->expectOutputString('<div class="warning flash">Test content for warning</div>'.PHP_EOL);
        Flash::warning('Test content for warning');
    }
}
