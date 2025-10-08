function initUploadHandler(options) {
    const {
        formSelector = "#form-upload",
        buttonText = "Upload Sekarang",
        rules = {},
        previewSelector = null,
        previewType = "auto",
        multiple = false,
        passwordCheckUrl,
    } = options;

    const $form = $(formSelector);
    const $btnSubmit = $form.find("button[type=submit]");
    const $fileInput = $form.find("input[type='file']");
    const $previewContainer = previewSelector ? $(previewSelector) : null;

    let filesArray = [];

    /** Toggle submit button */
    const toggleButton = (enabled, text = buttonText) =>
        $btnSubmit
            .prop("disabled", !enabled)
            .text(enabled ? text : "Loading...");

    /** Show file preview */
    function renderPreview(
        files,
        container,
        type = "auto",
        multiple = false,
        inputEl = null
    ) {
        container.html("");
        if (!files) return;

        const fileList = multiple ? files : [files];

        fileList.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = (e) => {
                const wrapper = $("<div>")
                    .addClass("position-relative m-2")
                    .css({
                        width: multiple ? "120px" : "auto",
                        height: multiple ? "120px" : "auto",
                    });

                let fileType = type;
                if (fileType === "auto") {
                    if (file.type.startsWith("image/")) fileType = "image";
                    else if (file.type.startsWith("video/")) fileType = "video";
                }

                let previewEl;
                if (fileType === "image") {
                    previewEl = $("<img>")
                        .attr("src", e.target.result)
                        .addClass("border")
                        .css({
                            width: "100%",
                            height: "100%",
                            objectFit: "cover",
                            borderRadius: "8px",
                        });
                } else if (fileType === "video") {
                    previewEl = $("<video>")
                        .attr({ src: e.target.result, controls: true })
                        .css({
                            width: "100%",
                            height: "100%",
                            objectFit: "cover",
                            borderRadius: "8px",
                        });
                } else {
                    previewEl = $(
                        '<span class="text-danger">Format tidak didukung</span>'
                    );
                }

                const removeBtn = $("<button>")
                    .addClass(
                        "btn btn-sm btn-danger position-absolute top-0 end-0 m-1 p-1"
                    )
                    .html('<i class="mdi mdi-close"></i>')
                    .on("click", () => {
                        if (multiple && Array.isArray(files)) {
                            files.splice(index, 1);
                            if (inputEl) {
                                const dt = new DataTransfer();
                                files.forEach((f) => dt.items.add(f));
                                inputEl.files = dt.files;
                            }
                            renderPreview(
                                files,
                                container,
                                type,
                                multiple,
                                inputEl
                            );
                        } else {
                            container.html("");
                            if (inputEl) inputEl.value = "";
                        }
                    });

                wrapper.append(previewEl, removeBtn);
                container.append(wrapper);
            };
            reader.readAsDataURL(file);
        });
    }

    /** Validate file against rules */
    function validateFile(file, callback) {
        if (!file) {
            showToast("error", "Pilih file terlebih dahulu!");
            toggleButton(true);
            return;
        }

        const checkSize = rules.max_size && file.size > rules.max_size;
        if (checkSize) {
            showToast(
                "error",
                `Ukuran file melebihi ${Math.round(
                    rules.max_size / 1024 / 1024
                )}MB!`
            );
            toggleButton(true);
            return;
        }

        if (
            file.type.startsWith("video/") &&
            (rules.min_duration || rules.max_duration)
        ) {
            const video = document.createElement("video");
            video.preload = "metadata";
            video.src = URL.createObjectURL(file);

            video.onloadedmetadata = () => {
                URL.revokeObjectURL(video.src);

                if (rules.min_duration && video.duration < rules.min_duration) {
                    showToast(
                        "error",
                        `Durasi video minimal ${rules.min_duration} detik!`
                    );
                    toggleButton(true);
                    return;
                }

                if (rules.max_duration && video.duration > rules.max_duration) {
                    showToast(
                        "error",
                        `Durasi video maksimal ${rules.max_duration} detik!`
                    );
                    toggleButton(true);
                    return;
                }

                callback();
            };
        } else {
            callback();
        }
    }

    /** Submit form via AJAX */
    function submitAjax() {
        const formData = new FormData($form[0]);

        $.ajax({
            url: $form.attr("action"),
            type: $form.attr("method"),
            data: formData,
            processData: false,
            contentType: false,
            success: (res) => {
                if (res.status === "success") {
                    $form[0].reset();
                    if ($previewContainer)
                        $previewContainer.html("").addClass("d-none");
                    showToast("success", res.message);
                } else showToast("error", res.message);
                toggleButton(true);
            },
            error: (xhr) => {
                if (xhr.status === 422 && xhr.responseJSON?.errors) {
                    const errors = Object.values(xhr.responseJSON.errors)
                        .flat()
                        .join("<br>");
                    showToast(
                        "error",
                        errors || "Data yang dimasukkan tidak valid"
                    );
                } else {
                    showToast(
                        "error",
                        xhr.responseJSON?.message ||
                            "Terjadi kesalahan, coba lagi."
                    );
                }
                toggleButton(true);
            },
        });
    }

    /** Handle form submit */
    $form.on("submit", (e) => {
        e.preventDefault();
        toggleButton(false);

        const processUpload = () => {
            if (multiple) {
                if (!filesArray.length) {
                    showToast("error", "Pilih minimal 1 file!");
                    toggleButton(true);
                    return;
                }

                const checkNext = (i) => {
                    if (i >= filesArray.length) return submitAjax();
                    validateFile(filesArray[i], () => checkNext(i + 1));
                };
                checkNext(0);
            } else {
                validateFile($fileInput[0]?.files[0], submitAjax);
            }
        };

        if (passwordCheckUrl) {
            $.get(passwordCheckUrl)
                .done((res) =>
                    res.confirmed
                        ? processUpload()
                        : confirmPassword(processUpload)
                )
                .fail(() => {
                    showToast("error", "Gagal memeriksa password.");
                    toggleButton(true);
                });
        } else processUpload();
    });

    /** Handle file input change */
    if ($previewContainer && $fileInput.length) {
        $fileInput.on("change", function () {
            if (multiple) {
                filesArray = Array.from(this.files);
                renderPreview(
                    filesArray,
                    $previewContainer,
                    previewType,
                    true,
                    this
                );
                $previewContainer.removeClass("d-none");
            } else {
                const file = this.files[0];
                renderPreview(
                    file,
                    $previewContainer,
                    previewType,
                    false,
                    this
                );
                $previewContainer.toggleClass("d-none", !file);
            }
        });
    }
}
