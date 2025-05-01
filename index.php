<!doctype html>
<html lang="sk">
<?php
    $file_path = "parts/head.php";
if(!require($file_path)) {
    echo"Failed to include $file_path";
}
?>  
    <body>
        <?php
            $file_path = "parts/nav.php";
            if(!require($file_path)) {
                echo "Failed to include $file_path";
            }
        ?>

        <?php
            $file_path = "parts/header.php";
            if(!require($file_path)) {
                echo "Failed to include $file_path";
            }
        ?>     

            <main>
                <section class="about-section section-padding" id="section_2">
                    <div class="section-overlay"></div>
                    <div class="container">
                        <div class="row align-items-center">

                            <div class="col-lg-6 col-12">
                                <div class="ratio ratio-1x1">
                                    <video autoplay="" loop="" muted="" class="custom-video" poster="">
                                        <source src="videos/pexels-mike-jones-9046237.mp4" type="video/mp4">

                                        Váš prehliadač nepodporuje video tag.
                                    </video>

                                    <div class="about-video-info d-flex flex-column">
                                        <h4 class="mt-auto">Pražíme kávu od roku 2009.</h4>

                                        <h4>Najlepšia káva na Slovensku.</h4>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-5 col-12 mt-4 mt-lg-0 mx-auto">
                                <em class="text-white">Zlaté zrnko</em>

                                <h2 class="text-white mb-3">Prémiová pražiareň kávy</h2>

                                <p class="text-white">Naša pražiareň sa nachádza v srdci mesta už vyše desať rokov a postupne sa stala obľúbeným miestom všetkých milovníkov kvalitnej kávy.</p>

                                <p class="text-white">Záleží nám na kvalite a pôvode každého zrnka. Každú várku kávy pražíme s láskou a starostlivosťou, aby sme dosiahli dokonalú chuť a arómu, ktorá poteší vaše zmysly.</p>

                                <a href="#barista-team" class="smoothscroll btn custom-btn custom-border-btn mt-3 mb-4">Spoznajte náš tím</a>
                            </div>

                        </div>
                    </div>
                </section>


                <section class="barista-section section-padding section-bg" id="barista-team">
                    <div class="container">
                        <div class="row justify-content-center">

                            <div class="col-lg-12 col-12 text-center mb-4 pb-lg-2">
                                <em class="text-white">Kreatívny tím</em>

                                <h2 class="text-white">Naši pražiči a baristi</h2>
                            </div>

                            <div class="col-lg-3 col-md-6 col-12 mb-4">
                                <div class="team-block-wrap">
                                    <div class="team-block-info d-flex flex-column">
                                        <div class="d-flex mt-auto mb-3">
                                            <h4 class="text-white mb-0">Tomáš</h4>

                                            <p class="badge ms-4"><em>Majiteľ</em></p>
                                        </div>

                                        <p class="text-white mb-0">Expert s 15-ročnými skúsenosťami s pražením kávy.</p>
                                    </div>

                                    <div class="team-block-image-wrap">
                                        <img src="images/team/portrait-elegant-old-man-wearing-suit.jpg" class="team-block-image img-fluid" alt="">
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6 col-12 mb-4">
                                <div class="team-block-wrap">
                                    <div class="team-block-info d-flex flex-column">
                                        <div class="d-flex mt-auto mb-3">
                                            <h4 class="text-white mb-0">Zuzana</h4>

                                            <p class="badge ms-4"><em>Manažérka</em></p>
                                        </div>

                                        <p class="text-white mb-0">Zabezpečuje, aby všetko fungovalo ako hodinky.</p>
                                    </div>

                                    <div class="team-block-image-wrap">
                                        <img src="images/team/cute-korean-barista-girl-pouring-coffee-prepare-filter-batch-brew-pour-working-cafe.jpg" class="team-block-image img-fluid" alt="">
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6 col-12 mb-4">
                                <div class="team-block-wrap">
                                    <div class="team-block-info d-flex flex-column">
                                        <div class="d-flex mt-auto mb-3">
                                            <h4 class="text-white mb-0">Martin</h4>

                                            <p class="badge ms-4"><em>Hlavný pražič</em></p>
                                        </div>

                                        <p class="text-white mb-0">Majster v hľadaní dokonalých profilov praženia.</p>
                                    </div>

                                    <div class="team-block-image-wrap">
                                        <img src="images/team/small-business-owner-drinking-coffee.jpg" class="team-block-image img-fluid" alt="">
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6 col-12">
                                <div class="team-block-wrap">
                                    <div class="team-block-info d-flex flex-column">
                                        <div class="d-flex mt-auto mb-3">
                                            <h4 class="text-white mb-0">Michaela</h4>

                                            <p class="badge ms-4"><em>Baristka</em></p>
                                        </div>

                                        <p class="text-white mb-0">Jej latte art vás očarí rovnako ako jej úsmev.</p>
                                    </div>

                                    <div class="team-block-image-wrap">
                                        <img src="images/team/smiley-business-woman-working-cashier.jpg" class="team-block-image img-fluid" alt="">
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </section>


                <section class="menu-section section-padding" id="section_3">
                    <div class="container">
                        <div class="row">

                            <div class="col-lg-6 col-12 mb-4 mb-lg-0">
                                <div class="menu-block-wrap">
                                    <div class="text-center mb-4 pb-lg-2">
                                        <em class="text-white">Naše kávy</em>
                                        <h4 class="text-white">Jednodruhové kávy</h4>
                                    </div>

                                    <div class="menu-block">
                                        <div class="d-flex">
                                            <h6>Etiópia Yirgacheffe</h6>
                                        
                                            <span class="underline"></span>

                                            <strong class="ms-auto">12,50 €</strong>
                                        </div>

                                        <div class="border-top mt-2 pt-2">
                                            <small>Ovocné tóny s jemnou kyselinkou a nádychom citrusov</small>
                                        </div>
                                    </div>

                                    <div class="menu-block my-4">
                                        <div class="d-flex">
                                            <h6>
                                                Brazília Santos
                                            </h6>
                                        
                                            <span class="underline"></span>

                                            <strong class="text-white ms-auto"><del>16,50 €</del></strong>

                                            <strong class="ms-2">12,00 €</strong>
                                        </div>

                                        <div class="border-top mt-2 pt-2">
                                            <small>Oriešková chuť s karamelovou sladkosťou</small>
                                        </div>
                                    </div>

                                    <div class="menu-block">
                                        <div class="d-flex">
                                            <h6>Guatemala Antigua
                                                <span class="badge ms-3">Odporúčame</span>
                                            </h6>
                                        
                                            <span class="underline"></span>

                                            <strong class="ms-auto">15,00 €</strong>
                                        </div>

                                        <div class="border-top mt-2 pt-2">
                                            <small>Jemná kyselinka s tónmi mandlí a čokolády</small>
                                        </div>
                                    </div>

                                    <div class="menu-block my-4">
                                        <div class="d-flex">
                                            <h6>Kolumbia Supremo</h6>
                                        
                                            <span class="underline"></span>

                                            <strong class="ms-auto">14,50 €</strong>
                                        </div>

                                        <div class="border-top mt-2 pt-2">
                                            <small>Vyvážená chuť s tónmi čokolády a orechov</small>
                                        </div>
                                    </div>

                                    <div class="menu-block">
                                        <div class="d-flex">
                                            <h6>Keňa AA</h6>
                                        
                                            <span class="underline"></span>

                                            <strong class="ms-auto">18,00 €</strong>
                                        </div>

                                        <div class="border-top mt-2 pt-2">
                                            <small>Výrazná ovocná chuť s bohatou kyselinkou</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6 col-12">
                                <div class="menu-block-wrap">
                                    <div class="text-center mb-4 pb-lg-2">
                                        <em class="text-white">Špeciality</em>
                                        <h4 class="text-white">Zmesi a espresso</h4>
                                    </div>

                                    <div class="menu-block">
                                        <div class="d-flex">
                                            <h6>Ranná zmes</h6>
                                        
                                            <span class="underline"></span>

                                            <strong class="text-white ms-auto"><del>12,50 €</del></strong>

                                            <strong class="ms-2">9,50 €</strong>
                                        </div>

                                        <div class="border-top mt-2 pt-2">
                                            <small>Intenzívna zmes s výraznou chuťou a vyššou kofeínovou dávkou</small>
                                        </div>
                                    </div>

                                    <div class="menu-block my-4">
                                        <div class="d-flex">
                                            <h6>
                                                Zlatá zmes
                                                <span class="badge ms-3">Bestseller</span>
                                            </h6>
                                        
                                            <span class="underline"></span>

                                            <strong class="ms-auto">11,90 €</strong>
                                        </div>

                                        <div class="border-top mt-2 pt-2">
                                            <small>Naša vlajková loď - dokonale vyvážená zmes</small>
                                        </div>
                                    </div>

                                    <div class="menu-block">
                                        <div class="d-flex">
                                            <h6>Dekaf šetrný</h6>
                                        
                                            <span class="underline"></span>

                                            <strong class="ms-auto">13,50 €</strong>
                                        </div>

                                        <div class="border-top mt-2 pt-2">
                                            <small>Káva bez kofeínu spracovaná švajčiarskou vodnou metódou</small>
                                        </div>
                                    </div>

                                    <div class="menu-block my-4">
                                        <div class="d-flex">
                                            <h6>Espresso zmes</h6>
                                        
                                            <span class="underline"></span>

                                            <strong class="ms-auto">13,50 €</strong>
                                        </div>

                                        <div class="border-top mt-2 pt-2">
                                            <small>Temno pražená zmes ideálna pre espresso a mliečne nápoje</small>
                                        </div>
                                    </div>

                                    <div class="menu-block">
                                        <div class="d-flex">
                                            <h6>Ochutená káva - Čokoláda</h6>
                                        
                                            <span class="underline"></span>

                                            <strong class="ms-auto">10,25 €</strong>
                                        </div>

                                        <div class="border-top mt-2 pt-2">
                                            <small>Stredne pražená káva s prirodzenou čokoládovou arómou</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </section>

                <!-- Nová sekcia pre objednávku kávy -->
                <section class="about-section section-padding" id="section_order">
                    <div class="section-overlay"></div>
                    <div class="container">
                        <div class="row">   

            <div class="col-lg-12 col-12">
                <em class="text-white">Objednajte si</em>
                <h2 class="text-white mb-4 pb-lg-2">Naša čerstvo upražená káva</h2>
            </div>

            <div class="col-12">
                <a href="produkty.php" class="btn custom-btn custom-border-btn">Prehliadať našu ponuku</a>
            </div>

        </div>
    </div>
</section>


                <section class="reviews-section section-padding section-bg" id="section_4">
                    <div class="container">
                        <div class="row justify-content-center">

                            <div class="col-lg-12 col-12 text-center mb-4 pb-lg-2">
                                <em class="text-white">Recenzie od zákazníkov</em>

                                <h2 class="text-white">Referencie</h2>
                            </div>

                            <div class="timeline">
                                <div class="timeline-container timeline-container-left">
                                    <div class="timeline-content">
                                        <div class="reviews-block">
                                            <div class="reviews-block-image-wrap d-flex align-items-center">
                                                <img src="images/reviews/young-woman-with-round-glasses-yellow-sweater.jpg" class="reviews-block-image img-fluid" alt="">

                                                <div class="">
                                                    <h6 class="text-white mb-0">Simona</h6>
                                                    <em class="text-white">Zákazníčka</em>
                                                </div>
                                            </div>

                                            <div class="reviews-block-info">
                                                <p>Káva od Zlatého zrnka je absolútna špička. Skúsila som už mnoho druhov, ale vždy sa vraciam k ich Etiópii, ktorá má neskutočne ovocnú chuť.</p>

                                                <div class="d-flex border-top pt-3 mt-4">
                                                    <strong class="text-white">4.5 <small class="ms-2">Hodnotenie</small></strong>

                                                    <div class="reviews-group ms-auto">
                                                        <i class="bi-star-fill"></i>
                                                        <i class="bi-star-fill"></i>
                                                        <i class="bi-star-fill"></i>
                                                        <i class="bi-star-fill"></i>
                                                        <i class="bi-star"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="timeline-container timeline-container-right">
                                    <div class="timeline-content">
                                        <div class="reviews-block">
                                            <div class="reviews-block-image-wrap d-flex align-items-center">
                                                <img src="images/reviews/senior-man-white-sweater-eyeglasses.jpg" class="reviews-block-image img-fluid" alt="">

                                                <div class="">
                                                    <h6 class="text-white mb-0">Roman</h6>
                                                    <em class="text-white">Zákazník</em>
                                                </div>
                                            </div>

                                            <div class="reviews-block-info">
                                                <p>Ako dlhoročný milovník kávy môžem povedať, že Zlaté zrnko ponúka jednu z najlepších káv na Slovensku. Cením si ich profesionalitu a kvalitu každej zásielky.</p>

                                                <div class="d-flex border-top pt-3 mt-4">
                                                    <strong class="text-white">5.0 <small class="ms-2">Hodnotenie</small></strong>

                                                    <div class="reviews-group ms-auto">
                                                        <i class="bi-star-fill"></i>
                                                        <i class="bi-star-fill"></i>
                                                        <i class="bi-star-fill"></i>
                                                        <i class="bi-star-fill"></i>
                                                        <i class="bi-star-fill"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="timeline-container timeline-container-left">
                                    <div class="timeline-content">
                                        <div class="reviews-block">
                                            <div class="reviews-block-image-wrap d-flex align-items-center">
                                                <img src="images/reviews/young-beautiful-woman-pink-warm-sweater-natural-look-smiling-portrait-isolated-long-hair.jpg" class="reviews-block-image img-fluid" alt="">

                                                <div class="">
                                                    <h6 class="text-white mb-0">Kristína</h6>
                                                    <em class="text-white">Zákazníčka</em>
                                                </div>
                                            </div>

                                            <div class="reviews-block-info">
                                                <p>Od kedy som objavila Zlaté zrnko, už nepotrebujem chodiť do kaviarní. Ich káva je vždy čerstvo upražená a dodaná v krásnom ekologickom balení.</p>

                                                <div class="d-flex border-top pt-3 mt-4">
                                                    <strong class="text-white">4.5 <small class="ms-2">Hodnotenie</small></strong>

                                                    <div class="reviews-group ms-auto">
                                                        <i class="bi-star-fill"></i>
                                                        <i class="bi-star-fill"></i>
                                                        <i class="bi-star-fill"></i>
                                                        <i class="bi-star-fill"></i>
                                                        <i class="bi-star"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </section>


                <!-- -->
            <?php
                $file_path = "parts/footer.php";
            if(!require($file_path)) {
                echo"Failed to include $file_path";
            }
            ?>  
            </main>

        <!-- JAVASCRIPT FILES -->
        <script src="js/jquery.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script src="js/jquery.sticky.js"></script>
        <script src="js/click-scroll.js"></script>
        <script src="js/vegas.min.js"></script>
        <script src="js/custom.js"></script>

    </body>
</html>