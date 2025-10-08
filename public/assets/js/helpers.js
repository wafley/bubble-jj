// Loader helper
let loaderCount = 0;
function loader(status) {
    loaderCount += status ? 1 : -1;
    loaderCount = Math.max(loaderCount, 0);
    $("html").attr("loader", loaderCount > 0 ? "enable" : "disable");
}

// Toggle password handler
function togglePassword(passwordId, toggleBtn) {
    const passwordInput = $("#" + passwordId);
    const type =
        passwordInput.attr("type") === "password" ? "text" : "password";
    passwordInput.attr("type", type);
    $(toggleBtn).find("i").toggleClass("fa-eye fa-eye-slash");
}

// Input phone handler
$(document).on("input", "#phone", function () {
    this.value = this.value.replace(/[^0-9]/g, "");
});

// Username input handler
function sanitizeUsername(value) {
    if (!value) return null;

    value = value.trim().toLowerCase();
    value = value.replace(/^(https?:\/\/)?(www\.)?tiktok\.com\/@/i, "");
    return value.replace(/^@/, "");
}

$(document).on("blur", "#username, #username_1, #username_2", function () {
    this.value = sanitizeUsername(this.value) || "";
});

// Show toast helper
function showToast(icon, message, timer = 3000) {
    Swal.fire({
        toast: true,
        position: "top-end",
        icon: icon,
        title: message,
        showConfirmButton: false,
        timer: timer,
    });
}

// Password confirmation helper
function requirePasswordThen(action) {
    const runAction = () => {
        if (typeof action === "function") {
            action();
        }
    };

    // Check if password is already confirmed
    $.get(window.checkPasswordUrl, function (res) {
        if (res.confirmed) {
            runAction();
        } else {
            // Show modal to confirm password
            const modalEl = document.getElementById("confirmPasswordModal");
            const modal = new bootstrap.Modal(modalEl);
            const form = document.getElementById("form-confirm-password");

            form.reset();
            modal.show();

            $(form)
                .off("submit")
                .on("submit", function (e) {
                    e.preventDefault();

                    $.ajax({
                        url: form.action,
                        method: form.method,
                        data: $(form).serialize(),
                        success: function (res) {
                            if (res.status === "success") {
                                modal.hide();
                                runAction();
                            }
                        },
                        error: function (xhr) {
                            let msg =
                                xhr.responseJSON?.message || "Password salah!";
                            showToast("error", msg);
                        },
                    });
                });
        }
    });
}
