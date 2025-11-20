document.addEventListener("DOMContentLoaded", function () {
    const input = document.getElementById("imageInput");
    const previewField = document.getElementById("preview");
    const imageNameField = document.getElementById("imageName");
    const hiddenImageName = document.querySelector('input[name="image_name"]');
    const hiddenPreviewUrl = document.querySelector(
        'input[name="preview_url"]'
    );

    const imagePrefix = window.imagePrefix;
    const previewContainer = window.profileImageConfig?.previewContainerSelector
        ? document.querySelector(
              window.profileImageConfig.previewContainerSelector
          )
        : null;

    const preview = window.profileImageConfig?.previewSelector
        ? document.querySelector(window.profileImageConfig.previewSelector)
        : null;

    const fileUploadButtonSelector =
        window.profileImageConfig?.fileUploadButtonSelector;

    const fileUploadButton = fileUploadButtonSelector
        ? document.querySelector(fileUploadButtonSelector)
        : null;

    if (!input || !previewField) return;

    // ✅ ここがポイント！
    // old input があるけど、ファイルは存在しない or バリデーションエラーだったときに初期化
    const hasValidationError = document.querySelector(".form__error");
    if (hasValidationError && !window.hasPreview) {
        imageNameField.value = "";
        hiddenImageName.value = "";
        hiddenPreviewUrl.value = "";
        previewField.src = "";
        previewField.style.display = "none";
    }

    input.addEventListener("change", function (event) {
        const file = event.target.files[0];
        if (file) {
            const extension = file.name.split(".").pop();

            fetch(window.route.countImages)
                .then((response) => {
                    if (!response.ok) throw new Error("サーバーエラー");
                    return response.json();
                })
                .then((data) => {
                    imageName = `${imagePrefix}${data.count + 1}.${extension}`;
                    imageNameField.value = imageName;
                    hiddenImageName.value = imageName;

                    previewField.src = URL.createObjectURL(file);
                    previewField.style.display = "block";

                    if (imagePrefix === "user") {
                    } else if (imagePrefix === "item") {
                        if (hiddenImageName.value) {
                            // 画像あり → 大きく／ボタン非表示
                            previewContainer?.classList.add("large");
                            preview?.classList.add("large");
                            fileUploadButton?.classList.add("none");
                        } else {
                            // 画像なし → 元に戻す
                            previewContainer?.classList.remove("large");
                            preview?.classList.remove("large");
                            fileUploadButton?.classList.remove("none");
                        }
                    } else {
                    }
                })
                .catch((error) => {
                    console.error("fetch エラー:", error);
                    alert("ファイル名の取得に失敗しました");
                });
        }
    });
});
