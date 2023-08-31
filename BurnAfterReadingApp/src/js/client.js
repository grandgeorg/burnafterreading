(function() {
    "use strict"
    document.addEventListener("DOMContentLoaded", function () {
        const copyClientContent = document.querySelector('#copy-client-content');
        copyClientContent.addEventListener('click', function() {
            const clientContent = document.querySelector('.client-content');
            navigator.clipboard.writeText(clientContent.textContent);
        });
    });
})();