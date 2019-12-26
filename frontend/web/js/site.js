
function jsFunction(e, value)
{
    let domain = e.getAttribute('data-domain-name');

    $.ajax({
        url: '/api/api/redirect',
        type: 'POST',
        data: {
            domain:domain,
            value:value
        },
        success: function(res){
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
                alert('Аудит проведен');
            },
            error: function(){
                $.pjax.reload({container:"#sitePjax"});
                alert('Error!');
            }
        });
    });
});

$(document).ready(function() {
    $(".my-img").click(function() {
        let img = $(this);
        let src = img.attr('src');
        $("body").append("<div class='popup'>"+
            "<div class='popup_bg'></div>"+
            "<img src='"+src+"' class='popup_img' />"+
            "</div>");
        $(".popup").fadeIn(100);
        $(".popup_bg").click(function() {
            $(".popup").fadeOut(100);
            setTimeout(function() {
                $(".popup").remove();
            }, 100);
        });
    });

});

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

