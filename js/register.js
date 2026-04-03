(function () {
  "use strict";
  var form = document.getElementById("regForm");
  if (!form) return;
  var p1 = document.getElementById("password");
  var p2 = document.getElementById("password2");
  var fb = document.getElementById("pw2fb");
  function check() {
    if (!p2.value) {
      p2.classList.remove("is-invalid");
      return true;
    }
    var ok = p1.value === p2.value;
    p2.classList.toggle("is-invalid", !ok);
    if (fb) fb.style.display = ok ? "none" : "block";
    return ok;
  }
  p1.addEventListener("input", check);
  p2.addEventListener("input", check);
  form.addEventListener("submit", function (e) {
    if (!check()) e.preventDefault();
  });
})();
