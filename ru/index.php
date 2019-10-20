<?
include $_SERVER["DOCUMENT_ROOT"]."/header.php";
?>

<div class="flex-container">
    <div class="block">
        <p>Привет! Меня зовут Максим, вы попали на сайт с моим резюме. </br>
            В меню вы можете выбрать то, что вас интересует.</br>
            Если же вас ничего не интересует, то вы можете посмотреть на этих <strong>великолепных</strong> котиков.
        </p>
    </div>
</div>

<?include $_SERVER["DOCUMENT_ROOT"]."/slider.php";?>

<!-- Swiper JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.4.6/js/swiper.min.js"></script>

<!-- Initialize Swiper -->
<script>
    var now_count = 0;
    var swiper = undefined;

    window.onload = function () {
        $('.swiper-container').css('margin', '50px 0 0 0');
        size_for_swiper();
    };

    function size_for_swiper() {
        var screenWidth = window.innerWidth;
        if (screenWidth < 1800 && screenWidth > 1024 && now_count !== 2) {
            if (swiper !== undefined)
                swiper.destroy();
            swiper = new Swiper('.swiper-container', {
                slidesPerView: 2,
                spaceBetween: 0,
                slidesPerGroup: 2,
                loop: true,
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
            });
            now_count = 2;
            swiper.update();
        } else if (screenWidth < 1025 && now_count !== 1) {
            if (swiper !== undefined)
                swiper.destroy();
            swiper = new Swiper('.swiper-container', {
                slidesPerView: 1,
                spaceBetween: 0,
                slidesPerGroup: 1,
                loop: true,
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
            });
            now_count = 1;
            swiper.update();
        } else if (screenWidth > 1799 && now_count !== 3) {
            if (swiper !== undefined)
                swiper.destroy();
            swiper = new Swiper('.swiper-container', {
                slidesPerView: 3,
                spaceBetween: 0,
                slidesPerGroup: 3,
                loop: true,
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
            });
            now_count = 3;
            swiper.update();
        }
    }

    $(window).on('resize', function(){
        size_for_swiper();
    });

</script>
