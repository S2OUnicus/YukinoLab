<?php
// サイト：ベースパス
$sturl = "/pureReview/";
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
            <?php require_once('../page/parts/stage/header.php'); ?>

            <!-- Stage Navigation -->
            <?php require_once('../page/parts/stage/nav.php'); ?>

            <!--
            (!! Important !!) Stage Main: [Works]
            -->
            <?php require_once('inner/_works.php'); ?>

            <!-- Stage Footer -->
            <?php require_once('../page/parts/stage/footer.php'); ?>
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