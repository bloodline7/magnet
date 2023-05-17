require("jquery-cropper");
import 'cropperjs/dist/cropper.css';

const cropperSet =
    {
        init: function () {

            this.setting = {width: 400, height: 400};
            this.setLayout();
            this.setCropper();
            this.setInputImage();
        },

        setLayout: function () {
            $(".image-crop").css({'width': '100%', 'height': '400px'});
            this.image = $(".image-crop > img").hide();
            this.image.css('max-width', '100%');
        },

        setCropper: function () {

            this.image.cropper({
                autoCropArea: 1,
                ready: function () {
                    $(".img-preview").css('overflow', 'hidden');
                    $("#crop").click(function () {

                        const imgdata = cropperSet.image.cropper('getImageData');

                        this.zoom = {
                            height: imgdata.naturalHeight / imgdata.height,
                            width: imgdata.naturalWidth / imgdata.width
                        }

                        const cropdata = cropperSet.image.cropper('getCropBoxData');

                        const realWidth = cropdata.width * this.zoom.width;
                        const cropWidth = (realWidth > cropperSet.setting.width) ? cropperSet.setting.width : realWidth;

                        const realHeight = cropdata.height * this.zoom.height;
                        const cropHeight = (realHeight > cropperSet.setting.height) ? cropperSet.setting.height : realHeight;


                        //setting 과 비교해서 구할 이미지의 가로 세로 사이즈를 강제함.
                        // 최종 이미지의 사이즈와  용량을 구해서 이미지 하단에 표기함

                        const croppedimage = cropperSet.image.cropper('getCroppedCanvas',
                            {
                                width: cropWidth,
                                height: cropHeight,
                                beforeDrawImage: function (canvas) {
                                    const context = canvas.getContext('2d');
                                    context.imageSmoothingEnabled = false;
                                    context.imageSmoothingQuality = 'high';
                                }
                            });

                       const showImg = croppedimage.toDataURL(cropperSet.fileType);

                        croppedimage.toBlob( function (blob) {

                            console.log("ImageSize:" + blob.size);

                            $.ajax('/path/to/upload', {
                                method: "POST",
                                data: formData,
                                processData: false,
                                contentType: false,
                                success: function () {
                                    console.log('Upload success');
                                },
                                error: function () {
                                    console.log('Upload error');
                                }
                            });

                        }, cropperSet.fileType , 1 );

                        $(document.createElement('img')).attr('src', showImg).css('width', '100%').appendTo($(".img-preview").empty());

                    });
                }
            });

        },

        setInputImage: function () {

            const inputImage = $("#inputImage");
            if (window.FileReader) {
                inputImage.change(function () {
                    var fileReader = new FileReader(),
                        files = this.files,
                        file;

                    if (!files.length) {
                        return;
                    }

                    file = files[0];
                    if (/^image\/\w+$/.test(file.type)) {
                        cropperSet.fileType = file.type;

                        console.log("FileSize:" + file.size);

                        fileReader.readAsDataURL(file);
                        fileReader.onload = function () {
                            cropperSet.image.cropper("reset", true).cropper("replace", this.result);
                        };

                    } else {
                        alert("Please upload a image file");
                    }

                });

            } else {
                inputImage.hide();
            }
        }
    }

$(document).on('load', function (event, target) {
    cropperSet.init();
});

