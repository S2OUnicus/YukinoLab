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
 * @package    Html        Html ヘルパー
 *
 * @copyright  Copyright (c) 2005 - 2023 KumbiaPHP Team
 * @license    https://github.com/KumbiaPHP/KumbiaPHP/blob/master/LICENSE   New BSD License
 */

use \Mockery as m;

/**
 * Html クラスのテスト
 *
 * @category Test
 * @package  Html
 *
 * @runTestsInSeparateProcesses  テストごとに別プロセスで実行
 */
class HtmlTest extends PHPUnit\Framework\TestCase
{
    //use \Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

    /**
     * 各テストの後処理
     */
    protected function tearDown(): void
    {
        /*
         * テストを別プロセスで実行する (@runTestsInSeparateProcesses) 場合、
         * tearDown で Mockery を明示的に閉じることが推奨されている。
         *
         * http://docs.mockery.io/en/latest/reference/phpunit_integration.html#phpunit-integration
         */
        m::close();
    }

    /**
     * Html::img() 用のデータプロバイダ
     *
     * @return array
     */
    public function imgDataProvider()
    {
        return array(
            array(
                'img' => 'img.jpg',
                'alt' => null,
                'attrs' => array('class="btn"', 'class="btn"'),
                'expected' => sprintf('<img src="%simg/img.jpg" alt="" class="btn"/>', PUBLIC_PATH),
            ),
            array(
                'img' => 'path/to/img2.png',
                'alt' => 'Image Name',
                'attrs' => array(array('class' => 'btn'), 'class="btn"'),
                'expected' => sprintf('<img src="%simg/path/to/img2.png" alt="Image Name" class="btn"/>', PUBLIC_PATH),
            ),
            array(
                'img' => 'path/to/img2.png',
                'alt' => 'Alt',
                'attrs' => array(array('class' => 'btn btn-primary', 'target' => '_blank'), 'class="btn" target="_blank"'),
                'expected' => sprintf('<img src="%simg/path/to/img2.png" alt="Alt" class="btn btn-primary" target="_blank"/>', PUBLIC_PATH),
            ),
        );
    }

    /**
     * @dataProvider imgDataProvider
     * Html::img() の出力が期待通りかを検証
     */
    public function testImg($img, $alt, $attrs, $expected)
    {
        //$tagMock = m::mock('alias:Tag');
        //$tagMock->shouldReceive('getAttrs')->withArgs(array($attrs[0]))->andReturn($attrs[1]);

        $this->assertSame($expected, Html::img($img, $alt, $attrs[0]));
    }

    /**
     * Html::img() の alt が省略されたときのデフォルト値を検証
     */
    public function testImgDefaultAlt()
    {
        //$tagMock = m::mock('alias:Tag');
        //$tagMock->shouldReceive('getAttrs')->withAnyArgs()->andReturn('');

        $expected = sprintf('<img src="%simg/img.png" alt="" />', PUBLIC_PATH);
        $this->assertSame($expected, Html::img('img.png'));
    }

    /**
     * Html::link() の基本的な動作を検証
     */
    public function testLink()
    {
        //$tagMock = m::mock('alias:Tag');
        //$tagMock->shouldReceive('getAttrs')->with(array('a' => 'b'))->andReturn('a="b"');
        //$tagMock->shouldReceive('getAttrs')->with(array('a' => 'b', 'c' => 'd'))->andReturn('a="b" c="d"');

        $expected = sprintf('<a href="%saction-name" >Action name</a>', PUBLIC_PATH);
        $this->assertSame($expected, Html::link('action-name', 'Action name'));

        $expected = sprintf('<a href="%saction-name" a="b">Action name</a>', PUBLIC_PATH);
        $this->assertSame($expected, Html::link('action-name', 'Action name', array('a' => 'b')));

        $expected = sprintf('<a href="%saction-name" a="b" c="d">Action name</a>', PUBLIC_PATH);
        $this->assertSame($expected, Html::link('action-name', 'Action name', array('a' => 'b', 'c' => 'd')));
    }

    /**
     * Html::link() 属性なし／文字列属性での動作を検証
     */
    public function testLinkWithoutAttrs()
    {
        //$tagMock = m::mock('alias:Tag');
        //$tagMock->shouldNotReceive('getAttrs');

        $expected = sprintf('<a href="%saction-name" >Action name</a>', PUBLIC_PATH);
        $this->assertSame($expected, Html::link('action-name', 'Action name'));

        $expected = sprintf('<a href="%saction-name" a="b">Action name</a>', PUBLIC_PATH);
        $this->assertSame($expected, Html::link('action-name', 'Action name', 'a="b"'));
    }

    /**
     * Html::link() 属性が配列で渡されたときの動作を検証
     */
    public function testLinkWithAttrsAsArray()
    {
        $expected = sprintf('<a href="%saction-name" >Action name</a>', PUBLIC_PATH);
        Html::link('action-name', 'Action name', array());

        $expected = sprintf('<a href="%saction-name" a="b">Action name</a>', PUBLIC_PATH);
        $this->assertSame($expected, Html::link('action-name', 'Action name', array('a' => 'b')));

        $expected = sprintf('<a href="%saction-name" a="b" c="d">Action name</a>', PUBLIC_PATH);
        $this->assertSame($expected, Html::link('action-name', 'Action name', array('a' => 'b', 'c' => 'd')));
    }

    /**
     * linkAction 用のデータプロバイダ
     *
     * @return array
     */
    public function linkActionDataProvider()
    {
        return array(
            array('action', 'controller', sprintf('href="%scontroller/action"', PUBLIC_PATH)),
            array('edit/3', 'user', sprintf('href="%suser/edit/3"', PUBLIC_PATH)),
            array('', 'test', sprintf('href="%stest/"', PUBLIC_PATH)),
            array(null, 'test', sprintf('href="%stest/"', PUBLIC_PATH)),
        );
    }

    /**
     * @dataProvider linkActionDataProvider
     * Html::linkAction() が正しい href パターンを生成するか検証
     */
    public function testLinkActionHrefPattern($action, $controllerPath, $expected)
    {
        $routerMock = m::mock('alias:Router');
        $routerMock->shouldReceive('get')->with('controller_path')->andReturn($controllerPath);

        //$tagMock = m::mock('alias:Tag');
        //$tagMock->shouldReceive('getAttrs')->withAnyArgs()->andReturn('');

        $link = Html::linkAction($action, 'Link Text');

        $this->assertStringContainsString($expected, $link);
    }

    /**
     * Html::linkAction() の完全なリンク文字列を検証
     */
    public function testLinkAction()
    {
        $routerMock = m::mock('alias:Router');
        $routerMock->shouldReceive('get')->with('controller_path')->andReturn('test');

        //$tagMock = m::mock('alias:Tag');
        //$tagMock->shouldReceive('getAttrs')->withAnyArgs()->andReturn('');

        $link = Html::linkAction('action-name', 'Link Text');

        $this->assertSame(
            '<a href="http://127.0.0.1/test/action-name" >Link Text</a>',
            $link
        );
    }
}
