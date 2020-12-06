$(document).ready(function () {

    /* Sweet alert " */

    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    })

$(document).on("click",".delete_configuration", function(event){
    event.preventDefault();
   let button = $(this);
   let id = button.data("id");

    $.ajax({
        type: "DELETE",
        url: `/dashboard/configuration/${id}`,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            console.log("deleting" + id);
            console.log(response);
            button.parents().find('tr').hide(300, function(){
                $(this).remove();
            })
            Toast.fire({
                icon: 'success',
                title: 'Deleted'
            })
        },
        error: function (msg) {
            Toast.fire({
                icon: "warning",
                title: msg.responseJSON.title,
                text: msg.responseJSON.details,
            })
        }
    });
});

    $(document).on("click",".change_status", function(event){
        event.preventDefault();
        let button = $(this);
        let id = button.data("id");

        $.ajax({
            type: "PATCH",
            url: `/dashboard/configuration/${id}`,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                console.log("changing " + id);
                console.log(response);
                if(button.prop("checked")){
                    button.prop("checked", false);
                    button.siblings('label').text('Enable');
                } else {
                    button.prop("checked", true);
                    button.siblings('label').text('Disable');
                }
            },
            error: function (msg) {
                Toast.fire({
                    icon: "warning",
                    title: msg.responseJSON.title,
                    text: msg.responseJSON.details,
                })
            }
        });
    });

// $(document).on("change", "#configuration_file", function(event){
//     let button = $('.main-form-button');
//
//     if(!checkExtension('json')){
//         button.prop('disabled',true);
//         if(!button.hasClass('disabled')){
//             button.addClass('disabled');
//         }
//         return false;
//     }
//
//     button.prop('disabled',false);
//     if(button.hasClass('disabled')){
//         button.removeClass('disabled');
//     }
// });
//
// function checkExtension(extension){
//     file_name = $('#configuration_file').val();
//     file_extension = file_name.substring(file_name.lastIndexOf('.') + 1);
//     if(file_extension !== extension){
//         return false;
//     }
//     return true;
// }

});
