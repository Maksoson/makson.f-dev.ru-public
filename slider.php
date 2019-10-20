<div class="swiper-container">
    <?$slides = array(
        'kitucheni.jpg',
        'kitswag.jpg',
        'kitsmaslom.jpg',
        'kitresident.jpg',
        'kitlezit.jpg',
        'kitbabyrage.jpg',
        'kitDJ.jpg',
        'kit6biblethump.jpg',
        'kit3.jpg',
        'kit5smert.jpg',
        'kit4zirni.jpg',
        'kitsmallface.png',
//        'kitsnuffka.jpg',
        'kit2.jpg',
        'kit1.jpg',
        'glupikit.jpg',
        'kitsosiski.jpg',
        'kitohwait.jpg',
        'kitpefko.jpg',
    )?>
    <div class="swiper-wrapper">
        <?foreach($slides as $slide):?>
            <div class="swiper-slide"><img src="/images/<?=$slide?>" width="440" height="485"></div>
        <?endforeach;?>
    </div>
    <!--&lt;!&ndash; Add Pagination &ndash;&gt;-->
    <!--<div class="swiper-pagination"></div>-->
    <!--&lt;!&ndash; Add Arrows &ndash;&gt;-->
    <div class="swiper-button-next"></div>
    <div class="swiper-button-prev"></div>
</div>
