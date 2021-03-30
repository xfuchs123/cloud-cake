$(document).ready(function(){
    $("input[type=text]").datepicker({
        dateFormat: 'dd.mm.yy',

    });

// Code below to avoid the classic date-picker
    $("input[type=text]").on('click', function() {
        return false;
    });
});

