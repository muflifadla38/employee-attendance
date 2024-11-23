"use strict";

// Class definition
let KTSigninGeneral = (function () {
    // Elements
    let form;
    let submitButton;
    let validator;

    // Handle form
    let handleValidation = function (e) {
        // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
        validator = FormValidation.formValidation(form, {
            fields: {
                email: {
                    validators: {
                        notEmpty: {
                            message: "email is required",
                        },
                    },
                },
                password: {
                    validators: {
                        notEmpty: {
                            message: "The password is required",
                        },
                    },
                },
            },
            plugins: {
                trigger: new FormValidation.plugins.Trigger(),
                bootstrap: new FormValidation.plugins.Bootstrap5({
                    rowSelector: ".fv-row",
                    eleInvalidClass: "", // comment to enable invalid state icons
                    eleValidClass: "", // comment to enable valid state icons
                }),
            },
        });
    };

    let handleSubmitDemo = function (e) {
        // Handle form submit
        submitButton.addEventListener("click", function (e) {
            // Prevent button default action
            e.preventDefault();

            // Validate form
            validator.validate().then(function (status) {
                if (status == "Valid") {
                    // Show loading indication
                    submitButton.setAttribute("data-kt-indicator", "on");

                    // Disable button to avoid multiple click
                    submitButton.disabled = true;

                    const errorSwal = {
                        buttonsStyling: false,
                        showConfirmButton: true,
                        confirmButtonText: "Close",
                        customClass: {
                            confirmButton: "btn fw-bold btn-primary",
                        },
                    };

                    $.ajax({
                        url: "/api/v1/login",
                        type: "POST",
                        cache: false,
                        data: {
                            email: $('[name="email"]').val(),
                            password: $('[name="password"]').val(),
                            _token: $('[name="csrf-token"]').attr("content"),
                        },
                    })
                        .done((response) => {
                            // Remove loading indication
                            submitButton.removeAttribute("data-kt-indicator");

                            // Enable button
                            submitButton.disabled = false;

                            // Show popup confirmation
                            Swal.fire(
                                Object.assign(
                                    {
                                        text: response.metadata.message,
                                        icon: response.metadata.status,
                                    },
                                    errorSwal
                                )
                            ).then(() => {
                                form.reset(); // reset form

                                let redirectUrl = form.getAttribute(
                                    "data-kt-redirect-url"
                                );

                                if (
                                    response.metadata.status == "success" &&
                                    redirectUrl
                                ) {
                                    location.href = redirectUrl;
                                }
                            });
                        })
                        .fail((xhr, status, error) => {
                            Swal.fire(
                                Object.assign(
                                    {
                                        text: `Gagal login!. ${xhr.responseJSON.metadata.message}`,
                                        icon: "error",
                                    },
                                    errorSwal
                                )
                            );

                            // Remove loading indication
                            submitButton.removeAttribute("data-kt-indicator");

                            // Enable button
                            submitButton.disabled = false;
                        });
                } else {
                    // Show error popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                    Swal.fire({
                        text: "Sorry, looks like there are some errors detected, please try again.",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn btn-primary",
                        },
                    });
                }
            });
        });
    };

    // Public functions
    return {
        // Initialization
        init: function () {
            form = document.querySelector("#kt_sign_in_form");
            submitButton = document.querySelector("#kt_sign_in_submit");

            handleValidation();
            handleSubmitDemo();
        },
    };
})();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTSigninGeneral.init();
});
