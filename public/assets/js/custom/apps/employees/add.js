"use strict";

// Class definition
let KTEmployeesAddEmployee = (function () {
    let modal, modalEl, stepper, form, formSubmitButton, formContinueButton;

    // Variables
    let stepperObj,
        validations = [];

    let handleForm = function () {
        initStepper(
            stepper,
            stepperObj,
            validations,
            formSubmitButton,
            formContinueButton
        );

        $("#add-dob-picker").flatpickr({
            altInput: true,
            altFormat: "j F Y",
            dateFormat: "Y-m-d",
        });

        formSubmitButton.addEventListener("click", function (e) {
            let validator = validations[1]; // get validator for last form

            validator.validate().then(function (status) {
                if (status == "Valid") {
                    e.preventDefault();

                    formSubmitButton.disabled = true;
                    formSubmitButton.setAttribute("data-kt-indicator", "on");

                    const errorSwal = {
                        buttonsStyling: false,
                        showConfirmButton: true,
                        confirmButtonText: "Close",
                        customClass: {
                            confirmButton: "btn fw-bold btn-primary",
                        },
                    };

                    const formData = new FormData();
                    formData.append(
                        "name",
                        form.querySelector('[name="name"]').value
                    );
                    formData.append(
                        "dob",
                        form.querySelector('[name="dob"]').value
                    );
                    formData.append(
                        "city",
                        form.querySelector('[name="city"]').value
                    );
                    formData.append(
                        "email",
                        form.querySelector('[name="email"]').value
                    );
                    formData.append(
                        "password",
                        form.querySelector('[name="password"]').value
                    );

                    const image = $("#add_employee_image")[0].files[0];
                    if (image) {
                        formData.append("image", image);
                    }

                    $.ajax({
                        url: formSubmitButton.dataset.url,
                        type: "POST",
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: formData,
                        headers: {
                            "X-CSRF-TOKEN": token,
                        },
                    })
                        .done((response) => {
                            closeModal("#kt_modal_add_employee");

                            formSubmitButton.disabled = false;
                            formSubmitButton.removeAttribute(
                                "data-kt-indicator"
                            );

                            if (response.metadata.status == "success")
                                closeModal("#kt_modal_add_employee");

                            Swal.fire(
                                Object.assign(
                                    {
                                        text: response.metadata.message,
                                        icon: response.metadata.status,
                                    },
                                    errorSwal
                                )
                            ).then(() => {
                                if (response.metadata.status == "success") {
                                    const employeeStatuses = $(
                                        ".add-employee-status"
                                    );
                                    for (const employeeStatus of employeeStatuses) {
                                        const employeeStatusClass =
                                            employeeStatus.closest(
                                                ".btn-outline"
                                            ).classList;

                                        employeeStatusClass.remove("active");

                                        if (employeeStatus.value == 1) {
                                            employeeStatusClass.add("active");
                                        }
                                    }

                                    $("#kt_add_employee_form select")
                                        .val("")
                                        .change();

                                    form.reset();
                                    datatable.draw();
                                }
                            });
                        })
                        .fail((xhr, status, error) => {
                            formSubmitButton.disabled = false;
                            formSubmitButton.removeAttribute(
                                "data-kt-indicator"
                            );

                            let message = error;
                            if (xhr?.responseJSON) {
                                message = xhr.responseJSON?.metadata
                                    ? xhr.responseJSON.metadata.message
                                    : xhr.responseJSON?.message;
                            }

                            Swal.fire(
                                Object.assign(
                                    {
                                        text: "Failed create data!. " + message,
                                        icon: "error",
                                    },
                                    errorSwal
                                )
                            );
                        });
                } else {
                    Swal.fire({
                        text: "Sorry, looks like there are some errors detected, please try again.",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn btn-light",
                        },
                    }).then(function () {
                        KTUtil.scrollTop();
                    });
                }
            });
        });
    };

    let initValidation = function () {
        // Step 1
        validations.push(
            FormValidation.formValidation(form, {
                fields: {
                    name: {
                        validators: {
                            notEmpty: {
                                message: "Full name is required",
                            },
                        },
                    },
                    dob: {
                        validators: {
                            notEmpty: {
                                message: "Date of birth is required",
                            },
                        },
                    },
                    city: {
                        validators: {
                            notEmpty: {
                                message: "City is required",
                            },
                        },
                    },
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap: new FormValidation.plugins.Bootstrap5({
                        rowSelector: ".fv-row",
                        eleInvalidClass: "",
                        eleValidClass: "",
                    }),
                },
            })
        );

        // Step 2
        validations.push(
            FormValidation.formValidation(form, {
                fields: {
                    image: {
                        validators: {
                            notEmpty: {
                                message: "Please select an image",
                            },
                            file: {
                                extension: "jpeg,jpg,png",
                                type: "image/jpeg,image/png",
                                maxSize: 1048576, // 1024 * 1024
                                message: "Progile image is not valid",
                            },
                        },
                    },
                    email: {
                        validators: {
                            notEmpty: {
                                message: "Email is required",
                            },
                        },
                    },
                    password: {
                        validators: {
                            notEmpty: {
                                message: "Password is required",
                            },
                            stringLength: {
                                min: 8,
                                message:
                                    "Password must be at least 8 characters",
                            },
                        },
                    },
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap: new FormValidation.plugins.Bootstrap5({
                        rowSelector: ".fv-row",
                        eleInvalidClass: "",
                        eleValidClass: "",
                    }),
                },
            })
        );
    };

    return {
        init: function () {
            modalEl = document.querySelector("#kt_modal_add_employee");

            if (modalEl) {
                modal = new bootstrap.Modal(modalEl);
            }

            stepper = document.querySelector("#kt_add_employee_stepper");

            if (!stepper) {
                return;
            }

            form = stepper.querySelector("#kt_add_employee_form");
            formSubmitButton = stepper.querySelector(
                '[data-kt-stepper-action="submit"]'
            );

            formContinueButton = stepper.querySelector(
                '[data-kt-stepper-action="next"]'
            );

            initValidation();
            handleForm();
        },
    };
})();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTEmployeesAddEmployee.init();
});
