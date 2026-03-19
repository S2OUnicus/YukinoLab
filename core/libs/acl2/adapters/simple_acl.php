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
 * PHP によるルール定義を行う ACL 実装
 *
 * @category   Kumbia
 * @package    Acl
 */
class SimpleAcl extends Acl2
{

    /**
     * ロールの定義
     * それぞれのロールに対して「親ロール」と「アクセス可能なリソース」を紐付ける
     *
     * @var array
     *
     * @example SimpleAcl-roles
     *   protected $_roles = array(
     *       'rol1' => array(
     *           'resources' => array('recurso1', 'recurso2')
     *       ),
     *       'rol2' => array(
     *           'resources' => array('recurso2'),
     *           'parents' => array('rol1')
     *       )
     *   );
     */
    protected $_roles = array();

    /**
     * システムユーザーと、それぞれに割り当てられているロール
     *
     * @var array
     *
     * @example SimpleAcl-users
     * protected $_users = array(
     *     'usuario1' => array('rol1', 'rol2'),
     *     'usuario2' => array('rol3')
     * );
     */
    protected $_users = array();

    /**
     * 指定したロールに、アクセス可能なリソースを設定する
     *
     * @param string $role      ロール名
     * @param array  $resources ロールがアクセスできるリソースの配列
     */
    public function allow($role, $resources)
    {
        $this->_roles[$role]['resources'] = $resources;
    }

    /**
     * 指定したロールの「親ロール」を設定する
     *
     * @param string $role    ロール名
     * @param array  $parents 親ロール名の配列
     */
    public function parents($role, $parents)
    {
        $this->_roles[$role]['parents'] = $parents;
    }

    /**
     * ユーザーと、そのユーザーに紐付くロールを登録する
     *
     * @param string $user  ユーザー名
     * @param array  $roles 付与するロールの配列
     */
    public function user($user, $roles)
    {
        $this->_users[$user] = $roles;
    }

    /**
     * アクセス検証対象ユーザーに紐付くロールを取得する
     *
     * @param  string $user アクセス検証を行うユーザー
     * @return array  そのユーザーに割り当てられているロール一覧
     */
    protected function _getUserRoles($user)
    {
        if (isset($this->_users[$user])) {
            return $this->_users[$user];
        }

        return array();
    }

    /**
     * 指定したロールがアクセスできるリソース一覧を取得する
     *
     * @param  string $role ロール名
     * @return array  ロールがアクセス可能なリソースの配列
     */
    protected function _getRoleResources($role)
    {
        if (isset($this->_roles[$role]['resources'])) {
            return $this->_roles[$role]['resources'];
        }

        return array();
    }

    /**
     * 指定したロールの親ロール一覧を取得する
     *
     * @param  string $role ロール名
     * @return array  親ロール名の配列
     */
    protected function _getRoleParents($role)
    {
        if (isset($this->_roles[$role]['parents'])) {
            return $this->_roles[$role]['parents'];
        }

        return array();
    }

}
