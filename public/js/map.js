let map;
let marker = null;
let markers = [];
let circle = null;
let circles = [];
let locations = [];

function initMap() {
    map = new google.maps.Map(document.getElementById("map"), {
        center: {lat: 42.3600768, lng: 13.3738677},
        zoom: 10,
        disableDefaultUI: true,
    });

    if (window.location.pathname === "/reports") {
        initReports(map);
        initLocations(map);
    }

    if (window.location.pathname === "/me/locations") {
        initLocations(map);
    }

    initZoomControl(map);
    initFullscreenControl(map);
    initCurrentPos(map);
    map.addListener('click', (e) => {
        placeMarkerAndPanTo(e.latLng, map)
    });
}

function initCurrentPos(map) {

    infoWindow = new google.maps.InfoWindow();

    const locationButton = document.createElement("button");
    locationButton.textContent = "Pan to Current Location";
    locationButton.classList.add("custom-map-control-button");
    map.controls[google.maps.ControlPosition.TOP_CENTER].push(locationButton);
    locationButton.addEventListener("click", (event) => {
        event.preventDefault();
        // Try HTML5 geolocation.
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const pos = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude,
                    };

                    infoWindow.open(map);
                    map.setCenter(pos);
                    placeMarkerAndPanTo(pos, map);

                },
                () => {
                    handleLocationError(true, infoWindow, map.getCenter());
                }
            );
        } else {
            // Browser doesn't support Geolocation
            handleLocationError(false, infoWindow, map.getCenter());
        }
    });
}

function handleLocationError(browserHasGeolocation, infoWindow, pos) {
    infoWindow.setPosition(pos);
    infoWindow.setContent(
        browserHasGeolocation
            ? "Error: The Geolocation service failed."
            : "Error: Your browser doesn't support geolocation."
    );
    infoWindow.open(map);
}

function initZoomControl(map) {
    document.querySelector(".zoom-control-in").onclick = function () {
        map.setZoom(map.getZoom() + 1);
    };

    document.querySelector(".zoom-control-out").onclick = function () {
        map.setZoom(map.getZoom() - 1);
    };
    map.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(
        document.querySelector(".zoom-control")
    );
}

function initFullscreenControl(map) {
    const elementToSendFullscreen = map.getDiv().firstChild;
    const fullscreenControl = document.querySelector(".fullscreen-control");
    map.controls[google.maps.ControlPosition.RIGHT_TOP].push(fullscreenControl);

    fullscreenControl.onclick = function () {
        if (isFullscreen(elementToSendFullscreen)) {
            exitFullscreen();
        } else {
            requestFullscreen(elementToSendFullscreen);
        }
    };

    document.onwebkitfullscreenchange = document.onmsfullscreenchange = document.onmozfullscreenchange = document.onfullscreenchange = function () {
        if (isFullscreen(elementToSendFullscreen)) {
            fullscreenControl.classList.add("is-fullscreen");
        } else {
            fullscreenControl.classList.remove("is-fullscreen");
        }
    };
}

function isFullscreen(element) {
    return (
        (document.fullscreenElement ||
            document.webkitFullscreenElement ||
            document.mozFullScreenElement ||
            document.msFullscreenElement) == element
    );
}

function requestFullscreen(element) {
    if (element.requestFullscreen) {
        element.requestFullscreen();
    } else if (element.webkitRequestFullScreen) {
        element.webkitRequestFullScreen();
    } else if (element.mozRequestFullScreen) {
        element.mozRequestFullScreen();
    } else if (element.msRequestFullScreen) {
        element.msRequestFullScreen();
    }
}

function exitFullscreen() {
    if (document.exitFullscreen) {
        document.exitFullscreen();
    } else if (document.webkitExitFullscreen) {
        document.webkitExitFullscreen();
    } else if (document.mozCancelFullScreen) {
        document.mozCancelFullScreen();
    } else if (document.msExitFullscreen) {
        document.msExitFullscreen();
    }
}

function placeMarkerAndPanTo(latLng, map) {
    if (marker !== null) {
        marker.setMap(null);
    }

    if (circle !== null) {
        circle.setMap(null);
    }
    marker = new google.maps.Marker({
        position: latLng,
        map: map,
        icon: window.location.origin + "/img/icons/location.png",
    });

    if (window.location.pathname !== "/reports/create") {
        circle = new google.maps.Circle({
            strokeColor: "#5bf700",
            strokeOpacity: 0.8,
            strokeWeight: 2,
            fillColor: "#00ae00",
            fillOpacity: 0.20,
            map,
            center: marker.getPosition().toJSON(),
            radius: parseInt($('#radius').val()) * 1000,
        });
    }
    map.panTo(latLng);
    // console.log(marker.getPosition().toJSON());
}

function initReports(map) {
    $.ajax({
        url: "/api/v1/reports",
        method: "GET",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Authorization': 'Bearer ' + localStorage.getItem('api_key')
        },
        dataType: "json",
        success: function (data) {
            $.each(data.data, function (index, value) {
                addMarkerWithTimeOut(value, map, addReportMarker, index * 400)
            })

        },
        error: function (err) {
            console.log(err)
        }
    })
}

function initLocations(map) {

    $.ajax({
        url: "/api/v1/me/locations",
        method: "GET",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Authorization': 'Bearer ' + localStorage.getItem('api_key')
        },
        dataType: "json",
        success: function (data) {
            $.each(data.data, function (index, value) {
                addLocationMarker(value,map);
            });
        },
        error: function (err) {
            Toast.fire({
                icon: "warning",
                title: err.responseJSON.title,
                text: err.responseJSON.details,
            })
        }
    })
}

function addCircle(marker, map, radius = 10000) {
    const circle = new google.maps.Circle({
        strokeColor: "#FF0000",
        strokeOpacity: 0.8,
        strokeWeight: 2,
        fillColor: "#FF0000",
        fillOpacity: 0.20,
        map,
        center: marker.getPosition().toJSON(),
        radius: radius,
    });
    circles.push(circle);
}

function deleteCircles() {
    hideCircles();
    circles = [];
}

function addReportMarker(report, map) {
    const contentString =
        '<div id="content">' +
        '<div id="siteNotice">' +
        "</div>" +
        '<h3 id="firstHeading" class="firstHeading">' + report.name + '</h3>' +
        '<div id="bodyContent">' +
        "<p>" + report.message + "</p>" +
        "<small>(lat: " + report.coordinates.lat + " " + "lon: " + report.coordinates.lon + ")</small>" +
        "<br>" +
        "<small>Created: " + report.created_at + "</small>"
    "</div>" +
    "</div>";
    const infoWindow = new google.maps.InfoWindow({
        content: contentString,
    });
    const marker = new google.maps.Marker({
        position: {lat: report.coordinates.lat, lng: report.coordinates.lon},
        map: map,
        title: report.name,
        animation: google.maps.Animation.DROP,
        icon: window.location.origin + "/img/icons/pest.png"
    });
    markers.push(marker);
    google.maps.event.addListener(marker, "click", () => {
        infoWindow.open(map, marker);
    });
}

function addLocationMarker(location, map) {
    const contentString =
        '<div id="content">' +
        '<div id="siteNotice">' +
        "</div>" +
        '<div id="bodyContent">' +
        "<p>(lat: " + location.coordinates.lat + " " + "lon: " + location.coordinates.lon + ")</p>" +
        "</div>" +
        '<div class="row justify-content-center">' +
        '<div class="col-4">' +
        '<a class="btn btn-sm btn-danger delete-location" data-id="'+ location.id+'" role="button" href="#">Delete</a>'
        '</div>' +
        '</div>' +
        "</div>";
    const infoWindow = new google.maps.InfoWindow({
        content: contentString,
    });
    const marker = new google.maps.Marker({
        position: {lat: location.coordinates.lat, lng: location.coordinates.lon},
        map: map,
        animation: google.maps.Animation.DROP,
        location_id: location.id
    });
    addCircle(marker, map, location.radius * 1000);
    locations.push(marker);
    google.maps.event.addListener(marker, "click", () => {
        infoWindow.open(map, marker);
    });
}

function deleteMarkers() {
    $.each(markers, function (index, value) {
        value.setMap(null);
    });
    markers = [];
}

function deleteLocations(){
    $.each(locations, function (index, value) {
        value.setMap(null);
    });
    locations = [];
}

function addMarkerWithTimeOut(obj, map, func, timeout) {
    window.setTimeout(() => {
        func(obj, map)
    }, timeout);
}

function setCircleRadius(radius) {
    if (circle !== null) {
        circle.setRadius(radius);
    }
}

function showCircles() {
    setMapOnAll(circles, map);
}

function showLocations(){
    setMapOnAll(locations,map);
}

function hideCircles(){
    $.each(circles, function(index,value){
       value.setMap(null)
    });
}

function hideLocations(){
    $.each(locations, function(index,value){
        value.setMap(null)
    });
}

function deleteLocations(){
    hideLocations();
    locations = [];
}

function setMapOnAll(array,map) {
    $.each(array,function(index,value){
        value.setMap(map)
    });
}
