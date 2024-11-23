"use strict";

// Class definition
let KTemployeesEditEmployee = (function () {
    let modal,
        modalEl = document.querySelector("#kt_modal_edit_employee"),
        stepper = document.querySelector("#kt_edit_employee_stepper"),
        form = stepper.querySelector("#kt_edit_employee_form"),
        formSubmitButton = stepper.querySelector(
            '[data-kt-stepper-action="submit"]'
        ),
        formContinueButton = stepper.querySelector(
            '[data-kt-stepper-action="next"]'
        );

    // Variables
    let employeeId,
        currentEmail,
        oldEmail,
        editMonthYearPicker,
        stepperObj,
        validations = [];

    if (modalEl) {
        modal = new bootstrap.Modal(modalEl);
    }

    if (!stepper) {
        return;
    }

    let initEditEmployee = () => {
        initStepper(
            stepper,
            stepperObj,
            validations,
            formSubmitButton,
            formContinueButton
        );

        $(document).on("click", ".edit-employee", function (e) {
            e.preventDefault();

            const editForm = $("#kt_edit_employee_form");
            employeeId = $(this).data("id");

            // Get & Set employee data
            $.ajax({
                url: `${apiPath}/employees/${employeeId}`,
                type: "GET",
            }).then((response) => {
                // Personal Data
                editForm.find("[name='name']").val(response.data.user.name);
                editMonthYearPicker.setDate(response.data.dob);
                editForm.find("[name='dob']").val(response.data.dob);
                editForm.find("[name='city']").val(response.data.city);

                // Account Data
                oldEmail = response.data.user.email;
                editForm.find("[name='email']").val(oldEmail);

                if (response.data.image) {
                    const imageWrapper = editForm
                        .find(".image-input-wrapper")
                        .last()[0];
                    imageWrapper.style.cssText = `background-image: url('storage/${response.data.image}')`;
                }
            });
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

                    currentEmail = form.querySelector('[name="email"]').value;

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

                    const image = $("#edit_employee_image")[0].files[0];
                    const password =
                        form.querySelector('[name="password"]').value;

                    if (currentEmail != oldEmail) {
                        formData.append("email", currentEmail);
                    }

                    if (image) {
                        formData.append("image", image);
                    }

                    if (password) {
                        formData.append("password", password);
                    }

                    $.ajax({
                        url: `${apiPath}/employees/${employeeId}`,
                        type: "POST",
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: formData,
                        headers: {
                            "X-HTTP-Method-Override": "PUT",
                            "X-CSRF-TOKEN": token,
                        },
                    })
                        .done((response) => {
                            closeModal("#kt_modal_edit_employee");

                            formSubmitButton.disabled = false;
                            formSubmitButton.removeAttribute(
                                "data-kt-indicator"
                            );

                            if (response.metadata.status == "success")
                                closeModal("#kt_modal_edit_employee");

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
                                        ".edit-employee-status"
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

                                    $("#kt_edit_employee_form select")
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
                                        text: "Failed edit data!. " + message,
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

        $(form.querySelector('[name="subdistrict_id"]')).on(
            "change",
            function () {
                const subdistrict_id = $(this).val();

                $(form.querySelector('[name="village_id"]')).data(
                    "id",
                    subdistrict_id
                );
            }
        );
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
                                message: "City of birth is required",
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
                            file: {
                                extension: "jpeg,jpg,png",
                                type: "image/jpeg,image/png",
                                maxSize: 1048576, // 1024 * 1024
                                message: "Foto profil is not valid",
                            },
                        },
                    },
                    email: {
                        validators: {
                            notEmpty: {
                                message: "email is required",
                            },
                        },
                    },
                    password: {
                        validators: {
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

    let initEditMonthYearPicker = () => {
        editMonthYearPicker = $("#edit-dob-picker").flatpickr({
            altInput: true,
            altFormat: "j F Y",
            dateFormat: "Y-m-d",
        });
    };

    return {
        init: function () {
            initEditEmployee();
            initValidation();
            initEditMonthYearPicker();
        },
    };
})();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTemployeesEditEmployee.init();
});
