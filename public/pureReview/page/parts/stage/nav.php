<!-- ブランド -->
<div id="yl-st-brand" class="uk-flex uk-flex-middle">
    <!-- サイトロゴ -->
    <div id="yl-st-logo">
        <a href="<?= $sturl ?>"
            title="YUKINO Lab"
            hx-get="<?= $sturl ?>"
            hx-target="#htmx-main"
            hx-select="#htmx-main"
            hx-select-oob="#htmx-aside"
            hx-swap="outerHTML"
            hx-push-url="true"
        >
            <img src="<?= $sturl ?>favicon.ico" alt="YUKINO Lab Logo">
        </a>
    </div>
    <!-- サイト名 -->
    <div id="yl-st-name">
        <a href="<?= $sturl ?>"
            title="YUKINO Lab"
            hx-get="<?= $sturl ?>"
            hx-target="#htmx-main"
            hx-select="#htmx-main"
            hx-select-oob="#htmx-aside"
            hx-swap="outerHTML"
            hx-push-url="true"
        >
            YUKINO Lab
        </a>
    </div>
</div>

<div id="yl-pcmenu" class="uk-flex uk-flex-middle">
    <a href="<?= $sturl ?>"
        title="ホーム"
        uk-tooltip="title: ホーム; pos: bottom"
        hx-get="<?= $sturl ?>"
        hx-target="#htmx-main"
        hx-select="#htmx-main"
        hx-select-oob="#htmx-aside"
        hx-swap="outerHTML"
        hx-push-url="true"
    >
        home
    </a>

    <a href="<?= $sturl ?>"
        title="作品集"
        uk-tooltip="title: 作品集; pos: bottom"
        hx-get="<?= $sturl ?>s/works"
        hx-target="#htmx-main"
        hx-select="#htmx-main"
        hx-select-oob="#htmx-aside"
        hx-swap="outerHTML"
        hx-push-url="true"
    >
        works
    </a>

    <a href="<?= $sturl ?>"
        title="日程"
        uk-tooltip="title: 日程; pos: bottom"
        hx-get="<?= $sturl ?>s/schedule"
        hx-target="#htmx-main"
        hx-select="#htmx-main"
        hx-select-oob="#htmx-aside"
        hx-swap="outerHTML"
        hx-push-url="true"
    >
        schedule
    </a>

    <a href="<?= $sturl ?>"
        title="よくある質問"
        uk-tooltip="title: よくある質問; pos: bottom"
        hx-get="<?= $sturl ?>s/faq"
        hx-target="#htmx-main"
        hx-select="#htmx-main"
        hx-select-oob="#htmx-aside"
        hx-swap="outerHTML"
        hx-push-url="true"
    >
        faq
    </a>

    <a href="<?= $sturl ?>"
        title="個人相関"
        uk-tooltip="title: 個人相関; pos: bottom"
        hx-get="<?= $sturl ?>s/about"
        hx-target="#htmx-main"
        hx-select="#htmx-main"
        hx-select-oob="#htmx-aside"
        hx-swap="outerHTML"
        hx-push-url="true"
    >
        about
    </a>

    <a href="<?= $sturl ?>"
        title="お問い合わせ"
        uk-tooltip="title: お問い合わせ; pos: bottom"
        hx-get="<?= $sturl ?>s/inquiry"
        hx-target="#htmx-main"
        hx-select="#htmx-main"
        hx-select-oob="#htmx-aside"
        hx-swap="outerHTML"
        hx-push-url="true"
    >
        inquiry
    </a>
</div>

<!-- モバイルメニューボタン -->
<button
    id="yl-st-mbmenu-btn"
    class="uk-button uk-button-default uk-flex uk-flex-middle uk-flex-center"
    type="button"
    uk-toggle="target: #yl-st-mbmenu"
>
    ☰
</button>

<!-- モバイルメニュー -->
<div id="yl-st-mbmenu" uk-offcanvas="overlay: true">
    <div class="uk-offcanvas-bar">
        <span>YUKINO Lab</span>

        <hr>

        <div id="yl-st-mbm-cnt" class="uk-flex uk-flex-column">

                <a href="<?= $sturl ?>"
        title="ホーム"
        hx-get="<?= $sturl ?>"
        hx-target="#htmx-main"
        hx-select="#htmx-main"
        hx-select-oob="#htmx-aside"
        hx-swap="outerHTML"
        hx-push-url="true"
    >
        home
    </a>

    <a href="<?= $sturl ?>"
        title="作品集"
        hx-get="<?= $sturl ?>s/works"
        hx-target="#htmx-main"
        hx-select="#htmx-main"
        hx-select-oob="#htmx-aside"
        hx-swap="outerHTML"
        hx-push-url="true"
    >
        works
    </a>

    <a href="<?= $sturl ?>"
        title="日程"
        hx-get="<?= $sturl ?>s/schedule"
        hx-target="#htmx-main"
        hx-select="#htmx-main"
        hx-select-oob="#htmx-aside"
        hx-swap="outerHTML"
        hx-push-url="true"
    >
        schedule
    </a>

    <a href="<?= $sturl ?>"
        title="よくある質問"
        hx-get="<?= $sturl ?>s/faq"
        hx-target="#htmx-main"
        hx-select="#htmx-main"
        hx-select-oob="#htmx-aside"
        hx-swap="outerHTML"
        hx-push-url="true"
    >
        faq
    </a>

    <a href="<?= $sturl ?>"
        title="個人相関"
        hx-get="<?= $sturl ?>s/about"
        hx-target="#htmx-main"
        hx-select="#htmx-main"
        hx-select-oob="#htmx-aside"
        hx-swap="outerHTML"
        hx-push-url="true"
    >
        about
    </a>

    <a href="<?= $sturl ?>"
        title="お問い合わせ"
        hx-get="<?= $sturl ?>s/inquiry"
        hx-target="#htmx-main"
        hx-select="#htmx-main"
        hx-select-oob="#htmx-aside"
        hx-swap="outerHTML"
        hx-push-url="true"
    >
        inquiry
    </a>

        </div>

    </div>
</div>
