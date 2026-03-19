<?php
/**
 * KumbiaPHP web & app Framework
 *
 * LICENSE
 *
 * このソースファイルは、同梱されている LICENSE ファイルに記載された
 * New BSD ライセンスの条件に従います。
 *
 * @category   Kumbia
 * @package    Acl    アクセス制御 (ACL)
 *
 * @copyright  Copyright (c) 2005 - 2023 KumbiaPHP Team
 * @license    https://github.com/KumbiaPHP/KumbiaPHP/blob/master/LICENSE   New BSD License
 */

/**
 * ACL 管理用ベースクラス
 *
 * ACL（Access Control List）の権限管理を行うための
 * 新しいベースクラス
 *
 * @category   Kumbia
 * @package    Acl
 */
abstract class Acl2
{

    /**
     * デフォルトのアダプタ名
     *
     * @var string
     */
    protected static $_defaultAdapter = 'simple';

    /**
     * ユーザーが指定リソースへアクセスできるかを検証する
     *
     * @param string $resource アクセスを検証するリソース
     * @param string $user     ACL 上のユーザー
     * @return boolean         アクセス可能なら TRUE、そうでなければ FALSE
     */
    public function check($resource, $user)
    {
        // ユーザーに紐付くすべてのロールを走査
        foreach ($this->_getUserRoles($user) as $role) {
            if ($this->_checkRole($role, $resource)) {
                return TRUE;
            }
        }

        // どのロールでも許可されていなければ、デフォルトで拒否
        return FALSE;
    }

    /**
     * 指定ロールがリソースへアクセスできるかを検証する
     *
     * @param string $role     ロール名
     * @param string $resource リソース名
     * @return boolean
     */
    private function _checkRole($role, $resource)
    {
        // ロール自身がリソースへアクセス可能かを確認
        if (in_array($resource, $this->_getRoleResources($role))) {
            return TRUE;
        }

        // 親ロールからの継承によってアクセス可能かを確認
        foreach ($this->_getRoleParents($role) as $parent) {
            if ($this->_checkRole($parent, $resource)) {
                return TRUE;
            }
        }

        return FALSE;
    }

    /**
     * アクセス検証対象ユーザーに紐付くロールを取得する
     *
     * @param  string $user アクセスを検証するユーザー
     * @return array  ユーザーに割り当てられているロール一覧
     */
    abstract protected function _getUserRoles($user);

    /**
     * 指定ロールがアクセス可能なリソース一覧を取得する
     *
     * @param  string $role ロール名
     * @return array  ロールがアクセスできるリソース一覧
     */
    abstract protected function _getRoleResources($role);

    /**
     * 指定ロールの親ロール一覧を取得する
     *
     * @param  string $role ロール名
     * @return array  親ロールの一覧
     */
    abstract protected function _getRoleParents($role);

    /**
     * ACL 用アダプタのインスタンスを生成して返す
     *
     * @param  string $adapter アダプタ名（simple, model, xml, ini など）
     * @return object          アダプタクラスのインスタンス
     */
    public static function factory($adapter = '')
    {
        if (!$adapter) {
            $adapter = self::$_defaultAdapter;
        }

        require_once __DIR__ . "/adapters/{$adapter}_acl.php";
        $class = $adapter . 'acl';

        return new $class;
    }

    /**
     * デフォルトで使用するアダプタを変更する
     *
     * @param string $adapter デフォルトアダプタ名
     */
    public static function setDefault($adapter)
    {
        self::$_defaultAdapter = $adapter;
    }

}
