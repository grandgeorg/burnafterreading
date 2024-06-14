(function () {
  "use strict";
  document.addEventListener("DOMContentLoaded", function () {
    const copyAdminContent = document.querySelector("#copy-admin-content");
    copyAdminContent.addEventListener("click", function () {
      const adminContent = document.querySelector(copyAdminContent.dataset.clipboardTarget);
      navigator.clipboard.writeText(adminContent.textContent);
    });
  });
})();
