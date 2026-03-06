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
 * @package    Session     セッション
 *
 * @copyright  Copyright (c) 2005 - 2023 KumbiaPHP Team
 * @license    https://github.com/KumbiaPHP/KumbiaPHP/blob/master/LICENSE   New BSD License
 */

/**
 * Session クラスのテスト
 *
 * @category    Test
 * @package     Session
 */
class SessionTest extends PHPUnit\Framework\TestCase
{
    /**
     * 各テストの前にセッションが開始されていることを保証
     */
    public function setUp(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }
    }

    /**
     * 存在しないキーに対して has() が false を返すことを検証
     */
    public function testAssertKeyNotExists()
    {
        $this->assertFalse(Session::has('test_key'));
        $this->assertFalse(Session::has('test_key', 'other_namespace'));
    }

    /**
     * set() / get() の基本的な動作を検証
     */
    public function testAssertSetAndGet()
    {
        $this->assertFalse(Session::has('test_key'));

        Session::set('test_key', 'value');

        $this->assertTrue(Session::has('test_key'));
        $this->assertSame('value', Session::get('test_key'));
    }

    /**
     * 未設定キー取得時のデフォルト挙動（null）を検証
     */
    public function testGetDefaultValue()
    {
        Session::delete('test_key');

        $this->assertFalse(Session::has('test_key'));
        $this->assertNull(Session::get('test_key'));
    }

    /**
     * 名前空間付きで has() を利用したときの挙動を検証
     */
    public function testHasWithNamespaces()
    {
        $this->assertFalse(Session::has('test_key'));
        $this->assertFalse(Session::has('test_key', 'other'));

        Session::set('test_key', 'value');

        $this->assertTrue(Session::has('test_key'));
        $this->assertFalse(Session::has('test_key', 'other'));

        Session::delete('test_key');
        Session::set('test_key', 'other_value', 'other');

        $this->assertFalse(Session::has('test_key'));
        $this->assertTrue(Session::has('test_key', 'other'));
    }

    /**
     * 名前空間付きで get() を利用したときの挙動を検証
     */
    public function testGetWithNamespaces()
    {
        Session::delete('test_key');
        Session::delete('test_key', 'other');

        $this->assertNull(Session::get('test_key'));
        $this->assertNull(Session::get('test_key', 'other'));

        Session::set('test_key', 'value');
        Session::set('test_key', 'other_value', 'other');

        $this->assertSame('value', Session::get('test_key'));
        $this->assertSame('other_value', Session::get('test_key', 'other'));
    }

    /**
     * 名前空間なしの delete() の動作を検証
     */
    public function testDelete()
    {
        Session::set('test_key', 'value');

        $this->assertTrue(Session::has('test_key'));
        $this->assertSame('value', Session::get('test_key'));

        Session::delete('test_key');

        $this->assertFalse(Session::has('test_key'));
        $this->assertNull(Session::get('test_key'));
    }

    /**
     * 名前空間付き delete() の動作を検証
     */
    public function testDeleteWithNamespace()
    {
        Session::set('test_key', 'value');
        Session::set('test_key', 'other_value', 'other');
        Session::set('test_key', 'another_value', 'another');

        Session::delete('test_key');

        $this->assertFalse(Session::has('test_key'));
        $this->assertTrue(Session::has('test_key', 'other'));
        $this->assertTrue(Session::has('test_key', 'another'));

        Session::delete('test_key', 'other');

        $this->assertFalse(Session::has('test_key', 'other'));
        $this->assertTrue(Session::has('test_key', 'another'));
    }

    /**
     * 各テスト後にセッションをクリア
     */
    protected function tearDown(): void
    {
        if (session_status() != PHP_SESSION_NONE) {
            @session_unset();
            @session_destroy();
        }
    }
}
