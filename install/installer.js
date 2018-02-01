$(function() {
    $('.datepicker').datepicker({
        dateFormat: 'yy-mm-dd',
        showOn: 'button',
        buttonImage: '../immagini/calendar.gif',
        buttonImageOnly: true,
        changeMonth: true,
        changeYear: true
   });
});

function setAction(url) {
    objForm = document.getElementById('formInstall');
    objForm.action = url;
    objForm.submit();
}
