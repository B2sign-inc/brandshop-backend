(function () {
    var elements = document.querySelectorAll('.partial-delete');

    if (elements && elements.length > 0) {

        elements.forEach(function(element) {
            element.addEventListener('click', function (e) {
                e.preventDefault();
                if (confirm('Are you sure you want to delete this item?')) {
                    var formId = this.getAttribute('data-delete-form-id');
                    if (formId) {
                        var form = document.getElementById(formId);
                        form && form.submit();
                    }
                }
            });
        });
    }
})();