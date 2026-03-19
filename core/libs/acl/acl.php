<?php
/**
 * KumbiaPHP web & app Framework
 *
 * LICENSE
 *
 * このソースファイルは、同梱されている LICENSE ファイルに記載された
 * New BSD ライセンスの条件に従います。
 *
 * @category   Kumbia        フレームワーク本体
 * @package    Acl           アクセス制御 (ACL)
 *
 * @copyright  Copyright (c) 2005 - 2023 KumbiaPHP Team
 * @license    https://github.com/KumbiaPHP/KumbiaPHP/blob/master/LICENSE   New BSD License
 */
/**
 * @see AclRole
 */
include __DIR__ .'/role/role.php';

/**
 * @see AclResource
 */
include __DIR__ .'/resource/resource.php';

/**
 * ACL（Access Control List / アクセス制御リスト）
 *
 * ACL（Access Control List）は、権限分離を実現するために使われる
 * セキュリティの概念であり、どのオブジェクトに対してどのような
 * アクセス権限を与えるかを定義します。
 *
 * 各 ACL には、ロール（Roles）、リソース（Resources）、および
 * それらに対するアクセス権（Actions）が含まれます。
 *
 * $roles          = AclRole オブジェクトの一覧（ロールのリスト）
 * $resources      = AclResource オブジェクトの一覧（制御対象リソース）
 * $access         = アクセス権限の一覧
 * $role_inherits  = 他ロールから継承しているロールの一覧
 * $resource_names = リソース名の一覧
 * $roles_names    = ロール名の一覧
 *
 * @category   Kumbia
 * @package    Acl
 * @deprecated 1.0 以降は ACL2 の使用を推奨
 */
class Acl {

    /**
     * ACL に登録されているロール名の一覧
     *
     * @var array
     */
    private $roles_names = array();

    /**
     * ACL に登録されているロールオブジェクトの一覧
     *
     * @var array
     */
    private $roles = array();

    /**
     * ACL に登録されているリソースオブジェクトの一覧
     *
     * @var array
     */
    private $resources = array();

    /**
     * アクセス権限テーブル
     *
     * @var array
     */
    public $access = array();

    /**
     * ロール同士の継承関係
     *
     * @var array
     */
    private $role_inherits = array();

    /**
     * リソース名の一覧
     *
     * @var array
     */
    private $resources_names = array('*');

    /**
     * リソースごとのアクセス種別リスト
     *
     * @var array
     */
    private $access_list = array('*' => array('*'));

    /**
     * ロールを ACL に追加する
     *
     * $roleObject      = 追加する AclRole オブジェクト
     * $access_inherits = 権限を継承するロール名、またはその配列
     *
     * 例:
     * <code>$acl->add_role(new Acl_Role('administrador'), 'consultor');</code>
     *
     * @param AclRole    $roleObject
     * @param string|array $access_inherits 継承元ロール
     * @return false|null 既に存在する場合は false
     */
    public function add_role(AclRole $roleObject, $access_inherits = '') {
        if (in_array($roleObject->name, $this->roles_names)) {
            return false;
        }
        $this->roles[]                             = $roleObject;
        $this->roles_names[]                       = $roleObject->name;
        // すべてのリソース・すべてのアクセスに対して許可（デフォルト）
        $this->access[$roleObject->name]['*']['*'] = 'A';
        if ($access_inherits) {
            $this->add_inherit($roleObject->name, $access_inherits);
        }
    }

    /**
     * ロールに、別のロールからアクセス権を継承させる
     *
     * @param string       $role           継承先ロール名
     * @param string|array $role_to_inherit 継承元ロール名、または配列
     * @return bool|void
     */
    public function add_inherit($role, $role_to_inherit) {
        if (!in_array($role, $this->roles_names)) {
            return false;
        }
        if ($role_to_inherit != '') {
            if (is_array($role_to_inherit)) {
                foreach ($role_to_inherit as $rol_in) {
                    if ($rol_in == $role) {
                        return false;
                    }
                    if (!in_array($rol_in, $this->roles_names)) {
                        throw new KumbiaException("ロール '{$rol_in}' は ACL リスト内に存在しません");

                    }
                    $this->role_inherits[$role][] = $rol_in;
                }
                $this->rebuild_access_list();
            } else {
                if ($role_to_inherit == $role) {
                    return false;
                }
                if (!in_array($role_to_inherit, $this->roles_names)) {
                    throw new KumbiaException("ロール '{$role_to_inherit}' は ACL リスト内に存在しません");

                }
                $this->role_inherits[$role][] = $role_to_inherit;
                $this->rebuild_access_list();
            }
        } else {
            throw new KumbiaException("Acl::add_inherit では継承元ロールを指定する必要があります");

        }
    }

    /**
     * 指定したロールが ACL 内に存在するかどうかを確認する
     *
     * @param string $role_name
     * @return boolean
     */
    public function is_role($role_name) {
        return in_array($role_name, $this->roles_names);
    }

    /**
     * 指定したリソースが ACL 内に存在するかどうかを確認する
     *
     * @param string $resource_name
     * @return boolean
     */
    public function is_resource($resource_name) {
        return in_array($resource_name, $this->resources_names);
    }

    /**
     * リソースを ACL に追加する
     *
     * resource_name は、たとえば
     * consulta（閲覧）、buscar（検索）、insertar（追加）、valida（検証）
     * などの具体的な操作を表す名前、あるいはそのリストを受け取ります。
     *
     * 例:
     * <code>
     * // 単一のリソースにアクセスを追加
     * $acl->add_resource(new AclResource('clientes'), 'consulta');
     *
     * // 複数のアクセス種別を追加
     * $acl->add_resource(new AclResource('clientes'), 'consulta', 'buscar', 'insertar');
     * </code>
     *
     * @param AclResource $resource
     * @return boolean|null
     */
    public function add_resource(AclResource $resource) {
        if (!in_array($resource->name, $this->resources)) {
            $this->resources[]                  = $resource;
            $this->access_list[$resource->name] = array();
            $this->resources_names[]            = $resource->name;
        }
        if (func_num_args() > 1) {
            $access_list = func_get_args();
            unset($access_list[0]);
            $this->add_resource_access($resource->name, $access_list);
        }
    }

    /**
     * リソースにアクセス種別を追加する
     *
     * @param string       $resource    リソース名
     * @param string|array $access_list アクセス種別（文字列または配列）
     */
    public function add_resource_access($resource, $access_list) {
        if (is_array($access_list)) {
            foreach ($access_list as $access_name) {
                if (!in_array($access_name, $this->access_list[$resource])) {
                    $this->access_list[$resource][] = $access_name;
                }
            }
        } else {
            if (!in_array($access_list, $this->access_list[$resource])) {
                $this->access_list[$resource][] = $access_list;
            }
        }
    }

    /**
     * リソースからアクセス種別を削除する
     *
     * @param string       $resource
     * @param string|array $access_list
     */
    public function drop_resource_access($resource, $access_list) {
        if (is_array($access_list)) {
            foreach ($access_list as $access_name) {
                if (in_array($access_name, $this->access_list[$resource])) {
                    foreach ($this->access_list[$resource] as $i => $access) {
                        if ($access == $access_name) {
                            unset($this->access_list[$resource][$i]);
                        }
                    }
                }
            }
        } else {
            if (in_array($access_list, $this->access_list[$resource])) {
                foreach ($this->access_list[$resource] as $i => $access) {
                    if ($access == $access_list) {
                        unset($this->access_list[$resource][$i]);
                    }
                }
            }
        }
        $this->rebuild_access_list();
    }

    /**
     * 特定ロールに対し、リソースへのアクセスを許可する
     *
     * ワイルドカードとして '*' を使用可能。
     *
     * 例:
     * <code>
     * // invitados ロールに、clientes リソースの consulta アクセスを許可
     * $acl->allow('invitados', 'clientes', 'consulta');
     *
     * // invitados ロールに、clientes リソースの consulta / insertar アクセスを許可
     * $acl->allow('invitados', 'clientes', array('consulta', 'insertar'));
     *
     * // すべてのロールに、productos リソースの visualiza アクセスを許可
     * $acl->allow('*', 'productos', 'visualiza');
     *
     * // すべてのロールに、すべてのリソースで visualiza アクセスを許可
     * $acl->allow('*', '*', 'visualiza');
     * </code>
     *
     * @param string       $role
     * @param string       $resource
     * @param string|array $access
     */
    public function allow($role, $resource, $access) {
        if (!in_array($role, $this->roles_names)) {
            throw new KumbiaException("ロール '$role' は ACL リスト内に存在しません");

        }
        if (!in_array($resource, $this->resources_names)) {
            throw new KumbiaException("リソース '$resource' は ACL リスト内に存在しません");

        }
        if (is_array($access)) {
            foreach ($access as $acc) {
                if (!in_array($acc, $this->access_list[$resource])) {
                    throw new KumbiaException("アクセス '$acc' はリソース '$resource' の ACL リスト内に存在しません");

                }
            }
            foreach ($access as $acc) {
                $this->access[$role][$resource][$acc] = 'A';
            }
        } else {
            if (!in_array($access, $this->access_list[$resource])) {
                throw new KumbiaException("アクセス '$access' はリソース '$resource' の ACL リスト内に存在しません");

            }
            $this->access[$role][$resource][$access] = 'A';
            $this->rebuild_access_list();
        }
    }

    /**
     * 特定ロールに対し、リソースへのアクセスを拒否する
     *
     * ワイルドカードとして '*' を使用可能。
     *
     * 例:
     * <code>
     * // invitados ロールの clientes リソースに対する consulta アクセスを拒否
     * $acl->deny('invitados', 'clientes', 'consulta');
     *
     * // invitados ロールの clientes リソースに対する consulta / insertar を拒否
     * $acl->deny('invitados', 'clientes', array('consulta', 'insertar'));
     *
     * // すべてのロールで productos リソースの visualiza アクセスを拒否
     * $acl->deny('*', 'productos', 'visualiza');
     *
     * // すべてのロールで、すべてのリソースの visualiza アクセスを拒否
     * $acl->deny('*', '*', 'visualiza');
     * </code>
     *
     * @param string       $role
     * @param string       $resource
     * @param string|array $access
     */
    public function deny($role, $resource, $access) {
        if (!in_array($role, $this->roles_names)) {
            throw new KumbiaException("ロール '$role' は ACL リスト内に存在しません");

        }
        if (!in_array($resource, $this->resources_names)) {
            throw new KumbiaException("リソース '$resource' は ACL リスト内に存在しません");

        }
        if (is_array($access)) {
            foreach ($access as $acc) {
                if (!in_array($acc, $this->access_list[$resource])) {
                    throw new KumbiaException("アクセス '$acc' はリソース '$resource' の ACL リスト内に存在しません");

                }
            }
            foreach ($access as $acc) {
                $this->access[$role][$resource][$acc] = 'D';
            }
        } else {
            if (!in_array($access, $this->access_list[$resource])) {
                throw new KumbiaException("アクセス '$access' はリソース '$resource' の ACL リスト内に存在しません");

            }
            $this->access[$role][$resource][$access] = 'D';
            $this->rebuild_access_list();
        }
    }

    /**
     * 指定されたロールが、リソースに対してアクセス権を持つかどうかを返す
     *
     * 例:
     * <code>
     * // andres は productos リソースに対して insertar できるか？
     * $acl->is_allowed('andres', 'productos', 'insertar');
     *
     * // invitado は任意のリソースに対して editar できるか？
     * $acl->is_allowed('invitado', '*', 'editar');
     * </code>
     *
     * @param string       $role
     * @param string       $resource
     * @param string|array $access_list
     * @return boolean|null
     */
    public function is_allowed($role, $resource, $access_list) {
        if (!in_array($role, $this->roles_names)) {
            throw new KumbiaException("ロール '$role' は ACL リスト内に存在しません（Acl::is_allowed）");

        }
        if (!in_array($resource, $this->resources_names)) {
            throw new KumbiaException("リソース '$resource' は ACL リスト内に存在しません（Acl::is_allowed）");

        }
        if (is_array($access_list)) {
            foreach ($access_list as $access) {
                if (!in_array($access, $this->access_list[$resource])) {
                    throw new KumbiaException("アクセス '$access' はリソース '$resource' の ACL リスト内に存在しません（Acl::is_allowed）");

                }
            }
        } else {
            if (!in_array($access_list, $this->access_list[$resource])) {
                throw new KumbiaException("アクセス '$access_list' はリソース '$resource' の ACL リスト内に存在しません（Acl::is_allowed）");

            }
        }

        /* foreach($this->access[$role] as ){

        } */
        // FIXME: 現時点では簡易な判定のみ。今後改善予定。
        if (!isset($this->access[$role][$resource][$access_list])) {
            return false;
        }

        if ($this->access[$role][$resource][$access_list] == "A") {
            return true;
        }
    }

    /**
     * ロール継承および許可／拒否設定から
     * アクセスリストを再構築する
     *
     * @access private
     */
    private function rebuild_access_list() {
        for ($i = 0; $i <= ceil(count($this->roles)*count($this->roles)/2); $i++) {
            foreach ($this->roles_names as $role) {
                if (isset($this->role_inherits[$role])) {
                    foreach ($this->role_inherits[$role] as $role_inherit) {
                        if (isset($this->access[$role_inherit])) {
                            foreach ($this->access[$role_inherit] as $resource_name => $access) {
                                foreach ($access as $access_name => $value) {
                                    if (!in_array($access_name, $this->access_list[$resource_name])) {
                                        unset($this->access[$role_inherit][$resource_name][$access_name]);
                                    } else {
                                        if (!isset($this->access[$role][$resource_name][$access_name])) {
                                            $this->access[$role][$resource_name][$access_name] = $value;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

}
