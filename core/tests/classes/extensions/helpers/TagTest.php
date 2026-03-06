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
 * @package    Tag         Tag ヘルパー
 *
 * @copyright  Copyright (c) 2005 - 2023 KumbiaPHP Team
 * @license    https://github.com/KumbiaPHP/KumbiaPHP/blob/master/LICENSE   New BSD License
 */

/**
 * Tag クラスのテスト
 *
 * @category   Test
 * @package    Tag
 */
class TagTest extends PHPUnit\Framework\TestCase
{
    /**
     * Tag::js() 用のデータプロバイダ
     *
     * @return array
     */
    public function jsFileProvider()
    {
        return array(
            array('file'),
            array('path/to/file2'),
            array('path/to/file-3'),
        );
    }

    /**
     * @dataProvider jsFileProvider
     * Tag::js() が正しい <script> タグを生成するかを検証
     */
    public function testJs($file)
    {
        $scriptPattern = '<script type="text/javascript" src="%sjavascript/%s"></script>';
        $response = Tag::js($file);
        $expected = sprintf($scriptPattern, PUBLIC_PATH, $file.'.js');

        $this->assertSame($expected, $response);
    }

    /**
     * @dataProvider jsFileProvider
     * nocache パラメータ付きの Tag::js() を検証
     */
    public function testJsNoCache($file)
    {
        $scriptPattern = '<script type="text/javascript" src="%sjavascript/%s?nocache=';

        $response = Tag::js($file, false);
        $expected = sprintf($scriptPattern, PUBLIC_PATH, $file.'.js');

        // 先頭は nocache 付きの URL で始まっているか
        $this->assertStringStartsWith($expected, $response);
        // 閉じタグで終わっているか
        $this->assertStringEndsWith('"></script>', $response);
    }

    /**
     * 配列で属性を渡したときの Tag::getAttrs() を検証
     */
    public function testGetAttrsPassingArray()
    {
        $response = Tag::getAttrs(array(
            'attr-one' => 'value-one',
            'attr-two' => 'value-two',
        ));

        $expected = 'attr-one="value-one" attr-two="value-two"';
        $this->assertSame($expected, $response);
    }

    /**
     * 文字列で属性を渡したときの Tag::getAttrs() を検証
     */
    public function testGetAttrsPassingString()
    {
        $expected = 'attr-one="value-one" attr-two="value-two"';
        $response = Tag::getAttrs($expected);

        $this->assertSame($expected, $response);
    }

    /**
     * CSS ファイルの追加と取得処理を検証
     */
    public function testAddAndGetCssFiles()
    {
        $this->assertEmpty(Tag::getCss());

        Tag::css('css1');
        Tag::css('css2', 'print');
        Tag::css('css3');

        $files = Tag::getCss();
        $this->assertCount(3, $files);

        $this->assertInternalCssValue('css1', 'screen', $files[0]);
        $this->assertInternalCssValue('css2', 'print', $files[1]);
        $this->assertInternalCssValue('css3', 'screen', $files[2]);
    }

    /**
     * Tag::create() のテスト用データプロバイダ
     *
     * @return array
     */
    public function createTagDataProvider()
    {
        return array(
            array(
                'a',
                array('href' => PUBLIC_PATH, 'class' => 'btn'),
                null,
                sprintf('<a href="%s" class="btn"/>', PUBLIC_PATH)
            ),
            array(
                'input',
                array('type' => 'text', 'value' => 'Hola KumbiaPHP'),
                null,
                '<input type="text" value="Hola KumbiaPHP"/>'
            ),
            array(
                'input',
                'value="Hola KumbiaPHP" type="text"',
                null,
                '<input value="Hola KumbiaPHP" type="text"/>'
            ),
            array(
                'script',
                array('type' => 'text/javascript'),
                'console.log("Hola KumbiaPHP");',
                '<script type="text/javascript">console.log("Hola KumbiaPHP");</script>',
            ),
        );
    }

    /**
     * @dataProvider createTagDataProvider
     * Tag::create() の出力 HTML を検証
     */
    public function testCreateWithoutContent($tag, $attrs, $content, $expectedResult)
    {
        ob_start();
        Tag::create($tag, $content, $attrs);
        $html = ob_get_clean();

        $this->assertSame($expectedResult, $html);
    }

    /**
     * CSS 情報の配列が正しい値を持っているか検証するヘルパー
     *
     * @param string $file  期待される src
     * @param string $media 期待される media
     * @param array  $data  実際の CSS 情報配列
     */
    private function assertInternalCssValue($file, $media, $data)
    {
        $this->assertArrayHasKey('src', $data);
        $this->assertArrayHasKey('media', $data);
        $this->assertSame($file, $data['src']);
        $this->assertSame($media, $data['media']);
    }
}
