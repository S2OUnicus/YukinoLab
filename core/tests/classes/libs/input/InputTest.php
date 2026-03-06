<?php
/**
 * KumbiaPHP web & app Framework
 *
 * LICENSE
 *
 * このソースファイルは、同梱されている LICENSE.txt に記載された
 * New BSD ライセンスの条件に従います。
 * ライセンスの写しは以下の URL からも入手できます:
 * http://wiki.kumbiaphp.com/Licencia
 * 上記いずれからも取得できない場合は、license@kumbiaphp.com 宛に
 * メールでお問い合わせください。折り返しコピーをお送りします。
 *
 * @category   Kumbia      フレームワーク本体
 * @package    Session     セッション / 入力関連
 * @copyright  Copyright (c) 2005 - 2017 Kumbia Team
 * @license    http://wiki.kumbiaphp.com/Licencia     New BSD License
 */

/**
 * Input クラスのテスト
 *
 * @category Test
 */
#[\AllowDynamicProperties]
class InputTest extends PHPUnit\Framework\TestCase
{
    /**
     * 各テスト前にスーパーグローバルのバックアップ・初期化を行う
     */
    public function setUp(): void
    {
        $this->originalValues = [
            $_GET,
            $_POST,
            $_REQUEST,
            $_SERVER,
        ];

        $_GET = [];
        $_POST = [];
        $_REQUEST = [];
        $_SERVER = [];
    }

    /**
     * 各テスト後にスーパーグローバルを元の状態へ戻す
     */
    protected function tearDown(): void
    {
        [$_GET, $_POST, $_REQUEST, $_SERVER] = $this->originalValues;
    }

    /**
     * Input::is()（HTTP メソッド判定）用データプロバイダ
     *
     * @return array
     */
    public function isMethodProvider()
    {
        return [
            ['GET', 'GET', true],
            ['POST', 'POST', true],
            ['get', 'GET', false],
            ['get', 'POST', false],
            ['GET', 'POST', false],
            ['POST', 'GET', false],
            ['GET ', 'GET', false],
            [' GET ', 'GET', false],
            [' GET', 'GET', false],
            [' Get', 'GET', false],
        ];
    }

    /**
     * @dataProvider isMethodProvider
     * Input::is() が期待通りに HTTP メソッドを判定するか検証
     */
    public function testIsMethod($expectedMethod, $method, $canBeTrue)
    {
        $_SERVER['REQUEST_METHOD'] = $method;

        $result = Input::is($expectedMethod);

        if ($canBeTrue) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }

    /**
     * Ajax リクエストの場合に Input::isAjax() が true を返すことを検証
     */
    public function testIsAjaxMustBeTrue()
    {
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';

        $this->assertTrue(Input::isAjax());
    }

    /**
     * Ajax でない場合に Input::isAjax() が false を返すことを検証
     */
    public function testIsAjaxMustBeFalse()
    {
        $this->assertFalse(Input::isAjax());

        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'OtherValue';
        $this->assertFalse(Input::isAjax());
    }

    /**
     * 特定インデックスを指定して Input::delete() したときの挙動を検証
     */
    public function testDeleteIndex()
    {
        $_POST['__index__'] = '__value__';

        $this->assertSame('__value__', $_POST['__index__']);

        Input::delete('__index__');

        $this->assertSame([], $_POST['__index__']);
    }

    /**
     * インデックス未指定で Input::delete() したときに POST 全体がクリアされることを検証
     */
    public function testDeleteWithoutIndex()
    {
        $_POST['__index__'] = '__value__';

        $this->assertSame('__value__', $_POST['__index__']);

        Input::delete();

        $this->assertSame([], $_POST);
    }

    /**
     * IP アドレス取得：HTTP_CLIENT_IP から取得できることを検証
     */
    public function testIpFromClientIp()
    {
        $_SERVER['HTTP_CLIENT_IP'] = '__test_ip__';

        $this->assertSame('__test_ip__', Input::ip());
    }

    /**
     * IP アドレス取得：HTTP_X_FORWARDED_FOR から取得できることを検証
     */
    public function testIpFromXForwaredFor()
    {
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '__test_ip__';

        $this->assertSame('__test_ip__', Input::ip());
    }

    /**
     * IP アドレス取得：REMOTE_ADDR から取得できることを検証
     */
    public function testIpFromRemoteAddr()
    {
        $_SERVER['REMOTE_ADDR'] = '__test_ip__';

        $this->assertSame('__test_ip__', Input::ip());
    }

    /**
     * GET / POST / REQUEST 用の共通データプロバイダ
     *
     * @return array
     */
    public function getRequestTestingData()
    {
        return [
            [&$_POST, 'post'],
            [&$_GET, 'get'],
            [&$_REQUEST, 'request'],
        ];
    }

    /**
     * @dataProvider getRequestTestingData
     * 単一インデックス指定での Input::post()/get()/request() の挙動を検証
     */
    public function testRequestSimpleIndex(&$GLOBAl, $method)
    {
        $hasMethod = 'has'.ucfirst($method);

        $this->assertFalse(Input::$hasMethod('__post_index__'));

        $GLOBAl['__post_index__'] = 'value';

        $this->assertSame('value', Input::$method('__post_index__'));
    }

    /**
     * @dataProvider getRequestTestingData
     * インデックス未指定での Input::post()/get()/request() の挙動を検証
     */
    public function testRequestWithoutIndex(&$GLOBAl, $method)
    {
        $this->assertEmpty(Input::$method());

        $GLOBAl['__post_index__'] = 'value';

        $this->assertSame(['__post_index__' => 'value'], Input::$method());
    }

    /**
     * @dataProvider getRequestTestingData
     * ドット記法によるネスト配列アクセスの挙動を検証
     */
    public function testRequestNestedIndex(&$GLOBAL, $method)
    {
        $this->assertSame('', Input::post('index1.index2.index4'));
        $this->assertSame('', Input::post('index1.index3'));

        $_POST['index1'] = [
            'index2' => [
                'index4' => 'value4',
            ],
            'index3' => 'value3',
        ];

        $this->assertSame('value4', Input::post('index1.index2.index4'));
        $this->assertSame('value3', Input::post('index1.index3'));

        $this->assertSame('', Input::post('index1.index3.index4'));
        $this->assertSame('', Input::post('index1.index5'));
        $this->assertSame('', Input::post('index61'));
    }
}
