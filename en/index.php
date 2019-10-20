<?include $_SERVER["DOCUMENT_ROOT"]."/headeren.php"; ?>
<div class="flex-container">
    <div class="block">
        <p>Hello! My name is Max and you came to the site with my resume. </br>
            In the menu you can choose what interests you.</br>
            If nothing interests you, then you can look at these <strong>magnificent</strong> cats.
        </p>
    </div>
</div>

<?include $_SERVER["DOCUMENT_ROOT"]."/slider.php";?>

<!-- Swiper JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.4.6/js/swiper.min.js"></script>

<!-- Initialize Swiper -->
<script>
    var swiper = new Swiper('.swiper-container', {
        slidesPerView: 4,
        spaceBetween: 0,
        slidesPerGroup: 1,
        // loop: true,
        // loopFillGroupWithBlank: true,
        // pagination: {
        //     el: '.swiper-pagination',
        //     clickable: true,
        // },
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
    });
</script>
</body>
</html>