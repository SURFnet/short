function copyToClipboard(text, el) {
  var elOriginalText = el.attr('data-original-title');

  var copyTextArea = document.createElement("textarea");
  copyTextArea.value = text;
  document.body.appendChild(copyTextArea);
  copyTextArea.select();
  try {
    var successful = document.execCommand('copy');
    var msg = successful ? 'Gekopieerd!' : 'Oeps, kopie naar klembord mislukt';
    el.attr('data-original-title', msg).tooltip('show');
  } catch (err) {
    console.log('Kopie naar klembord mislukt');
  }
  document.body.removeChild(copyTextArea);
  el.attr('data-original-title', elOriginalText);
}



(function() {
'use strict';

  $('.cpy').click(function() {
    var text = $(this).attr('data-clipboard-text');
    var el = $(this);
    copyToClipboard(text, el);
  });


$('[data-toggle="tooltip"]').tooltip();

$('[data-toggle=confirmation]').confirmation({
  rootSelector: '[data-toggle=confirmation]'
});

window.addEventListener('load', function() {
  
var forms = document.getElementsByClassName('needs-validation');

var validation = Array.prototype.filter.call(forms, function(form) {
    form.addEventListener('submit', function(event) {
      if (form.checkValidity() === false) {
	event.preventDefault();
	event.stopPropagation();
      }
      form.classList.add('was-validated');
    }, false);
  });
}, false);
})();
