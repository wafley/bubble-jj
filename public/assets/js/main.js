// Setup global AJAX behavior
$.ajaxSetup({
    headers: {
        "X-Requested-With": "XMLHttpRequest",
        "X-CSRF-TOKEN": $("meta[name='csrf-token']").attr("content"),
    },
    error: function (xhr) {
        if (xhr.status === 401 || xhr.status === 419) {
            showToast("warning", "Silahkan login untuk melanjutkan.", 5000);
            loadPage(window.routes.login);
            history.pushState(null, null, window.routes.login);
        }
    },
});

$(document).on("submit", "form[data-ajax='true']", function (e) {
    e.preventDefault();

    let $form = $(this);
    let url = $form.attr("action");
    let method = $form.attr("method") || "POST";

    let formData = new FormData(this);

    let requireConfirm =
        $form.attr("confirm-password") === true ||
        $form.attr("confirm-password") === "true";

    let btnSubmit = $form.find("button[type=submit]");
    let btnText = btnSubmit.text();
    let loadingText = btnSubmit.data("loading-text") || "Loading...";

    btnSubmit.prop("disabled", true).text(loadingText);

    ajaxRequest({
        url,
        method,
        data: formData,
        isFormData: true,
        confirmPassword: requireConfirm,
        onComplete: () => {
            btnSubmit.prop("disabled", false).text(btnText);

            let modalEl = $form.closest(".modal")[0];
            if (modalEl) {
                let modalInstance =
                    bootstrap.Modal.getInstance(modalEl) ||
                    new bootstrap.Modal(modalEl);
                modalInstance.hide();
            }
        },
    });
});

// Handle profile picture update
$(document).on("change", "[id^=picture_]", function () {
    const input = this;
    const url = $(input).data("url");

    if (input.files && input.files[0]) {
        const formData = new FormData();
        formData.append("picture", input.files[0]);
        formData.append("_method", "PATCH");

        ajaxRequest({
            url: url,
            method: "POST",
            data: formData,
            isFormData: true,
            onSuccess: function (res) {
                showToast("success", res.message, 3000);

                // preview update
                const reader = new FileReader();
                reader.onload = function (e) {
                    $(input)
                        .prev("label")
                        .find("img")
                        .attr("src", e.target.result);
                };
                reader.readAsDataURL(input.files[0]);
            },
        });
    }
});
