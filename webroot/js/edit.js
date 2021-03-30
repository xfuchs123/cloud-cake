$(document).ready(function(){
    $("#valid-from").datepicker({
        dateFormat: 'dd.mm.yy',

    });
    $("#valid-to").datepicker({
        dateFormat: 'dd.mm.yy',

    });

// Code below to avoid the classic date-picker
    $("#valid-from").on('click', function() {
        return false;
    });
    $("#valid-to").on('click', function() {
        return false;
    });
});
