<?
include $_SERVER["DOCUMENT_ROOT"]."/header.php";
?>

<div class="flex-container">
    <div class="block">
        <p style="font-weight: bold">Лабораторная работа 3.</p>
        <p><a>Задание</a></p>
    </div>
</div>

<div class="flex-container-laba2" style="margin-top: 2.5%">
    <div></div>
    <div></div>
    <div></div>
    <div></div>
    <div></div>
</div>


<script>
    var lastnumber = 0;
    var randomnumber = 0;
    var z = 0;
    var time = 1;
    window.onload = function () {
        function getRandomInt(min,max) {
            return Math.floor(Math.random()*(max - min)) + 4;
        }
        var img = "<img src='/images/pepegasmall.png'>";
        while (randomnumber === lastnumber)
            randomnumber = getRandomInt(0,5);
        document.getElementsByTagName('div')[randomnumber].innerHTML = img ;
        if (z === 0){
            z = z + 1;
        } else {
            document.getElementsByTagName('div')[lastnumber].innerHTML = '';
        }
        lastnumber = randomnumber;
        window.setTimeout(arguments.callee, time * 1000);
    }();
</script>