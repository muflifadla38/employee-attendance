"use strict";

const apiPath = "/api/v1";

const colorType = (type) => {
    let color;

    switch (type) {
        case "inactive":
            color = "danger";
            break;
        case "active":
            color = "success";
            break;
        case "admin":
            color = "warning";
            break;
        case "employee":
            color = "primary";
            break;
        default:
            color = "secondary";
            break;
    }

    return color;
};

const closeModal = (target) => {
    setTimeout(() => {
        $("button.swal2-confirm.btn.btn-primary").attr({
            "data-bs-dismiss": "modal",
            "data-bs-target": target,
        });
    }, 50);
};
