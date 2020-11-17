'use strict';

/* Bootstrap and jQuery datepicker */


$(function () {
    $('[data-toggle="tooltip"]').tooltip()
})

$(function () {
    $(".datepicker").datepicker();
    $(".datepicker").datepicker("option", "showAnim", "blind");
    $(".datepicker").datepicker("option", "dateFormat", "yy-mm-dd");
});

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

$(document).on('click', '#login-submit', function(event){
    event.preventDefault();
    let form = $('#login-form');
    if (!validateFormReport(form)) {
        return false;
    }
    let data = formSerialize(form);
    $.ajax({
        method: "POST",
        headers: {
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            }
        },
        url: "/api/v1/login",
        data: JSON.stringify(data),
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        success: function(data){
            localStorage.setItem('token', data.token);
            form.submit();
        },
        error: function(msg){
            Toast.fire({
                icon: "warning",
                title: msg.responseJSON.title,
                text: msg.responseJSON.details,
            })
        }

    })
})

/* Pest reports */

$(document).on('submit', '#pest-report', function (event) {
    event.preventDefault();
    let form = $(this);
    if (!validateFormReport(form)) {
        return false;
    }
    let data = formSerialize(form);

    if (marker === null) {
        Toast.fire({
            icon: "warning",
            title: "Missing coordinates"
        })
        return false;
    }

    let coordinates = marker.getPosition().toJSON();
    data.coordinates = {
        lat: coordinates.lat,
        lon: coordinates.lng
    };

    $.ajax({
        url: '/api/v1/pests/reports',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Authorization': 'Bearer ' + localStorage.getItem('token')
        },
        type: 'POST',
        data: JSON.stringify(data),
        contentType: 'application/json; charset=utf-8',
        dataType: 'json',
        success: function () {
            Toast.fire({
                icon: 'success',
                title: 'Report created'
            })
            window.location.replace(window.location.origin + "/demo/reports")
        },
        error: function (msg) {
            console.log(msg);
            Toast.fire({
                icon: "warning",
                title: msg.responseJSON.title,
                text: msg.responseJSON.details,
            })
        }
    });
});


$(document).on('submit', '#get-reports', function (event) {
    event.preventDefault();
    let form = $(this);
    let data = formSerialize(form);
    if (marker === null) {
        Toast.fire({
            icon: "warning",
            title: "Missing coordinates"
        })
        return false;
    }
    let coordinates = marker.getPosition().toJSON();

    $.ajax({
        url: `/api/v1/pests/reports?lat=${coordinates.lat}&lon=${coordinates.lng}&radius=${data.radius}&from=${data.from}&to=${data.to}`,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Authorization': 'Bearer ' + localStorage.getItem('token')
        },
        type: 'GET',
        dataType: 'json',
        success: function (data) {
            deleteMarkers()
            $.each(data.data, function (index, value) {
                addMarkerWithTimeOut(value, map, addReportMarker,index * 400);
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

/* Location save */

$(document).on('submit', '#save-location', function(event){
    event.preventDefault();
    let form = $(this);
    let data = formSerialize(form);
    if (marker === null) {
        Toast.fire({
            icon: "warning",
            title: "Missing coordinates"
        })
        return false;
    }
    let coordinates = marker.getPosition().toJSON();
    data.coordinates = {
        lat: coordinates.lat,
        lon: coordinates.lng
    };

    $.ajax({
        url: "/api/v1/me/locations",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Authorization': 'Bearer ' + localStorage.getItem('token')
        },
        method: "POST",
        dataType: "json",
        contentType: "application/json; charset=utf-8",
        data: JSON.stringify(data),
        success: function (data) {
            Toast.fire({
                icon: "success",
                title: "Location saved"
            })
            addLocationMarker(data, map)
        },
        error: function(err){
            Toast.fire({
                icon: "warning",
                title: err.responseJSON.title,
                text: err.responseJSON.details,
            })
        }
    })
});

$(document).on("click", ".delete-location", function(event){
    event.preventDefault();
    let id = $(this).data("id");
    $.ajax({
        url: "/api/v1/me/locations/" + id,
        method: "DELETE",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Authorization': 'Bearer ' + localStorage.getItem('token')
        },
        dataType: "json",
        success: function(data){
            Toast.fire({
                icon: "success",
                title: "Location deleted"
            })
            deleteLocations();
            deleteCircles();
            initLocations(map);
        },
        error: function(err){
            Toast.fire({
                icon: "warning",
                title: err.responseJSON.title,
                text: err.responseJSON.details,
            })
        }
    })
})


$(document).on("click", "#hide-locations", function(event){
    event.preventDefault();
    hideLocations();
    hideCircles();
    $(this).attr("id", "show-locations");
    $(this).text("Show locations");
})

$(document).on("click", "#show-locations", function(event){
    event.preventDefault();
    showLocations();
    showCircles();
    $(this).attr("id", "hide-locations");
    $(this).text("Hide locations");})

$(document).on("click", "#delete-locations", function(event){
    event.preventDefault();

    let xhr =  $.ajax({
        url: "/api/v1/me/locations",
        method: "DELETE",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Authorization': 'Bearer ' + localStorage.getItem('token')
        },
        dataType: "json",
        success: function(data){
            Toast.fire({
                icon: "success",
                title: "Locations deleted"
            })
            deleteLocations();
            deleteCircles();
        },
        error: function(err){
            Toast.fire({
                icon: "warning",
                title: err.responseJSON.title,
                text: err.responseJSON.details,
            })
        }
    })

})



/* Range utilizzato per settare il raggio */
var isDragging = false;
$('#radius')
    .mousedown(function () {

        isDragging = true;
    })
    .mousemove(function () {
        if (isDragging) {
            $(this).siblings().eq(0).text($(this).val() + " km");
            setCircleRadius($(this).val() * 1000);

        }
    })
    .mouseup(function () {
        isDragging = false;
        $(this).siblings().eq(0).text($(this).val() + " km");
        setCircleRadius($(this).val() * 1000);

    });

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
