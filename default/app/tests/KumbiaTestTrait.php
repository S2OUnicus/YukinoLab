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
 * @package    Core
 *
 * © 2005 - 2023 KumbiaPHP Team (http://www.kumbiaphp.com)
 * @license    https://github.com/KumbiaPHP/KumbiaPHP/blob/master/LICENSE   New BSD License
 */

trait KumbiaTestTrait
{
    /**
     * HTTP レスポンスコードを検証する
     *
     * @param int $code 期待するステータスコード
     */
    public function assertResponseCode($code)
    {
        $actual = http_response_code();
        $this->assertSame(
            $code,
            $actual,
            "ステータスコードが {$code} ではなく {$actual} でした。"
        );
    }

    /**
     * コントローラへリクエストを送信する
     *
     * @param string $method HTTP メソッド
     * @param string $url    controller/method/arg | URI
     * @param array  $params POST パラメータ／クエリストリング
     */
    protected function request($method, $url, $params = [])
    {
        $_SERVER['REQUEST_METHOD'] = $method;

        ob_start();
        $start_ob_level = ob_get_level();
        ob_start();
        View::render(Router::execute($url));
        while (ob_get_level() > $start_ob_level) {
            ob_end_flush();
        }

        //$content = $this->getActualOutput();
        return ob_get_clean();
    }

    /**
     * コントローラへの GET リクエスト
     *
     * @param string $url    controller/method/arg | URI
     * @param array  $params クエリストリング
     */
    public function get($url, $params = [])
    {
        return $this->request('GET', $url, $params);
    }
}
