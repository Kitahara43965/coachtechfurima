document.addEventListener("DOMContentLoaded", function () {
    const config = window.categorySelectConfig || {};
    const buttons = document.querySelectorAll(config.buttonSelector);
    const hiddenInput = document.querySelector(config.hiddenInputSelector);

    if (!buttons.length || !hiddenInput) return;

    buttons.forEach((button) => {
        button.addEventListener("click", function () {
            this.classList.toggle(config.activeClass || "active");

            const selectedIds = Array.from(buttons)
                .filter((btn) =>
                    btn.classList.contains(config.activeClass || "active")
                )
                .map((btn) => btn.dataset.id);

            hiddenInput.value = selectedIds.join(",");
        });
    });
});
