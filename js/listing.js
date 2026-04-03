(function () {
  'use strict';
  var form = document.getElementById('listingForm');
  if (!form) return;
  form.addEventListener('submit', function (e) {
    var price = parseFloat(document.getElementById('price').value);
    var stock = parseInt(document.getElementById('stock').value, 10);
    if (isNaN(price) || price < 0) {
      e.preventDefault();
      alert('Enter a valid price.');
      return;
    }
    if (isNaN(stock) || stock < 0) {
      e.preventDefault();
      alert('Enter a valid stock amount.');
    }
  });
})();
