$(document).ready(function () {

    $.ajax({
        method: "GET",
        headers: {
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            }
        },
        url: "/dashboard/analytics",
        dataType: "json",
        success: function (data) {
            let total_keys = [];
            let total_values = [];
            for (var k in data.total_usage) {
                total_keys.push(k);
                total_values.push(data.total_usage[k]);
            }
            let ctx1 = $('#apiUsage');
            inizialiteChart(ctx1, "Usage", "line","Total Usage", true, total_keys, total_values);

            let projects_names = [];
            let projects_usages = [];
            for (var k in data.projects) {
                projects_names.push(data.projects[k].name);
                projects_usages.push(data.projects[k].total);
            }
            let ctx2 = $('#projectUsage');
            inizialiteChart(ctx2, "Projects Usage", "bar","", false, projects_names, projects_usages);

            let users_names = [];
            let users_usages = [];
            for (var k in data.users){
                users_names.push(data.users[k].email);
                users_usages.push(data.users[k].total);
            }
            let ctx3 = $('#userUsage');
            inizialiteChart(ctx3, "Users Usage", "bar", "", false, users_names, users_usages);

            let endpoints_names = [];
            let endpoints_usages = [];
            for(var k in data.endpoints){
                endpoints_names.push(k);
                endpoints_usages.push(data.endpoints[k])
            }
            let ctx4 = $('#endpointUsage');
            inizialiteChart(ctx4,"Endpoint Usage", "bar","", false, endpoints_names, endpoints_usages);
        }
    });

    function inizialiteChart(ctx, label, type, title, display, labels, data) {
        let myChart = new Chart(ctx, {
            type: type,
            data: {
                labels: labels.length > 0 ? labels : [''],
                datasets: [{
                    label: label,
                    data: data.length > 0 ? data : [0],
                    backgroundColor: '#6666ff',
                    borderColor: '#7386D5',
                    borderWidth: 2,
                    fill: false
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }],
                    xAxes: [{
                        ticks: {
                            display: display
                        }
                    }]
                },
                responsive: true,
                maintainAspectRatio: false,
                title: {
                    display: true,
                    text: title
                }
            }
        });
    }

})
;
