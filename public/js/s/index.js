(() => {
    "use strict";

    // 3) ナビ画像も指定どおり差し替え
    const NAV_ACTIVE_SRC = "/img/base/main/special/egg_pink.webp";
    const NAV_INACTIVE_SRC = "/img/base/main/special/egg_blue.webp";

    // NAV画像が404だった場合のフォールバック（外部通信しないData URI）
    const FALLBACK_ACTIVE =
        `data:image/svg+xml;utf8,` +
        encodeURIComponent(
            `<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32">
      <circle cx="16" cy="16" r="10" fill="none" stroke="black" stroke-width="2"/>
      <circle cx="16" cy="16" r="4" fill="black"/>
    </svg>`,
        );
    const FALLBACK_INACTIVE =
        `data:image/svg+xml;utf8,` +
        encodeURIComponent(
            `<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32">
      <circle cx="16" cy="16" r="10" fill="none" stroke="black" stroke-width="2" opacity="0.5"/>
    </svg>`,
        );

    const UPDATE_INTERVAL_MS = 120; // 中央判定の間引き（重すぎ防止）
    const PER_PAGE = 3;

    /** 1回だけ画像URLが有効かチェック（404なら以後は使わない） */
    const loadOnceCache = new Map();
    function loadImageOnce(url, timeoutMs = 2500) {
        if (!url) return Promise.resolve(false);
        if (loadOnceCache.has(url)) return loadOnceCache.get(url);

        const p = new Promise((resolve) => {
            const img = new Image();
            let done = false;

            const finish = (ok) => {
                if (done) return;
                done = true;
                cleanup();
                resolve(ok);
            };

            const t = setTimeout(() => finish(false), timeoutMs);

            const cleanup = () => {
                clearTimeout(t);
                img.onload = null;
                img.onerror = null;
            };

            img.onload = () => finish(true);
            img.onerror = () => finish(false);
            img.decoding = "async";
            img.src = url; // ← ここで1回だけリクエスト（404でも1回で止まる）
        });

        loadOnceCache.set(url, p);
        return p;
    }

    function $(id) {
        return document.getElementById(id);
    }

    function assertDeps() {
        if (!window.Splide) {
            console.error("[eggCarousel] Splide が読み込まれていません");
            return false;
        }
        if (!window.splide || !window.splide.Extensions) {
            console.error(
                "[eggCarousel] AutoScroll Extension が読み込まれていません",
            );
            return false;
        }
        return true;
    }

    function safeArray(x) {
        return Array.isArray(x) ? x.filter(Boolean) : [];
    }

    function markBroken(imgEl) {
        imgEl.classList.add("is-broken");
        // これ以上onerrorで無限処理しない
        imgEl.onerror = null;
    }

    /** track中心に最も近いスライドを探して "元のインデックス(0..N-1)" を返す */
    function getActiveIndexByCenter(rootEl, trackEl, n) {
        const trackRect = trackEl.getBoundingClientRect();
        const centerX = trackRect.left + trackRect.width / 2;

        const slides = Array.from(rootEl.querySelectorAll(".splide__slide"));
        let bestSlide = null;
        let bestDist = Infinity;

        for (const slide of slides) {
            const r = slide.getBoundingClientRect();
            const slideCenter = r.left + r.width / 2;
            const d = Math.abs(slideCenter - centerX);
            if (d < bestDist) {
                bestDist = d;
                bestSlide = slide;
            }
        }

        if (!bestSlide) return 0;

        // cloneでも元に戻せる可能性が高い data-splide-index を優先
        const raw =
            bestSlide.getAttribute("data-splide-index") ??
            bestSlide.dataset.slideIndex ??
            "0";
        const idx = Number(raw);
        const mod = ((idx % n) + n) % n;
        return mod;
    }

    function setNavState(navButtons, activeIndex, srcActive, srcInactive) {
        // 画像の src は「状態が変わった時だけ」差し替える（＝404でも無限リクエストを起こさない）
        navButtons.forEach((btn, i) => {
            const img = btn.querySelector("img");
            if (!img) return;
            const next = i === activeIndex ? srcActive : srcInactive;
            if (img.src !== new URL(next, location.href).href) {
                img.src = next;
                img.alt = i === activeIndex ? "active" : "inactive";
            }
        });
    }

    async function init() {
        const slideImages = safeArray(window.slideImages);
        if (slideImages.length === 0) {
            console.warn("[eggCarousel] slideImages が空です");
            return;
        }

        if (!assertDeps()) return;

        const listEl = $("eggCarouselList");
        const navEl = $("eggCarouselNav");
        const rootEl = $("eggCarousel");

        if (!listEl || !navEl || !rootEl) {
            console.error("[eggCarousel] 必要なDOMが見つかりません");
            return;
        }

        // ナビ画像URLを「1回だけ」検証（404ならフォールバックに固定）
        const [activeOk, inactiveOk] = await Promise.all([
            loadImageOnce(NAV_ACTIVE_SRC),
            loadImageOnce(NAV_INACTIVE_SRC),
        ]);
        const resolvedActiveSrc = activeOk ? NAV_ACTIVE_SRC : FALLBACK_ACTIVE;
        const resolvedInactiveSrc = inactiveOk
            ? NAV_INACTIVE_SRC
            : FALLBACK_INACTIVE;

        // スライドDOM生成
        slideImages.forEach((src, i) => {
            const li = document.createElement("li");
            li.className = "splide__slide";
            li.dataset.slideIndex = String(i);

            const img = document.createElement("img");
            img.src = src;
            img.alt = `slide ${i + 1}`;
            img.loading = "lazy";
            img.decoding = "async";
            img.onerror = () => markBroken(img);

            li.appendChild(img);
            listEl.appendChild(li);
        });

        // ナビDOM生成（画像枚数に応じて）
        const navButtons = slideImages.map((_, i) => {
            const btn = document.createElement("button");
            btn.type = "button";
            btn.ariaLabel = `Go to slide ${i + 1}`;

            const img = document.createElement("img");
            // 初期は0番がactive扱い
            img.src = i === 0 ? resolvedActiveSrc : resolvedInactiveSrc;
            img.alt = i === 0 ? "active" : "inactive";
            // ナビ画像が壊れても「同じsrcを再設定しない」ので無限リクエストにはならないが念のため
            img.onerror = () => markBroken(img);

            btn.appendChild(img);
            navEl.appendChild(btn);
            return btn;
        });

        // Splide 初期化
        const splide = new Splide("#eggCarousel", {
            type: "loop",
            perPage: Math.min(PER_PAGE, slideImages.length),
            focus: "center",
            gap: "1rem",
            arrows: false,
            pagination: false,
            drag: "free",
            snap: false,
            direction: "rtl", // 右→左
            autoScroll: {
                speed: 0.7, // 一定速度
                pauseOnHover: false,
                pauseOnFocus: false,
                autoStart: true,
            },
        });

        splide.mount(window.splide.Extensions);

        // ナビクリックでジャンプ
        navButtons.forEach((btn, i) => {
            btn.addEventListener("click", () => {
                try {
                    splide.go(i);
                } catch (e) {
                    console.warn("[eggCarousel] splide.go 失敗", e);
                }
            });
        });

        // 中央判定ループ（間引き + activeIndex変化時だけ nav を更新）
        const trackEl = rootEl.querySelector(".splide__track");
        if (!trackEl) {
            console.error("[eggCarousel] trackが見つかりません");
            return;
        }

        let rafId = null;
        let lastActiveIndex = -1;
        let lastTick = 0;
        let stopped = false;

        function tick(ts) {
            if (stopped) return;
            rafId = requestAnimationFrame(tick);

            if (ts - lastTick < UPDATE_INTERVAL_MS) return;
            lastTick = ts;

            const idx = getActiveIndexByCenter(
                rootEl,
                trackEl,
                slideImages.length,
            );
            if (idx !== lastActiveIndex) {
                lastActiveIndex = idx;
                setNavState(
                    navButtons,
                    idx,
                    resolvedActiveSrc,
                    resolvedInactiveSrc,
                );
            }
        }

        function stopLoop() {
            stopped = true;
            if (rafId) cancelAnimationFrame(rafId);
            rafId = null;

            // AutoScroll停止（存在すれば）
            try {
                splide?.Components?.AutoScroll?.pause?.();
            } catch {}
        }

        function startLoop() {
            if (!stopped) return;
            stopped = false;
            rafId = requestAnimationFrame(tick);

            // AutoScroll再開（存在すれば）
            try {
                splide?.Components?.AutoScroll?.play?.();
            } catch {}
        }

        // 初回スタート
        stopped = false;
        rafId = requestAnimationFrame(tick);

        // タブ非表示時は止める（安全＆省リソース）
        document.addEventListener("visibilitychange", () => {
            if (document.hidden) stopLoop();
            else startLoop();
        });

        // 破棄（SPAなどで必要なら呼べるように）
        window.__destroyEggCarousel = () => {
            try {
                stopLoop();
            } catch {}
            try {
                splide.destroy(true);
            } catch {}
        };
    }

    document.addEventListener("DOMContentLoaded", () => {
        init().catch((e) => console.error("[eggCarousel] init error", e));
    });
})();
