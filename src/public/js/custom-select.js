document.addEventListener("DOMContentLoaded", function () {
    const config = window.customSelectConfig;
    if (!config) return;

    const id = config.id ? String(config.id) : null;
    const wrapperName = config.wrapperName || "custom-select-wrapper";
    const isDisabled =
        config.isDisabled === true || config.isDisabled === "true";
    const selectWrapper = document.querySelector(`.${wrapperName}`);
    const selectedOption = document.getElementById("custom-select-id-name");
    const customSelectPlaceholder = selectedOption.querySelector(
        ".custom-select-placeholder"
    );
    const placeholder = config.placeholder || "";
    const selectedText = selectedOption.querySelector(
        ".custom-select-selected-text"
    );
    const list = selectWrapper.querySelector(".custom-select-list");
    const itemClass = "custom-select-item";
    const items = selectWrapper.querySelectorAll(`.${itemClass}`);
    const otherDisplay = document.getElementById(
        "custom-select-other-display-id"
    );
    const isFetch = config.isFetch === true || config.isFetch === "true";
    const updateUrl = config.updateUrl || null;
    const idName = config.idName || null;
    const hiddenInput = document.getElementById("hidden-" + idName);

    let timeoutId;

    if (!selectWrapper || !selectedOption || !list) return;

    // 初期選択
    if (id) {
        const selectedItem = selectWrapper.querySelector(
            `.${itemClass}[data-id="${id}"]`
        );
        if (selectedItem) {
            const selectedId = selectedItem.getAttribute("data-id");
            if (hiddenInput) hiddenInput.value = selectedId;

            selectedText.textContent = selectedItem.getAttribute("data-name");
            if (otherDisplay)
                otherDisplay.textContent = selectedText.textContent;
            selectedItem.classList.add("selected");
            customSelectPlaceholder.style.display = "none";
            selectedText.style.display = "inline";
        }
    } else {
        customSelectPlaceholder.style.display = "inline";
        selectedText.style.display = "none";
        if (otherDisplay) otherDisplay.textContent = placeholder;
    }

    if (isDisabled) {
        selectedOption.classList.add("disabled"); // CSSで見た目を変更
        selectedOption.addEventListener("click", function (e) {
            e.stopPropagation(); // クリックイベントを無効化
        });

        // リスト自体も非表示にしておく
        list.style.display = "none";

        items.forEach((item) => {
            item.addEventListener("click", function (e) {
                e.stopPropagation();
            });
        });
    } else {
        // 通常のクリックイベント
        selectedOption.addEventListener("click", function () {
            const isVisible = list.style.display === "block";
            list.style.display = isVisible ? "none" : "block";
        });

        // 選択処理
        items.forEach((item) => {
            item.addEventListener("click", function () {
                const selectedId = item.getAttribute("data-id"); // 選んだID
                const selectedName = item.getAttribute("data-name");

                // hidden input にセット
                if (hiddenInput) hiddenInput.value = selectedId;

                // 表示更新
                selectedText.textContent = selectedName;
                customSelectPlaceholder.style.display = "none";
                selectedText.style.display = "inline";

                items.forEach((i) => i.classList.remove("selected"));
                item.classList.add("selected");

                if (otherDisplay) otherDisplay.textContent = selectedName;

                if (isFetch && updateUrl) {
                    // サーバー送信
                    const bodyData = {
                        [idName]: selectedId,
                        is_filled_with_delivery_address:
                            config.isFilledWithDeliveryAddress || false,
                        delivery_postcode: config.deliveryPostcode || "",
                        delivery_address: config.deliveryAddress || "",
                        delivery_building: config.deliveryBuilding || "",
                    };

                    fetch(updateUrl, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": config.csrfToken,
                        },
                        body: JSON.stringify(bodyData),
                    })
                        .then((res) =>
                            res.ok
                                ? res.json()
                                : Promise.reject(
                                      `HTTP error! status: ${res.status}`
                                  )
                        )
                        .then((data) => console.log("サーバー応答:", data))
                        .catch((err) => console.error("通信エラー:", err));
                }
            });
        });

        // リストの外にカーソルが出たときにリストを非表示にする（ただし1秒後に非表示）
        list.addEventListener("mouseleave", function () {
            // カーソルがリスト外に出た場合、1秒後にリストを非表示にする
            timeoutId = setTimeout(() => {
                list.style.display = "none";
            }, 300); // 1秒後に非表示に
        });

        // リストにカーソルが入ったときには、非表示処理をキャンセル
        list.addEventListener("mouseenter", function () {
            clearTimeout(timeoutId);
        });

        // 入力欄からカーソルが外れたときにリストが消えないように
        selectedOption.addEventListener("mouseleave", function () {
            // 何もしない（リストを消さない）
        });

        // 入力欄にカーソルが入ったとき、リストを表示する
        selectedOption.addEventListener("mouseenter", function () {
            // リストが表示されていない場合は表示
            if (list.style.display === "none") {
                list.style.display = "block";
            }
        });
    }
});
