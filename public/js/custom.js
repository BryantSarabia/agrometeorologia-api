'use strict';


var parent;
var project_id;
const url = "127.0.0.1:8000";
var api_key;

/* Bootstrap and jQuery datepicker */

$(function () {
    $('[data-toggle="tooltip"]').tooltip()
})
//
// $(function () {
//     $(".datepicker").datepicker();
//     $(".datepicker").datepicker("option", "showAnim", "blind");
//     $(".datepicker").datepicker("option", "dateFormat", "yy-mm-dd");
// });

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

/* ---------------- RIMUOVERE UN PROGETTO --------------*/

$(document).on('click', '.project_delete', function (event) {
    event.preventDefault();
    parent = $(this).parents('div.project');
    project_id = $(this).data('id');
    $('#deleteProjectModal').modal('show');

});

$('#deleteProject').on('submit', function (event) {
    event.preventDefault();

    /**
     * Logica eliminazione progetto */

    $.ajax({
        type: "DELETE",
        url: `/projects/${project_id}/delete`,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType: "json",
        success: function (response) {
            $('#deleteProjectModal').modal('hide');
            parent.fadeOut("slow", function () {
                parent.remove()
            })
        },
        error: function (error) {
            console.log(error);
        }
    });


})

/* Generate && Refresh API KEY */

var target;
var generate;
var button;
var use;
$(document).on('click', '.generate,.refresh', function (event) {
    button = $(this);
    target = $(this).parent().siblings('.key_field').find('input');
    project_id = $(this).data('id');
    use = $(this).parent().siblings('.key_field').find('.use_key');
    generate = true; /* Boolean per sapere se sto generando un nuovo token */
    $('#confirmPasswordModal').modal('show');
})

$('#confirmPassword').on('submit', function (event) {
    event.preventDefault();
    let password = $('input[type="password"]');
    /* Logica refresh token */

    $.ajax({
        type: "POST",
        url: `/projects/${project_id}/token`,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            'password': password.val(),
            'generate': generate /* Boolean che permette di sapere se deve generare un nuovo token o prendere il token del progetto */
        },
        success: function (response) {
            $('#confirmPasswordModal').modal('hide');
            if (password.hasClass('is-invalid')) {
                password.removeClass('is-invalid');
                $('.invalid-feedback').empty();
            }
            /* Metto la api key nel campo text */
            target.val(response.api_key);
            /* Se il bottone ha la classe "generate" allora la rimuovo e aggiungo la classe "hide_key" */
            if (button.hasClass('generate')) {
                button.removeClass('generate').addClass('hide_key');
                button.text('Hide');
                button.parent().after(
                    `<div class="col-2">
                        <button class="btn btn-warning refresh" name="generate" type="button" data-id="${project_id}">
                            Refresh
                        </button>
                    </div>
                    `
                )
            } /* Se il bottone ha la classe "show_key" allora la rimuovo e aggiungo la classe "hide_key" */
            else if (button.hasClass('show_key')) {
                button.removeClass('show_key').addClass('hide_key')
                button.text('Hide')
            } /* Se il bottone ha la classe refresh prendo il fratello a sinistra, gli rimuovo la classe show_key e aggiungo la classe hide, cambio anche il testo del bottone */
            else if (button.hasClass('refresh')) {
                let sibling = button.parents().prev().eq(0).find('button');
                sibling.removeClass('show_key').addClass('hide_key');
                sibling.text('Hide');
            }
            use.removeClass('d-none')
            password.val(''); /* Risetto la password del modal per sicurezza */

        },
        error: function (error) {
            console.log(error);
            if (password.hasClass('is-invalid')) {
                password.parent().children('span').remove();
            }
            password.addClass('is-invalid');
            password.parent().append(
                `
                <span class="invalid-feedback" role="alert">
                    <strong>${error.responseJSON.message}</strong>
                </span>
                `
            )
        }
    });

})


/* Show and Hide Api Key */

$(document).on('click', '.show_key', function (event) {
    button = $(this);
    target = $(this).parent().siblings('.key_field').find('input');
    project_id = $(this).data('id');
    use = $(this).parent().siblings('.key_field').find('.use_key');
    generate = false; /* Boolean a falso per sapere che devo prendere il token di un progetto */
    $('#confirmPasswordModal').modal('show');
})

$(document).on('click', '.hide_key', function (event) {
    button = $(this);
    target = $(this).parent().siblings('.key_field').find('input').val('');
    button.text('Show');
    button.removeClass('hide_key').addClass('show_key');
    use = $(this).parent().siblings('.key_field').find('.use_key');
    use.addClass('d-none')
    generate = false; /* Boolean a falso per sapere che devo prendere il token di un progetto */
})

$(document).on('click', '.use_key', function () {
    let token = $(this).parent().siblings().eq(0).val();

    if (token.length > 0) {
        localStorage.setItem('api_key', token);
        Toast.fire({
            icon: 'success',
            title: 'API Key assigned succesfully'
        })
    }
})

// /* Pest reports */
//
// $(document).on('submit', '#pest-report', function (event) {
//     event.preventDefault();
//     let form = $(this);
//     if (!validateFormReport(form)) {
//         return false;
//     }
//     let data = formSerialize(form);
//
//     if (marker === null) {
//         Toast.fire({
//             icon: "warning",
//             title: "Missing coordinates"
//         })
//         return false;
//     }
//
//     let coordinates = marker.getPosition().toJSON();
//     data.coordinates = {
//         lat: coordinates.lat,
//         lon: coordinates.lng
//     };
//     $.ajax({
//         url: '/api/v1/pests/reports',
//         headers: {
//             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
//             'Authorization': 'Bearer ' + localStorage.getItem('api_key')
//         },
//         type: 'POST',
//         data: JSON.stringify(data),
//         contentType: 'application/json; charset=utf-8',
//         dataType: 'json',
//         success: function () {
//             Toast.fire({
//                 icon: 'success',
//                 title: 'Report created'
//             })
//              window.location.replace(window.location.origin + "/reports")
//         },
//         error: function (msg) {
//             Toast.fire({
//                 icon: "warning",
//                 title: msg.responseJSON.title,
//                 text: msg.responseJSON.details,
//             })
//         }
//     });
// });
//
//
// $(document).on('submit', '#get-reports', function (event) {
//     event.preventDefault();
//     let form = $(this);
//     let data = formSerialize(form);
//     if (marker === null) {
//         Toast.fire({
//             icon: "warning",
//             title: "Missing coordinates"
//         })
//         return false;
//     }
//     let coordinates = marker.getPosition().toJSON();
//
//     $.ajax({
//         url: `/api/v1/pests/reports?lat=${coordinates.lat}&lon=${coordinates.lng}&radius=${data.radius}&from=${data.from}&to=${data.to}`,
//         headers: {
//             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
//             'Authorization': 'Bearer ' + localStorage.getItem('api_key')
//         },
//         type: 'GET',
//         dataType: 'json',
//         success: function (data) {
//             deleteMarkers()
//             $.each(data.data, function (index, value) {
//                 addMarkerWithTimeOut(value, map, addReportMarker,index * 400);
//             })
//         },
//         error: function (msg) {
//             Toast.fire({
//                 icon: "warning",
//                 title: msg.responseJSON.title,
//                 text: msg.responseJSON.details,
//             })
//         }
//     });
// });
//
// /* Location save */
//
// $(document).on('submit', '#save-location', function(event){
//     event.preventDefault();
//     let form = $(this);
//     let data = formSerialize(form);
//     if (marker === null) {
//         Toast.fire({
//             icon: "warning",
//             title: "Missing coordinates"
//         })
//         return false;
//     }
//     let coordinates = marker.getPosition().toJSON();
//     data.coordinates = {
//         lat: coordinates.lat,
//         lon: coordinates.lng
//     };
//
//     $.ajax({
//         url: "/api/v1/me/locations",
//         headers: {
//             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
//             'Authorization': 'Bearer ' + localStorage.getItem('api_key')
//         },
//         method: "POST",
//         dataType: "json",
//         contentType: "application/json; charset=utf-8",
//         data: JSON.stringify(data),
//         success: function (data) {
//             Toast.fire({
//                 icon: "success",
//                 title: "Location saved"
//             })
//             addLocationMarker(data, map)
//         },
//         error: function(err){
//             Toast.fire({
//                 icon: "warning",
//                 title: err.responseJSON.title,
//                 text: err.responseJSON.details,
//             })
//         }
//     })
// });
//
// $(document).on("click", ".delete-location", function(event){
//     event.preventDefault();
//     let id = $(this).data("id");
//     $.ajax({
//         url: "/api/v1/me/locations/" + id,
//         method: "DELETE",
//         headers: {
//             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
//             'Authorization': 'Bearer ' + localStorage.getItem('api_key')
//         },
//         dataType: "json",
//         success: function(data){
//             Toast.fire({
//                 icon: "success",
//                 title: "Location deleted"
//             })
//             deleteLocations();
//             deleteCircles();
//             initLocations(map);
//         },
//         error: function(err){
//             Toast.fire({
//                 icon: "warning",
//                 title: err.responseJSON.title,
//                 text: err.responseJSON.details,
//             })
//         }
//     })
// })
//
//
// $(document).on("click", "#hide-locations", function(event){
//     event.preventDefault();
//    hideLocations();
//    hideCircles();
//    $(this).attr("id", "show-locations");
//    $(this).text("Show locations");
// })
//
// $(document).on("click", "#show-locations", function(event){
//     event.preventDefault();
//     showLocations();
//     showCircles();
//     $(this).attr("id", "hide-locations");
//     $(this).text("Hide locations");})
//
// $(document).on("click", "#delete-locations", function(event){
//     event.preventDefault();
//
//      let xhr =  $.ajax({
//             url: "/api/v1/me/locations",
//             method: "DELETE",
//             headers: {
//                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
//                 'Authorization': 'Bearer ' + localStorage.getItem('api_key')
//             },
//             dataType: "json",
//             success: function(data){
//                 Toast.fire({
//                     icon: "success",
//                     title: "Locations deleted"
//                 })
//                 deleteLocations();
//                 deleteCircles();
//             },
//             error: function(err){
//                 Toast.fire({
//                     icon: "warning",
//                     title: err.responseJSON.title,
//                     text: err.responseJSON.details,
//                 })
//             }
//         })
//
// })
//
//
//
// /* Range utilizzato per settare il raggio */
// var isDragging = false;
// $('#radius')
//     .mousedown(function () {
//
//         isDragging = true;
//     })
//     .mousemove(function () {
//         if (isDragging) {
//             $(this).siblings().eq(0).text($(this).val() + " km");
//             setCircleRadius($(this).val() * 1000);
//
//         }
//     })
//     .mouseup(function () {
//         isDragging = false;
//         $(this).siblings().eq(0).text($(this).val() + " km");
//         setCircleRadius($(this).val() * 1000);
//
//     });
//
/* Funzione per creare un oggetto JSON a partire dal form */
function formSerialize(data) {
    let form = data.serializeArray();
    let formObject = {};
    $.each(form,
        function (i, v) {
            formObject[v.name] = v.value;
        });
    return formObject;
}

/* Valida che i campi del formulario non siano nulli */
function validateFormReport(data) {
    let form = data.serializeArray();
    let validated = true;
    $.each(form,
        function (i, v) {
            if (v.value === "") {
                let x = document.getElementsByName(v.name);
                if ($(x).hasClass('is-invalid')) {
                    $(x).parent().children('span').remove();
                }
                $(x).addClass('is-invalid');
                $(x).parent().append(
                    `
                <span class="invalid-feedback" role="alert">
                    <strong>${capitalizeFirstLetter(v.name) + " cannot be empty"}</strong>
                </span>
                `
                )
                validated = false;
            }
        });
    return validated;
}

function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

