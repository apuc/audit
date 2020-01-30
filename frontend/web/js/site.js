//редирект
function redirect(e, value) {
    let keys = $('#grid').yiiGridView('getSelectedRows');
    console.log(keys);
    $.ajax({
        url: '/api/api/redirect',
        type: 'POST',
        data: {
            keys: keys,
            value: value,
        },
        success: function (res) {
            res = JSON.parse(res);
            for (let i = 0; i < res.length; i++)
                window.open(res[i], "_blank");
        },
        error: function () {
            alert('Error!');
        }
    });
}

//размер GridView
function sizer(value) {
    $.ajax({
        url: '/domain/site/index',
        type: 'GET',
        data: {
            value: value
        },
        success: function (res) {
            $.pjax.reload({container: "#sitePjax"});
            console.log(res);
        },
        error: function () {
            $.pjax.reload({container: "#sitePjax"});
            alert('Error!');
        }
    });
}

//копирование домена в буфер
function copyToClipboard(containerid) {
    try {
        window.getSelection().removeAllRanges();
    } catch (e) {
        document.selection.empty();
    }
    if (document.selection) {
        var range = document.body.createTextRange();
        range.moveToElementText(document.getElementById(containerid));
        range.select().createTextRange();
        document.execCommand("Copy");
    } else if (window.getSelection) {
        var range = document.createRange();
        range.selectNode(document.getElementById(containerid));
        window.getSelection().addRange(range);
        document.execCommand("Copy");
    }
}

//вывод графика
function darwChart(name, container, data, created_at) {
    return new Highcharts.chart(container, {
        chart: {
            type: 'spline',
            width: 350,
            height: 300,
        },
        title: {text: name},
        xAxis: {categories: created_at},
        yAxis: {title: ''},
        series: [{name: name, data: data}],
    });
}

//график
$(document).ready(function () {
    $(".graphic_size").hide();
    $(".graphic_loading_time").hide();
    $(".graphic_server_response_code").hide();
    $(".target").mouseover(function (event) {
        let target = event.target.getAttribute('class');
        target = target.toString();
        let id = target.replace('glyphicon glyphicon-signal target ', '');
        $.ajax({
            url: '/api/api/chart',
            type: 'POST',
            data: {
                id: id
            },
            success: function (res) {
                res = JSON.parse(res);
                let size = res['size'];
                let loading_time = res['loading_time'];
                let server_response_code = res['server_response_code'];
                let created_at = res['created_at'];
                darwChart('Размер', 'size', size, created_at);
                darwChart('Время загрузки', 'loading_time', loading_time, created_at);
                darwChart('Код ответа сервера', 'server_response_code', server_response_code, created_at);
                $(".graphic_size").show();
                $(".graphic_loading_time").show();
                $(".graphic_server_response_code").show();
            },
            error: function () {
                console.log('Chart Error');
            }
        });
    });
    $(".target").mouseout(function (event) {
        $(".graphic_size").hide();
        $(".graphic_loading_time").hide();
        $(".graphic_server_response_code").hide();
    });
});

//аудит
$(document).ready(function () {
    $('.audit').on('click', function () {
        let keys = $('#grid').yiiGridView('getSelectedRows');
        $.ajax({
            url: '/api/api/audit',
            type: 'POST',
            data: {
                keys: keys
            },
            success: function (res) {
                alert('Сайты добавлены в очередь на аудит.');
            },
            error: function () {
                alert('Error!');
            }
        });
    });
});

//картинки
$(document).ready(function () {

    $(".my-img").mouseover(function (event) {
        let y = event.pageY - 100;
        let img = $(this);
        let src = img.attr('src');
        $("body").append("<div class='popup' style='position:absolute; left:-27%; top:" + y + "px'><img src='" + src + "' class='popup_img' /></div>");
        $(".popup").fadeIn(100);
        $(".popup").mouseout(function () {
            $(".popup").fadeOut(100);
            setTimeout(function () {
                $(".popup").remove();
            }, 100);
        });
    });
});

//индексация
$('.indexing').on('click', function () {
    let keys = $('#grid').yiiGridView('getSelectedRows');
    console.log(keys);
    $.ajax({
        url: '/api/api/indexing',
        type: 'POST',
        data: {
            keys: keys
        },
        success: function (res) {
            alert('Сайты добавлены в очередь на индексацию.');
        },
        error: function () {
            alert('Error!');
        }
    });
});

//модальное окно комментария
$('.comment').on('click', function () {
    let site_id = $(this).data("id");
    $("#exampleModal").attr("data-site-id", site_id);
});

//комментарий
$('#commentAjax').on('click', function () {
    let comment = document.getElementById('comments-comment').value;
    let destination_id = document.getElementById('comments-destination_id').value;
    let site_id = document.getElementById('exampleModal').getAttribute("data-site-id");

    $.ajax({
        url: '/api/api/comment',
        type: 'POST',
        data: {
            comment: comment,
            destination_id: destination_id,
            site_id: site_id
        },
        success: function (res) {
            console.log(res);
        },
        error: function () {
            alert('Error!');
        }
    });
});

// $(function() {
//     $("table").stickyTableHeaders();
// });

$('.theme').on('click', function () {
    let site_id = $(this).data("id");
    let modal = $("#modalTheme");
    let select2 = $('#theme_ids');
    modal.attr("data-site-id", site_id);

    $.ajax({
        url: '/api/api/selected',
        type: 'POST',
        data: {
            id: site_id,
        },
        success: function (res) {
           let value = JSON.parse(res);
            select2.val(value);
            select2.trigger('change')
        },
        error: function () { }
    });
});

$(document).on("click", "#modalThemeButton", function (e) {
    let site_id = document.getElementById('modalTheme').getAttribute("data-site-id");
    let theme_ids = $('#theme_ids').select2('data');
    theme_ids = JSON.stringify(theme_ids);

    $.ajax({
        url: '/api/api/theme',
        type: 'POST',
         data: {
             theme_ids: theme_ids,
             site_id: site_id
         },
        success: function (res) {
            $.pjax.reload({container:"#reload"});
        },
        error: function () {
            $.pjax.reload({container:"#reload"});
        }
    });
});

$('.links').on('click', function () {
    let site_id = $(this).data("id");

    $.ajax({
        url: '/api/api/links',
        type: 'POST',
        data: {
            site_id: site_id
        },
        success: function (res) {
            let value = JSON.parse(res);
            console.log(value);
            document.getElementById('acceptorModal').innerHTML = "";
            document.getElementById('anchorModal').innerHTML = "";

            for(let i = 0; i < value.length; i++) {
                document.getElementById('acceptorModal').innerHTML += '<a href="http://' + value[i]['acceptor'] + '" target="_blank">' + value[i]['acceptor'] + '</a><br>';
                document.getElementById('anchorModal').innerHTML += '<a href="https://www.google.com/search?q=' + value[i]['anchor'] + '" target="_blank">' + value[i]['anchor'] + '</a><br>';
            }
        },
        error: function () { }
    });
});