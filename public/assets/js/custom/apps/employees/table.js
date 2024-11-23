"use strict";

let badgeColor = (role) => {
    let color;
    switch (role) {
        case "BUMD":
        case "Lapangan":
            color = "danger";
            break;
        case "PPPK":
        case "SPPD":
            color = "success";
            break;
        case "ASN" || "Berkantor":
        case "Berkantor":
            color = "primary";
            break;
        default:
            color = "secondary";
            break;
    }

    return color;
};

let initStepper = (
    stepper,
    stepperObj,
    validations,
    formSubmitButton,
    formContinueButton
) => {
    stepperObj = new KTStepper(stepper);

    // Handle navigation click
    stepperObj.on("kt.stepper.click", function (stepper) {
        stepper.goTo(stepper.getClickedStepIndex());
    });

    stepperObj.on("kt.stepper.changed", function (stepper) {
        if (stepperObj.getCurrentStepIndex() === 5) {
            formSubmitButton.classList.remove("d-none");
            formSubmitButton.classList.add("d-inline-block");
            formContinueButton.classList.add("d-none");
        } else if (stepperObj.getCurrentStepIndex() === 6) {
            formSubmitButton.classList.add("d-none");
            formContinueButton.classList.add("d-none");
        } else {
            formSubmitButton.classList.remove("d-inline-block");
            formSubmitButton.classList.remove("d-none");
            formContinueButton.classList.remove("d-none");
        }
    });

    // Validation before going to next page
    stepperObj.on("kt.stepper.next", function (stepper) {
        let validator = validations[stepper.getCurrentStepIndex() - 1]; // get validator for currnt step

        if (validator) {
            validator.validate().then(function (status) {
                if (status == "Valid") {
                    stepper.goNext();
                    KTUtil.scrollTop();
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
        } else {
            stepper.goNext();

            KTUtil.scrollTop();
        }
    });

    // Prev event
    stepperObj.on("kt.stepper.previous", function (stepper) {
        stepper.goPrevious();
        KTUtil.scrollTop();
    });
};

// Class definition
let KTDatatablesServerSide = (function () {
    let table, searchTimeout;

    const filterForm = document.querySelector(
            '[data-kt-employee-table-filter="form"]'
        ),
        statusElement = filterForm.querySelector('[name="status-filter"]'),
        token = $("meta[name='csrf-token']").attr("content");

    window.token = token;

    // Shared variables
    let datatable,
        status = "active";

    // Private functions
    let initDatatable = function () {
        datatable = $("#kt_table_employees").DataTable({
            searchDelay: 500,
            processing: true,
            serverSide: true,
            order: [],
            ajax: {
                url: $("#table-url").val(),
                data: function (data) {
                    data.status = status;
                },
            },
            columns: [
                { data: "user.name", name: "user.name" },
                { data: "user.email", name: "user.email" },
                { data: "dob" },
                { data: "city" },
                { data: "user.status", name: "user.status" },
                { data: "action", orderable: false, searchable: false },
            ],
            columnDefs: [
                {
                    targets: 0,
                    className: "d-flex align-items-center",
                    render: function (data, type, row) {
                        const profile = row.user.image
                            ? `<div class="symbol symbol-50px symbol-circle">
                                    <div class="image symbol-label" style="background-image:url('storage/${row.user.image}')"></div>
                                </div>`
                            : `
                                <div class="symbol-label">
                                    <div class="symbol-label fs-2 fw-semibold bg-primary text-inverse-primary">${data.charAt(
                                        0
                                    )}</div>
                                </div>`;

                        return `
                        <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                            ${profile}
                        </div>
                        <div>
                            <div class="d-flex flex-column">
                                <span class="text-gray-800 mb-1">${data}</span>
                            </div>
                        </div>`;
                    },
                },
                {
                    targets: 2,
                    render: function (data, type, row) {
                        return moment(data).format("D MMMM YYYY");
                    },
                },
                {
                    targets: 4,
                    render: function (data, type, row) {
                        return `<span class="badge badge-light-${colorType(
                            data
                        )} fs-7 fw-bold text-capitalize">${data}</span>`;
                    },
                },
                {
                    targets: -1,
                    data: "action",
                    orderable: false,
                    className: "text-end d-flex",
                    render: function (data, type, row) {
                        const editButton = `
                            <div class="form-check form-check-custom form-check-solid form-switch form-switch-sm custom pe-auto me-2">
                                <input class="form-check-input" role="button" name="activate" type="checkbox" ${
                                    row.user.status == "active" ? "checked" : ""
                                } data-id="${data}" data-kt-employees-table-filter="inactive_row" data-bs-toggle="tooltip" data-bs-placement="top" title="Activate/ Inactivate Employee">
                            </div>
                            <button class="edit-employee btn btn-icon btn-bg-light btn-active-color-success btn-sm me-1" data-bs-toggle="modal" data-kt-employees-table-filter="edit_row"
                            data-bs-target="#kt_modal_edit_employee" data-id="${data}">
                                <i class="ki-duotone ki-pencil fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </button>`;

                        return `${row.editPermission ? editButton : ""}`;
                    },
                },
            ],
        });

        datatable.on("draw", function () {
            $('[data-bs-toggle="tooltip"]').tooltip();
        });

        table = datatable.$;

        window.datatable = datatable;

        datatable.on("draw", function () {
            initToggleToolbar();
            toggleToolbars();
            handleActivateRows();
            KTMenu.createInstances();
        });
    };

    let handleSearchDatatable = function () {
        const filterSearch = document.querySelector(
            '[data-kt-employee-table-filter="search"]'
        );
        filterSearch.addEventListener("keyup", function (e) {
            clearTimeout(searchTimeout);

            searchTimeout = setTimeout(() => {
                datatable.search(e.target.value).draw();
            }, 700);
        });
    };

    // Filter Datatable
    let handleFilterDatatable = () => {
        const filterSubmit = filterForm.querySelector(
            '[data-kt-employee-table-filter="filter"]'
        );

        // Filter datatable on submit
        filterSubmit.addEventListener("click", function () {
            status = statusElement.checked ? "active" : "inactive";

            datatable.draw();
        });
    };

    // Reset Filter
    let handleResetForm = () => {
        // Select reset button
        const resetButton = document.querySelector(
            '[data-kt-employee-table-filter="reset"]'
        );

        const filterSelect = document
            .querySelector('[data-kt-employee-table-filter="form"]')
            .querySelectorAll("select");

        // Reset datatable
        resetButton.addEventListener("click", function () {
            filterSelect.forEach((e) => {
                $(e).val("").trigger("change");
            });

            status = "active";
            statusElement.checked = status == "active";

            datatable.column(5).search(status).draw();
        });
    };

    // Activate/Inactivate employees
    let handleActivateRows = () => {
        const activateButtons = document.querySelectorAll(
            '[data-kt-employees-table-filter="inactive_row"]'
        );

        let statusLabel = "activate";
        let statusConfirm = "active";
        if (statusElement.checked) {
            statusLabel = "inactivate";
            statusConfirm = "inactive";
        }

        activateButtons.forEach((d) => {
            d.addEventListener("click", function (e) {
                e.preventDefault();

                // Select parent row
                const parent = e.target.closest("tr");
                const employeeId = $(this).data("id");
                const employeeName = parent
                    .querySelectorAll("td .text-gray-800.mb-1")[0]
                    .innerText.trim()
                    .replace(/[a-zA-Z]+\n/, "");

                Swal.fire({
                    html: `Are you sure you want to ${statusLabel} <strong>${employeeName}</strong> ?`,
                    icon: "warning",
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonText: `Yes, ${statusConfirm}!`,
                    cancelButtonText: "Cancel",
                    customClass: {
                        confirmButton: "btn fw-bold btn-danger",
                        cancelButton: "btn fw-bold btn-active-light-primary",
                    },
                }).then(function (result) {
                    if (result.value) {
                        Swal.fire({
                            html: `${statusLabel} employee <b>${employeeName}</b>`,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            allowEnterKey: false,
                            showConfirmButton: false,
                            buttonsStyling: false,
                            timerProgressBar: true,
                            didOpen: () => {
                                Swal.showLoading();

                                const errorSwal = {
                                    showConfirmButton: true,
                                    timer: undefined,
                                    timerProgressBar: false,
                                    confirmButtonText: "Close",
                                    customClass: {
                                        confirmButton:
                                            "btn fw-bold btn-primary",
                                    },
                                };

                                //fetch to delete data
                                $.ajax({
                                    url: `${apiPath}/employees/${employeeId}`,
                                    type: "POST",
                                    cache: false,
                                    headers: {
                                        "X-HTTP-Method-Override": "PUT",
                                    },
                                    data: {
                                        _token: token,
                                        status: statusConfirm,
                                    },
                                })
                                    .done((response) => {
                                        Swal.fire(
                                            Object.assign(
                                                {
                                                    text: response.metadata.message,
                                                    icon: response.metadata.status,
                                                    buttonsStyling: false,
                                                    showConfirmButton: false,
                                                    timer: 1000,
                                                    timerProgressBar: true,
                                                },
                                                response.metadata.status == "error"
                                                    ? errorSwal
                                                    : {}
                                            )
                                        ).then(() => {
                                            datatable.draw();
                                        });
                                    })
                                    .fail((jqXHR, textStatus, error) => {
                                        Swal.fire(
                                            Object.assign(
                                                {
                                                    text: `Failed ${statusLabel} ${employeeName}!. ${jqXHR.responseJSON.message}`,
                                                    icon: "error",
                                                },
                                                errorSwal
                                            )
                                        );
                                    });
                            },
                        });
                    } else if (result.dismiss === "cancel") {
                        Swal.fire({
                            text: `Failed ${statusConfirm} ${employeeName}`,
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: {
                                confirmButton: "btn fw-bold btn-primary",
                            },
                        });
                    }
                });
            });
        });
    };

    // Init toggle toolbar
    let initToggleToolbar = function () {
        // Select all checkboxes
        const container = document.querySelector("#kt_table_employees");
        const checkboxes = container.querySelectorAll(
            '.multi-checkbox[type="checkbox"]'
        );

        // Select elements
        const deleteSelected = document.querySelector(
            '[data-kt-employee-table-select="delete_selected"]'
        );

        // Toggle delete selected toolbar
        let selectedEmployeeId = [];
        let allEmployeeId = [];
        const rowCount = datatable.rows().count();
        checkboxes.forEach((check, key) => {
            // get all employees id
            if (key != 0) allEmployeeId.push(check.value);

            // Checkbox on click event
            check.addEventListener("click", function () {
                if (check.value == "all") {
                    check.checked
                        ? (selectedEmployeeId = allEmployeeId)
                        : (selectedEmployeeId = []);
                } else {
                    if (check.checked) {
                        selectedEmployeeId.push(check.value);
                    } else {
                        let indexToRemove = selectedEmployeeId.indexOf(
                            check.value
                        );
                        if (indexToRemove != -1) {
                            selectedEmployeeId.splice(indexToRemove, 1);
                        }
                    }

                    if (checkboxes.length != rowCount && checkboxes[0].checked)
                        checkboxes[0].checked = false;
                }

                setTimeout(function () {
                    toggleToolbars();
                }, 50);
            });
        });

        // Deleted selected rows
        deleteSelected.addEventListener("click", function () {
            Swal.fire({
                text: "Are you sure you want to delete selected employees?",
                icon: "warning",
                showCancelButton: true,
                buttonsStyling: false,
                showLoaderOnConfirm: true,
                confirmButtonText: "Yes, delete!",
                cancelButtonText: "No, cancel",
                customClass: {
                    confirmButton: "btn fw-bold btn-danger",
                    cancelButton: "btn fw-bold btn-active-light-primary",
                },
            }).then(function (result) {
                if (result.value) {
                    // Simulate delete request -- for demo purpose only
                    const errorSwal = {
                        showConfirmButton: true,
                        timer: undefined,
                        timerProgressBar: false,
                        confirmButtonText: "Close",
                        customClass: {
                            confirmButton: "btn fw-bold btn-primary",
                        },
                    };

                    //fetch to delete data
                    $.ajax({
                        url: `${apiPath}/employees/selected`,
                        type: "DELETE",
                        cache: false,
                        data: {
                            employees: selectedEmployeeId,
                            _token: token,
                        },
                    })
                        .done((response) => {
                            Swal.fire(
                                Object.assign(
                                    {
                                        text: response.message,
                                        icon: response.status,
                                        buttonsStyling: false,
                                        showConfirmButton: false,
                                        timer: 1000,
                                        timerProgressBar: true,
                                    },
                                    response.status == "error" ? errorSwal : {}
                                )
                            ).then(() => {
                                checkboxes[0].checked = false;

                                // delete row data from server and re-draw datatable
                                datatable.draw();
                            });
                        })
                        .fail((jqXHR, textStatus, error) => {
                            Swal.fire(
                                Object.assign(
                                    {
                                        text:
                                            "Failed delete selected employees!. " +
                                            jqXHR.responseJSON.message,
                                        icon: "error",
                                    },
                                    errorSwal
                                )
                            );
                        });
                } else if (result.dismiss === "cancel") {
                    Swal.fire({
                        text: "Selected employees was not deleted.",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn fw-bold btn-primary",
                        },
                    });
                }
            });
        });
    };

    // Toggle toolbars
    let toggleToolbars = function () {
        const container = document.querySelector("#kt_table_employees");
        const toolbarBase = document.querySelector(
            '[data-kt-employee-table-toolbar="base"]'
        );
        const toolbarSelected = document.querySelector(
            '[data-kt-employee-table-toolbar="selected"]'
        );
        const selectedCount = document.querySelector(
            '[data-kt-employee-table-select="selected_count"]'
        );

        // Select refreshed checkbox DOM elements
        const allCheckboxes = container.querySelectorAll(
            'tbody .multi-checkbox[type="checkbox"]'
        );

        // Detect checkboxes state & count
        let checkedState = false;
        let count = 0;

        // Count checked boxes
        allCheckboxes.forEach((checkbox) => {
            if (checkbox.checked) {
                checkedState = true;
                count++;
            }
        });

        // Toggle toolbars
        if (checkedState) {
            selectedCount.innerHTML = count;
            toolbarBase.classList.add("d-none");
            toolbarSelected.classList.remove("d-none");
        } else {
            toolbarBase.classList.remove("d-none");
            toolbarSelected.classList.add("d-none");
        }
    };

    let initFlatpickerLocale = () => {
        flatpickr.localize(flatpickr.l10ns.id);
    };

    // Public methods
    return {
        init: function () {
            initDatatable();
            handleSearchDatatable();
            initToggleToolbar();
            handleFilterDatatable();
            handleResetForm();
            handleActivateRows();
            initFlatpickerLocale();
        },
    };
})();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTDatatablesServerSide.init();
});
