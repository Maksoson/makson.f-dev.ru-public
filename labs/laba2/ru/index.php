<?
    include $_SERVER["DOCUMENT_ROOT"]."/header.php";
?>

<div class="flex-container">
    <div class="block">
        <p style="font-weight: bold">Лабораторная работа 2.</p>
        <p><a>Задание</a></p>
    </div>
    <div style="margin-left: auto; margin-right: auto; margin-top: 2.5%">
        <table id="calendar2">
            <thead>
            <tr><td>‹<td colspan="5" style="font-weight: bold"><td>›
            <tr><td>Пн<td>Вт<td>Ср<td>Чт<td>Пт<td>Сб<td>Вс
            <tbody>
        </table>
    </div>
</div>
<div class="flex-container">
    <div class="timer"></div>
</div>

<script>
    function Calendar2(id, year, month) {
        var Dlast = new Date(year,month+1,0).getDate(),
            D = new Date(year,month,Dlast),
            DNlast = new Date(D.getFullYear(),D.getMonth(),Dlast).getDay(),
            DNfirst = new Date(D.getFullYear(),D.getMonth(),1).getDay(),
            calendar = '<tr>',
            month=["Январь","Февраль","Март","Апрель","Май","Июнь","Июль","Август","Сентябрь","Октябрь","Ноябрь","Декабрь"];
        if (DNfirst != 0) {
            for(var  i = 1; i < DNfirst; i++) calendar += '<td>';
        }else{
            for(var  i = 0; i < 6; i++) calendar += '<td>';
        }
        for(var  i = 1; i <= Dlast; i++) {
            if (i == new Date().getDate() && D.getFullYear() == new Date().getFullYear() && D.getMonth() == new Date().getMonth()) {
                calendar += '<td class="today">' + i;
            }else{
                calendar += '<td>' + i;
            }
            if (new Date(D.getFullYear(),D.getMonth(),i).getDay() == 0) {
                calendar += '<tr>';
            }
        }
        for(var  i = DNlast; i < 7; i++) calendar += '<td>&nbsp;';
        document.querySelector('#'+id+' tbody').innerHTML = calendar;
        document.querySelector('#'+id+' thead td:nth-child(2)').innerHTML = month[D.getMonth()] + ' ' + D.getFullYear();
        document.querySelector('#'+id+' thead td:nth-child(2)').dataset.month = D.getMonth();
        document.querySelector('#'+id+' thead td:nth-child(2)').dataset.year = D.getFullYear();

        if (document.querySelectorAll('#'+id+' tbody tr').length < 6) {
            document.querySelector('#'+id+' tbody').innerHTML += '<tr><td>&nbsp;<td>&nbsp;<td>&nbsp;<td>&nbsp;<td>&nbsp;<td>&nbsp;<td>&nbsp;';
        }
    }

    Calendar2("calendar2", new Date().getFullYear(), new Date().getMonth());

    document.querySelector('#calendar2 thead tr:nth-child(1) td:nth-child(1)').onclick = function() {
        Calendar2("calendar2", document.querySelector('#calendar2 thead td:nth-child(2)').dataset.year, parseFloat(document.querySelector('#calendar2 thead td:nth-child(2)').dataset.month)-1);
    };

    document.querySelector('#calendar2 thead tr:nth-child(1) td:nth-child(3)').onclick = function() {
        Calendar2("calendar2", document.querySelector('#calendar2 thead td:nth-child(2)').dataset.year, parseFloat(document.querySelector('#calendar2 thead td:nth-child(2)').dataset.month)+1);
    }
</script>

<script>
        (function(){
            var date = new Date();
            var array = ['','',''];
            if (date.getHours() < 10) {
                array[0] = '0'
            }
            if (date.getMinutes() < 10) {
                array[1] = '0'
            }
            if (date.getSeconds() < 10) {
                array[2] = '0'
            }

            var time = array[0] + date.getHours() + ':' + array[1] + date.getMinutes() + ':' + array[2] + date.getSeconds();


            document.getElementsByClassName('timer')[0].innerHTML = time;
            window.setTimeout(arguments.callee, 1000);
        })();
</script>


