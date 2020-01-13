
function jsFunction(e, value)
{
    let domain = e.getAttribute('data-domain-name');
    console.log(e);
    $.ajax({
        url: '/api/api/redirect',
        type: 'POST',
        data: {
            domain:domain,
            value:value
        },
        success: function(res){
            console.log(res);
            window.open(res, "_blank");
        },
        error: function(){
            alert('Error!');
        }
    });
}

function sizer(value) {

    $.ajax({
        url: '/domain/site/index',
        type: 'GET',
        data: {
            value:value
        },
        success: function(res){
            $.pjax.reload({container:"#sitePjax"});
            console.log(res);
        },
        error: function(){
            $.pjax.reload({container:"#sitePjax"});
            alert('Error!');
        }
    });
}

$(document).ready(function(){
    $('.audit').on('click', function(){
        let keys = $('#grid').yiiGridView('getSelectedRows');
        $.ajax({
            url: '/api/api/audit',
            type: 'POST',
            data: {
                keys:keys
            },
            success: function(res){
                $.pjax.reload({container:"#sitePjax"});
                console.log(res);
                alert('Сайты добавлены в очередь на аудит.');
            },
            error: function(){
                $.pjax.reload({container:"#sitePjax"});
                alert('Error!');
            }
        });
    });
});

$(document).ready(function() {

    $(".my-img").mouseover(function(event) {
        let y =  event.pageY - 100;
        let img = $(this);
        let src = img.attr('src');
        $("body").append("<div class='popup' style='position:absolute; left:-27%; top:"+y+"px'><img src='"+src+"' class='popup_img' /></div>");
        $(".popup").fadeIn(100);
        $(".popup").mouseout(function() {
            $(".popup").fadeOut(100);
            setTimeout(function() { $(".popup").remove(); }, 100);
        });
    });
});

$(document).ready(function() {
    //$(".graphic" ).hide();
    $(".target").mouseover(function(event) {
        // $.ajax({
        //     url: '/api/api/chart',
        //     type: 'POST',
        //     data: {
        //         event:target
        //     },
        //     dataType: "json",
        //     success: function(res){
        //         console.log(res);
        //
        //     },
        //     error: function(){
        //       console.log('error');
        //     }
        // });
    });
    $(".target").mouseout(function(event) {
        //$(".graphic" ).hide();
    });
});

function darwChart() {
    let chart = new Highcharts.chart('container', {
        chart: {
            type: 'spline',
            scrollablePlotArea: {
                width: 250,
                height: 250,
                scrollPositionX: 1
            }
        },
        title: { text: 'Размер' },
        xAxis: { type: 'date', labels: { overflow: 'justify' } },
        yAxis: { title: { text: 'Number of Employees' } },
        legend: { layout: 'vertical', align: 'right', verticalAlign: 'middle' },
        plotOptions: {
            series: {
                label: { connectorAllowed: false },
                pointStart: 2010
            },
        },
        series: [{
            name: 'Размер',
            data: [43934, 52503, 57177, 69658, 97031, 119931]
        }],
    });
}

$('.indexing').on('click', function(){
    let keys = $('#grid').yiiGridView('getSelectedRows');
    $.ajax({
        url: '/api/api/indexing',
        type: 'POST',
        data: {
            keys:keys
        },
        success: function(res){
            $.pjax.reload({container:"#sitePjax"});
            alert('Индексация проведена');
        },
        error: function(){
            $.pjax.reload({container:"#sitePjax"});
            alert('Error!');
        }
    });
});

$('.comment').on('click', function(){
    let site_id = $(this).data("id");
    $("#exampleModal").attr("data-site-id", site_id);
});

$('#commentAjax').on('click', function(){
    let comment = document.getElementById('comments-comment').value;
    let destination_id = document.getElementById('comments-destination_id').value;
    let site_id = document.getElementById('exampleModal').getAttribute("data-site-id");

    $.ajax({
        url: '/api/api/comment',
        type: 'POST',
        data: {
            comment:comment,
            destination_id:destination_id,
            site_id:site_id
        },
        success: function(res){
            console.log(res);
            },
        error: function(){
            alert('Error!');
        }
    });
});