document.querySelectorAll('[data-confirm]').forEach(function (element) {
    element.addEventListener('click', function (event) {
        if (!window.confirm(element.dataset.confirm)) event.preventDefault();
    });
});

setTimeout(function () {
    document.querySelectorAll('.alert[data-dismiss]').forEach(function (alert) {
        alert.style.display = 'none';
    });
}, 6000);
