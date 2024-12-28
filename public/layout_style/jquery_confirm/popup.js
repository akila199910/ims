function someThingWrong() {
    $.confirm({
        draggable: false,
        theme: "modern",
        icon: "fas fa-info-circle",
        columnClass: "col-xl-6 col-lg-6 col-md-8 col-sm-12 col-12",
        title: "Error! ",
        content: "Something went wrong. Please try again!",
        type: "blue",
        buttons: {
            confirm: {
                text: "OK",
                btnClass: "btn-blue",
                action: function () {
                    $("#loader").hide();
                },
            },
        },
    });
}

function successPopup(message, url) {
    $.confirm({
        draggable: false,
        theme: "modern",
        icon: "fas fa-check-circle",
        columnClass: "col-xl-6 col-lg-6 col-md-8 col-sm-12 col-12",
        title: "Success! ",
        content: message,
        type: "green",
        buttons: {
            confirm: {
                text: "OK",
                btnClass: "btn-green",
                action: function () {
                    $("#loader").hide();
                    if (url != "") {
                        location.href = "" + url + "";
                    }
                },
            },
        },
    });
}

function errorPopup(message, url) {
    $.confirm({
        draggable: false,
        theme: "modern",
        icon: "fas fa-times-circle",
        columnClass: "col-xl-6 col-lg-6 col-md-8 col-sm-12 col-12",
        title: "Failed! ",
        content: message,
        type: "red",
        buttons: {
            confirm: {
                text: "OK",
                btnClass: "btn-red",
                action: function () {
                    $("#loader").hide();
                    if (url != "") {
                        location.href = "" + url + "";
                    }
                },
            },
        },
    });
}

$(document).ready(function () {});
