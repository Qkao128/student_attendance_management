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
