window.onload = function () {
    $("form#form-file").submit(function (e) {
        e.preventDefault();
        var formData = new FormData(this);
        requestUtils.doPostFile('process-file', formData, function (resp) {
            Swal.fire({
                title: resp.msg,
                icon: 'success'
            });
        })
    });

    $(".custom-file-input").on("change", function () {
        var fileName = $(this).val().split("\\").pop();
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    });
}

