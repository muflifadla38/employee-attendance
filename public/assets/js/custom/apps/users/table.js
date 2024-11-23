"use strict";

const token = $("meta[name='csrf-token']").attr("content");
let datatable;

let KTDatatablesServerSide = (function () {
    const statusElement = document.querySelector('[name="status-filter"]');

    let status = "active";

    let initDatatable = function () {
        datatable = $("#kt_table_users").DataTable({
            searchDelay: 500,
            processing: true,
            serverSide: true,
            order: [[4, "desc"]],
            select: {
                style: "multi",
                selector: 'td:first-child input[type="checkbox"]',
                className: "row-selected",
            },
            ajax: {
                url: $("#table-url").val(),
                data: function (data) {
                    data.status = status;
                },
            },
            columns: [
                { data: "id" },
                { data: "name" },
                { data: "email" },
                { data: "roles.name", name: "roles.name", orderable: false },
                { data: "status", orderable: false },
                { data: "created_at" },
                { data: "action", orderable: false, searchable: false },
            ],
            columnDefs: [
                {
                    targets: 0,
                    orderable: false,
                    render: function (data) {
                        return `
                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" value="${data}" />
                            </div>`;
                    },
                },
                {
                    targets: 1,
                    className: "d-flex align-items-center",
                    render: function (data, type, row) {
                        const profile = row.image
                            ? `<div class="symbol symbol-50px symbol-circle">
                                    <div class="image symbol-label" style="background-image:url('storage/${row.image}')"></div>
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
                        <div class="d-flex flex-column">
                            <span class="text-gray-800 mb-1">${data}</span>
                        </div>`;
                    },
                },
                {
                    targets: 3,
                    render: function (data, type, row) {
                        return `<span class="badge badge-light-${colorType(
                            data
                        )} fs-7 fw-bold text-capitalize">${data}</span>`;
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
                    targets: 5,
                    render: function (data, type, row) {
                        return moment(data).format("D MMMM YYYY");
                    },
                },
                {
                    targets: -1,
                    data: "action",
                    orderable: false,
                    className: "text-end",
                    render: function (data, type, row) {
                        const editButton = `
                            <button class="btn btn-icon btn-bg-light btn-active-color-success btn-sm me-1" data-bs-toggle="modal" data-kt-users-table-filter="edit_row"
                            data-bs-target="#kt_modal_edit_user" data-id="${data}">
                                <i class="ki-duotone ki-pencil fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </button>`;

                        const deleteButton = `
                        <button class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm"
                            data-kt-users-table-filter="delete_row" data-id="${data}">
                            <i class="ki-duotone ki-trash fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                                <span class="path5"></span>
                            </i>
                        </button>`;

                        return `
                            ${row.editPermission ? editButton : ""}
                            ${row.deletePermission ? deleteButton : ""}`;
                    },
                },
            ],
        });

        datatable.on("draw", function () {
            initToggleToolbar();
            toggleToolbars();
            handleDeleteRows();
            KTMenu.createInstances();
        });
    };

    let handleSearchDatatable = function () {
        let searchTimeout;

        const filterSearch = document.querySelector(
            '[data-kt-user-table-filter="search"]'
        );

        filterSearch.addEventListener("keyup", function (e) {
            clearTimeout(searchTimeout);

            searchTimeout = setTimeout(() => {
                datatable.search(e.target.value).draw();
            }, 700);
        });
    };

    let handleFilterDatatable = () => {
        const filterRole = document.querySelector(
            '[data-kt-user-table-filter="form"]'
        );

        const filterButton = filterRole.querySelector(
                '[data-kt-user-table-filter="filter"]'
            ),
            filterSelect = filterRole.querySelectorAll("select");

        filterButton.addEventListener("click", function () {
            let roleValue = "";

            filterSelect.forEach((e, n) => {
                e.value &&
                    "" !== e.value &&
                    (0 !== n && (roleValue += " "), (roleValue += e.value));
            });

            status = statusElement.checked ? "active" : "inactive";

            datatable.draw();
        });
    };

    let handleDeleteRows = () => {
        const deleteButtons = document.querySelectorAll(
            '[data-kt-users-table-filter="delete_row"]'
        );

        deleteButtons.forEach((d) => {
            d.addEventListener("click", function (e) {
                e.preventDefault();

                const parent = e.target.closest("tr");

                const userId = $(this).data("id");
                const email = parent
                    .querySelectorAll("td")[1]
                    .innerText.trim()
                    .replace(/[a-zA-Z]+\n/, "");

                Swal.fire({
                    text: "Are you sure you want to delete " + email + "?",
                    icon: "warning",
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonText: "Yes, delete!",
                    cancelButtonText: "No, cancel",
                    customClass: {
                        confirmButton: "btn fw-bold btn-danger",
                        cancelButton: "btn fw-bold btn-active-light-primary",
                    },
                }).then(function (result) {
                    if (result.value) {
                        Swal.fire({
                            text: "Deleting " + email,
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
                                    url: `${apiPath}/users/${userId}`,
                                    type: "DELETE",
                                    cache: false,
                                    data: {
                                        _token: token,
                                    },
                                })
                                    .done((response) => {
                                        Swal.fire(
                                            Object.assign(
                                                {
                                                    text: response.metadata
                                                        .message,
                                                    icon: response.metadata
                                                        .status,
                                                    buttonsStyling: false,
                                                    showConfirmButton: false,
                                                    timer: 1000,
                                                    timerProgressBar: true,
                                                },
                                                response.metadata.status ==
                                                    "error"
                                                    ? errorSwal
                                                    : {}
                                            )
                                        ).then(() => {
                                            // delete row data from server and re-draw datatable
                                            datatable.draw();
                                        });
                                    })
                                    .fail((xhr, textStatus, error) => {
                                        let message = error;
                                        if (xhr?.responseJSON) {
                                            message = xhr.responseJSON?.metadata
                                                ? xhr.responseJSON.metadata
                                                      .message
                                                : xhr.responseJSON?.message;
                                        }

                                        Swal.fire(
                                            Object.assign(
                                                {
                                                    text:
                                                        "Failed delete " +
                                                        email +
                                                        "!. " +
                                                        message,
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
                            text: email + " was not deleted.",
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

    let handleResetForm = () => {
        const resetButton = document.querySelector(
            '[data-kt-user-table-filter="reset"]'
        );

        const filterSelect = document
            .querySelector('[data-kt-user-table-filter="form"]')
            .querySelectorAll("select");

        resetButton.addEventListener("click", function () {
            status = "active";
            statusElement.checked = true;

            filterSelect.forEach((e) => {
                $(e).val("").trigger("change");
            });

            datatable.column(3).search("").draw();
        });
    };

    let initToggleToolbar = function () {
        const container = document.querySelector("#kt_table_users");
        const checkboxes = container.querySelectorAll('[type="checkbox"]');

        const deleteSelected = document.querySelector(
            '[data-kt-user-table-select="delete_selected"]'
        );

        let selectedUserId = [];
        let allUserId = [];
        const rowCount = datatable.rows().count();
        checkboxes.forEach((checkbox, key) => {
            if (key != 0) allUserId.push(checkbox.value);

            // Checkbox on click event
            checkbox.addEventListener("click", function () {
                if (checkbox.value == "all") {
                    checkbox.checked
                        ? (selectedUserId = allUserId)
                        : (selectedUserId = []);
                } else {
                    if (checkbox.checked) {
                        selectedUserId.push(checkbox.value);
                    } else {
                        let indexToRemove = selectedUserId.indexOf(
                            checkbox.value
                        );
                        if (indexToRemove != -1) {
                            selectedUserId.splice(indexToRemove, 1);
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

        deleteSelected.addEventListener("click", function () {
            Swal.fire({
                text: "Are you sure you want to delete selected users?",
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
                    const errorSwal = {
                        showConfirmButton: true,
                        timer: undefined,
                        timerProgressBar: false,
                        confirmButtonText: "Close",
                        customClass: {
                            confirmButton: "btn fw-bold btn-primary",
                        },
                    };

                    $.ajax({
                        url: `${apiPath}/users/selected`,
                        type: "DELETE",
                        cache: false,
                        data: {
                            users: selectedUserId,
                            _token: token,
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
                                checkboxes[0].checked = false;

                                datatable.draw();
                            });
                        })
                        .fail((xhr, textStatus, error) => {
                            Swal.fire(
                                Object.assign(
                                    {
                                        text:
                                            "Failed delete selected users!. " +
                                            (xhr?.responseJSON
                                                ? xhr?.responseJSON.metadata
                                                      .message
                                                : error),
                                        icon: "error",
                                    },
                                    errorSwal
                                )
                            );
                        });
                } else if (result.dismiss === "cancel") {
                    Swal.fire({
                        text: "Selected users was not deleted.",
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

    let toggleToolbars = function () {
        const container = document.querySelector("#kt_table_users");
        const toolbarBase = document.querySelector(
            '[data-kt-user-table-toolbar="base"]'
        );
        const toolbarSelected = document.querySelector(
            '[data-kt-user-table-toolbar="selected"]'
        );
        const selectedCount = document.querySelector(
            '[data-kt-user-table-select="selected_count"]'
        );

        const allCheckboxes = container.querySelectorAll(
            'tbody [type="checkbox"]'
        );

        let checkedState = false;
        let count = 0;

        // Count checked boxes
        allCheckboxes.forEach((c) => {
            if (c.checked) {
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

    return {
        init: function () {
            initDatatable();
            handleSearchDatatable();
            initToggleToolbar();
            handleFilterDatatable();
            handleDeleteRows();
            handleResetForm();
        },
    };
})();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTDatatablesServerSide.init();
});
