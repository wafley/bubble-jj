const layout = {
    breadcrumb: "#breadcrumb",
    content: "#page-content",
    modalContainer: "#modal-container",
};

// AJAX helper
function ajaxRequest(options) {
    const {
        url,
        method = "POST",
        data = {},
        isFormData = false,
        confirm = null,
        confirmPassword = false,
        onSuccess,
        onError,
        onComplete,
    } = options;

    const runAjax = () => {
        $.ajax({
            url: url,
            type: method,
            data: data,
            dataType: "json",
            processData: !isFormData,
            contentType: isFormData
                ? false
                : "application/x-www-form-urlencoded; charset=UTF-8",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: (res) => {
                if (typeof onSuccess === "function") {
                    onSuccess(res);
                } else {
                    if (res.message) {
                        showToast(
                            res.status,
                            res.message,
                            res.status === "success" ? 3000 : 5000
                        );
                    }

                    if (res.redirect_type) {
                        switch (res.redirect_type) {
                            case "history":
                                if (res.redirect === "back") {
                                    window.history.back();
                                } else if (res.redirect === "forward") {
                                    window.history.forward();
                                }
                                break;
                            case "reload":
                                window.location.reload();
                                break;
                            case "spa":
                                loadPage(res.redirect);
                                history.pushState(null, null, res.redirect);
                                break;
                            case "http":
                            default:
                                window.location.href = res.redirect;
                                break;
                        }
                    }
                }
            },
            error: (xhr, status, error) => {
                if (typeof onError === "function") {
                    onError(xhr, status, error);
                } else {
                    if (xhr.status === 422 && xhr.responseJSON.errors) {
                        const errors = Object.values(xhr.responseJSON.errors)
                            .flat()
                            .join("<br>");
                        showToast(
                            "error",
                            errors || "Data yang dimasukkan tidak valid",
                            5000
                        );
                    } else if (xhr.status === 401 || xhr.status === 419) {
                        showToast(
                            "warning",
                            "Silahkan login untuk melanjutkan.",
                            5000
                        );
                        loadPage(window.routes.login);
                        history.pushState(null, null, window.routes.login);
                    } else {
                        showToast(
                            "error",
                            xhr.responseJSON?.message ||
                                "Terjadi kesalahan, coba lagi.",
                            5000
                        );
                    }
                }
            },
            complete: () => {
                if (typeof onComplete === "function") {
                    onComplete();
                } else {
                    console.log("AJAX request completed");
                }
            },
        });
    };

    const execute = () => {
        if (confirmPassword) {
            requirePasswordThen(runAjax);
        } else {
            runAjax();
        }
    };

    if (confirm) {
        Swal.fire({
            title: confirm.title || "Apakah Anda yakin?",
            text: confirm.text || "",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: confirm.confirmButtonText || "Ya",
            cancelButtonText: confirm.cancelButtonText || "Batal",
        }).then((result) => {
            if (result.isConfirmed) {
                execute();
            }
        });
    } else {
        execute();
    }
}

// Active SPA link handler
function setActiveLink(url) {
    $(".spa-link").removeClass("active");

    $(".spa-link").each(function () {
        const href = $(this).attr("href");

        if (url.startsWith(href)) {
            $(this).addClass("active");
        }
    });
}

// SPA load helper
function loadPage(url) {
    loader(true);

    ajaxRequest({
        url: url,
        method: "GET",
        onSuccess: (res) => {
            // Remove partial tags
            $('style[data-partial="1"], script[data-partial="1"]').remove();

            // Load styles
            if (res.styles) {
                $("head").append($(res.styles).attr("data-partial", "1"));
            }

            // Load content
            $(layout.content).html(res.content || "");

            // Load modal
            if (res.modal) {
                $(layout.modalContainer).html(res.modal);
            }

            // Load script
            if (res.scripts) {
                $("body").append($(res.scripts).attr("data-partial", "1"));
            }

            // Replace title & breadcrumb
            if (res.title) {
                document.title = res.title + " - " + window.appName;
                $(layout.breadcrumb).text(res.title);
            }

            setActiveLink(url);
        },
        onError: (xhr) => {
            if (
                xhr.status === 302 ||
                xhr.status === 419 ||
                xhr.status === 401
            ) {
                window.location.href = url;
                return;
            }

            $(layout.content).html(
                '<h4 class="text-danger">Gagal memuat halaman.</h4>'
            );
        },
        onComplete: () => {
            loader(false);
        },
    });
}

// SPA click handler
$(document).on("click", ".spa-link", function (e) {
    const url = $(this).attr("href");
    if (!url || url === "#") return;

    e.preventDefault();
    loadPage(url);
    history.pushState(null, null, url);
});

// Browser back/forward
window.onpopstate = function () {
    loadPage(location.href);
};
