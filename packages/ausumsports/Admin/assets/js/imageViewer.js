import "../css/imageViewer.css";

const imageViewer = {

    init : function ()
    {
        $('.imageViewer').find("img").click( function () {
            if(!$("#image-viewer").length)  imageViewer.appendViewer();

            $("#full-image").attr("src", $(this).attr("src"));
            $('#image-viewer').show();

            $("#image-viewer .close").unbind().click(function(){
                $('#image-viewer').hide();
            });
        });
    },

    appendViewer: function ()
    {
        const viewer = $(document.createElement('div')).attr('id', 'image-viewer').appendTo("body:first");
        $(document.createElement('span')).addClass('close').html("&times;").appendTo(viewer);
        const box = $(document.createElement('div')).addClass('imgBox').appendTo(viewer);
        $(document.createElement('img')).addClass('modal-content').attr('id','full-image').appendTo(box);
        return viewer;
    }


}


$(document).on('load', function (event, target) {
    imageViewer.init(target);
});
