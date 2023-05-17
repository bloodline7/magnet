import $ from "jquery";
require("./dropzone");

$(document).on('load', function (event, target) {
    $(target.target).find(".dropzone").each(function () {

        const zone = $(this);

        const message = $(this).attr('data-message');
        const upload = $(this).attr('data-upload-url');


        function viewImg(Target, url)
        {
            const name = Target.find('input[type=hidden]').attr('name');
            $('img.'+name).remove();
            const img = $(document.createElement('img')).css('max-width','100%').addClass('mb-3 rounded-1').addClass(name)
                .attr('src', url);
            img.insertAfter(Target);
        }



        $(this).dropzone({

            url: upload,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dictDefaultMessage : message,
            /*
            accept: function(file, done) {

                console.log(file);

                var fileReader = new FileReader();

                if (/^image\/\w+$/.test(file.type)) {
                    fileReader.readAsDataURL(file);
                    fileReader.onload = function () {
                        $inputImage.val("");
                        $image.cropper("reset", true).cropper("replace", this.result);
                    };
                } else {
                    alert("Please upload a image file");
                }


                //window.loadCropper(file.dataURL) ;

                //done();

            },
*/
            init: function () {

                const url = zone.find("input[type=hidden]").val();

                if(url)
                {
                    viewImg(zone, url);
                }

                this.on("complete", function (res) {
                    console.log(res.xhr.response)
                    const result = JSON.parse(res.xhr.response);
                    viewImg(zone, result.url);
                    this.removeFile(res);
                    zone.find("input[type=hidden]").val(result.url);
                });
            }

        });
    })
});

