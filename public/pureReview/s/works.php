<?php
// サイト：ベースパス
$sturl = "/pureReview/";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <?php require_once('../page/_shared/meta.php'); ?>

	<title>Yukino Lab 公式</title>

    <?php require_once('../page/_shared/link.php'); ?>

    <!-- ページ仕様 -->
    <link rel="stylesheet" href="<?= $sturl ?>s/works.css?<?= time(); ?>">
</head>
<body hx-history-elt hx-ext="head-support">
    <noscript>This Page Requires Javascript.</noscript>

    <!-- Layers -->
    <div id="yl-layers">
        <!-- yl-ly-ground バックグラウンド用 -->
        <div id="yl-ly-ground" class="uk-flex uk-flex-column uk-flex-middle">
            <?php require_once('../page/layer/ground.php'); ?>
        </div>

        <!-- yl-ly-stage ステージパーツ用 -->
        <div id="yl-ly-stage" class="uk-flex uk-flex-column uk-flex-middle">
            <!-- Stage Header -->
            <header class="uk-flex uk-flex-middle uk-flex-center">
                <?php require_once('../page/parts/stage/header.php'); ?>
            </header>

            <!-- Stage Navigation -->
            <nav class="uk-flex uk-flex-middle uk-flex-center">
                <?php require_once('../page/parts/stage/nav.php'); ?>
            </nav>

            <!--
            (!! Important !!) Stage Main: [Works]
            -->
            <main class="uk-flex uk-flex-column uk-flex-center">
                <?php require_once('inner/_works.php'); ?>
            </main>

            <!-- Stage Footer -->
            <footer class="uk-flex uk-flex-column uk-flex-middle uk-flex-center">
                <?php require_once('../page/parts/stage/footer.php'); ?>
            </footer>
        </div>

        <!-- yl-ly-cover ステージに浮かぶもの -->
        <div id="yl-ly-cover" class="uk-flex uk-flex-column uk-flex-middle">
            <?php require_once('../page/layer/cover.php'); ?>
        </div>
    </div>

    <!-- Main Scripts -->
    <?php require_once('../page/_shared/scripts.php'); ?>

    <!-- User Main -->
    <script src="<?= $sturl ?>js/base.js?<?= time(); ?>"></script>
</body>
</html>