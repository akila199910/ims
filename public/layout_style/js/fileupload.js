
$(document).ready(function () {
    document.getElementById('file-label').addEventListener('click', function () {
        document.getElementById('image').click();
    });

    document.getElementById('image').addEventListener('change', function () {
        var fileName = this.files[0] ? this.files[0].name : 'No file chosen';
        document.querySelector('.upload-path').value = fileName;
    });
});

$(document).ready(function () {
    document.getElementById('file-label').addEventListener('click', function () {
        document.getElementById('agreement_document').click();
    });

    document.getElementById('agreement_document').addEventListener('change', function () {
        var fileName = this.files[0] ? this.files[0].name : 'No file chosen';
        document.querySelector('.upload-path').value = fileName;
    });
});

