import './bootstrap';
import '../sass/app.scss';

window.deleteFormConfirmation = function (e) {
    e.preventDefault();

    Swal.fire({
        title: "Are you sure want to delete?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#ffed10",
        cancelButtonColor: "#dc3545",

        confirmButtonText: "Yes",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            $(e.target).closest("form").submit();
        }
    });
};

window.resetForm = function (targetForm) {
    $(targetForm)[0].reset();
    $(targetForm).eq(0).find("select").val("").trigger("change");
    $(targetForm).validate().resetForm();
};

window.toggleFilter = function (targetId) {
    if ($(targetId).hasClass("d-none")) {
        if ($(window).width() >= 992) {
            $(targetId).addClass(
                "animate__animated animate__faster animate__fadeIn"
            );

            $(targetId).removeClass("animate__slideInUp");
        } else {
            $(targetId).addClass(
                "animate__animated animate__faster animate__slideInUp"
            );

            $(targetId).removeClass("animate__fadeIn");
        }

        $(targetId).removeClass("d-none");

        $("body").css("overflow-y", "hidden");
    } else {
        $(targetId).addClass("d-none");

        $("body").css("overflow-y", "auto");
    }
};

$(document).ready(function () {
    $(".filter-popup-wraper").click(function (event) {
        if (!$(event.target).closest(".filter-popup-content").length) {
            toggleFilter(this);
        }
    });
});
