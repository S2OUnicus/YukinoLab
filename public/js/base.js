(() => {
    "use strict";

    const CONTAINER_SELECTOR = "#yl-st-mbm-cnt";
    const OFFCANVAS_SELECTOR = "#yl-st-mbmenu";

    /**
     * UIkit offcanvas を安全に閉じる
     */
    function safeHideOffcanvas() {
        try {
            const offcanvasEl = document.querySelector(OFFCANVAS_SELECTOR);
            if (!offcanvasEl) {
                // 必須要素が無い場合は静かに終了（必要なら warn に変更）
                return;
            }

            // UIkitが無い/未ロードの場合
            if (
                typeof window.UIkit === "undefined" ||
                !window.UIkit?.offcanvas
            ) {
                console.warn("[offcanvas] UIkit is not available.");
                return;
            }

            // UIkit.offcanvas(...) が例外を投げる可能性にも備える
            const instance = window.UIkit.offcanvas(offcanvasEl);
            if (!instance || typeof instance.hide !== "function") {
                console.warn("[offcanvas] Offcanvas instance is invalid.");
                return;
            }

            instance.hide();
        } catch (err) {
            console.error("[offcanvas] Failed to hide offcanvas:", err);
        }
    }

    /**
     * click ハンドラ（イベント委譲）
     */
    function onClickWithinContainer(event) {
        try {
            // 左クリック/通常操作のみ（例：右クリックや修飾キー押下時は無視したい場合）
            // 必要なければこの条件は外してOKです
            if (event.button !== 0) return; // 左クリック以外
            if (
                event.metaKey ||
                event.ctrlKey ||
                event.shiftKey ||
                event.altKey
            )
                return;

            const container = document.querySelector(CONTAINER_SELECTOR);
            if (!container) return;

            // クリック元から祖先を辿って container 内の a を探す
            const anchor = event.target?.closest?.("a");
            if (!anchor) return;
            if (!container.contains(anchor)) return;

            // ここで要件の処理を実行
            safeHideOffcanvas();
        } catch (err) {
            console.error("[handler] Unexpected error:", err);
        }
    }

    /**
     * 初期化
     */
    function init() {
        try {
            const container = document.querySelector(CONTAINER_SELECTOR);
            if (!container) {
                console.warn(
                    `[init] Container not found: ${CONTAINER_SELECTOR}`,
                );
                return;
            }

            // containerに直接付けてもOKですが、
            // ここでは document に付けて「後からcontainerが差し替わる」ケースにも多少強くします
            document.addEventListener("click", onClickWithinContainer, {
                passive: true,
            });
        } catch (err) {
            console.error("[init] Failed:", err);
        }
    }

    // DOMContentLoaded（画像等のロードを待たずDOM構築完了でOK）
    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", init, { once: true });
    } else {
        init();
    }
})();
