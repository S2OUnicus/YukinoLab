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
 * @package    Core        コアユーティリティ
 *
 * @copyright  Copyright (c) 2005 - 2023 KumbiaPHP Team
 * @license    https://github.com/KumbiaPHP/KumbiaPHP/blob/master/LICENSE   New BSD License
 */

/**
 * Util クラスのテスト
 *
 * @category    Test
 * @package     Core
 *
 * @runTestsInSeparateProcesses  テストを個別プロセスで実行
 */
class UtilTest extends PHPUnit\Framework\TestCase
{
    /**
     * Util::underscore() 用のデータプロバイダ
     *
     * @return array
     */
    public function underescoreDataProvider()
    {
        return array(
            array('Hello World', 'Hello_World'),
            array('', ''),
            array('-_ae123$%&', '-_ae123$%&'),
            array(' ', '_'),
            array('  ', '__'),
            array('---', '---'),
            array(
                'If you did not receive a copy of the license and are unable to',
                'If_you_did_not_receive_a_copy_of_the_license_and_are_unable_to',
            ),
        );
    }

    /**
     * Util::dash() 用のデータプロバイダ
     *
     * @return array
     */
    public function dashDataProvider()
    {
        return array(
            array('Hello World', 'Hello-World'),
            array('', ''),
            array('-_ae123$%&', '-_ae123$%&'),
            array(' ', '-'),
            array('  ', '--'),
            array('---', '---'),
            array('___', '___'),
            array(
                'If you did not receive a copy of the license and are unable to',
                'If-you-did-not-receive-a-copy-of-the-license-and-are-unable-to',
            ),
        );
    }

    /**
     * Util::humanize() 用のデータプロバイダ
     *
     * @return array
     */
    public function humanizeDataProvider()
    {
        return array(
            array('Hello-World', 'Hello World'),
            array('Hello_World', 'Hello World'),
            array('', ''),
            array('-_ae123$%&', '  ae123$%&'),
            array(' ', ' '),
            array('  ', '  '),
            array('---', '   '),
            array('___', '   '),
            array('-__---___', '         '),
            array(
                'If you-did_not receive a-copy of the-license_and_are unable to',
                'If you did not receive a copy of the license and are unable to',
            ),
        );
    }

    /**
     * Util::encomillar() 用のデータプロバイダ
     *
     * @return array
     */
    public function encomillarDataProvider()
    {
        return array(
            array('a,b,c', '"a","b","c"'),
            array('a, b, c', '"a"," b"," c"'),
            array(' a , b , c ', '" a "," b "," c "'),
            array('hello , world,123', '"hello "," world","123"'),
        );
    }

    /**
     * Util::camelcase() 用のデータプロバイダ
     *
     * @return array
     */
    public function camelcaseDataProvider()
    {
        return array(
            array('a_b_c', 'ABC', 'aBC'),
            array('users', 'Users', 'users'),
            array('table_name', 'TableName', 'tableName'),
            array('table__name', 'TableName', 'tableName'),
            array('table___name', 'TableName', 'tableName'),
            array('table_name1', 'TableName1', 'tableName1'),
            array('table_name_1', 'TableName1', 'tableName1'),
            array('table_1_name', 'Table1Name', 'table1Name'),
            array('table_1name', 'Table1name', 'table1name'),
            array('table1_name', 'Table1Name', 'table1Name'),
            array('table1_2name', 'Table12name', 'table12name'),
            array('table_1_2_name', 'Table12Name', 'table12Name'),
            array('table_12_name', 'Table12Name', 'table12Name'),
            array('table12_name', 'Table12Name', 'table12Name'),
            array('table12name', 'Table12name', 'table12name'),
        );
    }

    /**
     * Util::smallcase() 用のデータプロバイダ
     *
     * @return array
     */
    public function smallcaseDataProvider()
    {
        return array(
            array('ABC', 'a_b_c'),
            array('Users', 'users'),
            array('TableName', 'table_name'),
            array('TableName1', 'table_name1'),
            array('Table1Name', 'table1_name'),
            array('Table12name', 'table12name'),
            array('Table12Name', 'table12_name'),
        );
    }

    /**
     * Util::getParams() 用のデータプロバイダ
     *
     * @return array
     */
    public function getParamsDataProvider()
    {
        return array(
            array(array(), array()),
            array(array('a: b'), array('a' => 'b')),
            array(array('a: b', 'c: d'), array('a' => 'b', 'c' => 'd')),
            array(
                array('param1: value1', 'param2:  value2'),
                array('param1' => 'value1', 'param2' => ' value2')
            ),
            array(
                array('param1 : value1', 'param2 :  value2'),
                array('param1 ' => 'value1', 'param2 ' => ' value2')
            ),
            array(
                array('value1', 'value2'),
                array('value1', 'value2'),
            ),
        );
    }

    /**
     * @dataProvider underescoreDataProvider
     * Util::underscore() の変換結果を検証
     */
    public function testUnderescore($original, $expected)
    {
        $result = Util::underscore($original);

        $this->assertSame($expected, $result);
    }

    /**
     * @dataProvider dashDataProvider
     * Util::dash() の変換結果を検証
     */
    public function testDash($original, $expected)
    {
        $result = Util::dash($original);

        $this->assertSame($expected, $result);
    }

    /**
     * @dataProvider humanizeDataProvider
     * Util::humanize() の変換結果を検証
     */
    public function testHumanize($original, $expected)
    {
        $result = Util::humanize($original);

        $this->assertSame($expected, $result);
    }

    /**
     * @dataProvider encomillarDataProvider
     * Util::encomillar() の変換結果を検証
     */
    public function testEncomillar($original, $expected)
    {
        $result = Util::encomillar($original);

        $this->assertSame($expected, $result);
    }

    /**
     * @dataProvider camelcaseDataProvider
     * Util::camelcase() の CamelCase / camelCase 変換を検証
     */
    public function testCamelcase($original, $expected, $expectedLowerCase)
    {
        $result = Util::camelcase($original);
        $resultLowerCase = Util::camelcase($original, true);

        $this->assertSame($expected, $result);
        $this->assertSame($expectedLowerCase, $resultLowerCase);
    }

    /**
     * @dataProvider smallcaseDataProvider
     * Util::smallcase() の変換結果を検証
     */
    public function testSmallcase($original, $expected)
    {
        $result = Util::smallcase($original);

        $this->assertSame($expected, $result);
    }

    /**
     * @dataProvider getParamsDataProvider
     * Util::getParams() の結果を検証
     */
    public function testGetParams($original, $expected)
    {
        $result = Util::getParams($original);

        $this->assertEquals($expected, $result);
    }
}
