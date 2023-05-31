$(document).ready(function() {
    var uploadFile;

    $(document).on('dragenter', function(e) {
        e.preventDefault();
        e.stopPropagation();
    });

    $(document).on('dragover', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $('.upload-box').css('backgroundColor', '#779977');
    });

    $(document).on('dragleave', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $('.upload-box').css('backgroundColor', 'transparent');
    });

    $(document).on('drop', function(e) {
        $('.upload-box').css('backgroundColor', 'transparent');

        uploadFile = e.originalEvent.dataTransfer.files[0];
        console.dir(uploadFile);
        $('.btn-file').addClass('hidden');
        $('.file-desc').text(uploadFile.name);

        return false;
    });

    $(document).on('change', '.btn-file', function(e) {
        uploadFile = e.target.files[0];
        $('.file-desc').text('');
    });

    $('.btn-timeout').click(function() {
        $('.btn-timeout').removeClass('active');
        $(this).addClass('active');
    });

    function showTooltip(target, message) {
        $('.tooltip').addClass('show');
        $('.tooltip').find('p.context').text(message);
        var tooltipLeft = $(target).offset().left + $(target).innerWidth() / 2 - $('.tooltip').innerWidth() / 2;
        var tooltipTop = $(target).offset().top - $('.tooltip').height() - 18;
        $('.tooltip').css('top', `${tooltipTop}px`);
        $('.tooltip').css('left', `${tooltipLeft}px`);
        $('.tooltip').find('div.tooltip-arrow').css('left', `${$('.tooltip').width() / 2 - 6}px`);
        $('.tooltip').stop().animate({
            opacity: 1
        }, 100);

        $('.tooltip').animate({
            opacity: 1
        }, 250, function() {
            $('.tooltip').animate({
                opacity: 0
            }, 500, function() {
                $('.tooltip').removeClass('show');
            });
        });
    }

    $('.upload-result').click(function() {
        var isIE = false || !!document.documentMode;

        if (isIE) {
            window.clipboardData.setData("text", $(this).text());
            showTooltip($('.upload-result'), '복사 성공!');
        }
        else {
            navigator.clipboard.writeText($(this).text()).then(function() {
                showTooltip($('.upload-result'), '복사 성공!');
            }, function() {
                showTooltip($('.upload-result'), '복사 권한을 먼저 허용해주세요.');
            });
        }

        showTooltip($('.upload-result'), '복사 성공!');
    });

    $('.btn-upload').click(function() {
        if (!uploadFile) return;

        $('.overlay').addClass('active');

        const formData = new FormData();
        var timeout = $('.input-timeout').val() * $('.btn-timeout.active').attr('data-multiplier');
        if ($('.btn-timeout.active').attr('data-multiplier') == '09') {
            timeout = 315360000;
        }

        formData.append('file', uploadFile);
        formData.append('timeout', timeout);

        $('.upload-progress').addClass('active');
        $('.upload-progress-level').css('width', '0%');
        $('.upload-progress-text').text('0%');

        $.ajax({
            url: './upload',
            async: true,
            method: 'POST',
            processData: false,
            contentType: false,
            data: formData,
            xhr: function() {
                var xhr = $.ajaxSettings.xhr();

                xhr.upload.onprogress = function(e) {
                    var percent = Math.floor(e.loaded * 10000 / e.total) / 100;
                    $('.upload-progress-level').css('width', `${percent}%`);
                    $('.upload-progress-text').text(`${percent}%`);
                };

                return xhr;
            },
            success: function(res) {
                $('.overlay').removeClass('active');
                clearUpload();

                var data = JSON.parse(res);
                if (data.result == 0) {
                    $('.upload-result').html(`<font color="red">upload fail (${data.message})</font>`);
                    return;
                }
                else {
                    $('.upload-result').text(data.url.replace(/ /g, '%20'));
                }
            },
            error: function(res) {
                $('.overlay').removeClass('active');
                clearUpload();
                $('.upload-result').html(`<font color="red">upload fail</font>`);
            }
        });
    });

    function clearUpload() {
        uploadFile = '';
        $('.file-desc').text('');
        $('.btn-file').remove();
        $('.file-selector').append(`<input class="btn-file" type="file">`);
        $('.upload-progress').removeClass('active');
    }
});