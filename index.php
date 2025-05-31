<!doctype html>
<html lang="sk">
<?php
    require_once "db/config.php";
    require_once "db/model/Produkt.php";
    require_once "functions/jsAcss.php";

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
            <div class="col-12 text-center mb-5">
                <em class="text-white">Ponuka</em>
                <h2 class="text-white mb-3">Naše kávy</h2>
            </div>
            
            <?php
            $all_products = Produkt::getAll();
            
            $products = array_filter($all_products, function($product) {
                return $product->getDostupneMnozstvo() > 0;
            });
            
            usort($products, function($a, $b) {
                return $b->getId() - $a->getId();
            });
            
            $products = array_slice($products, 0, 10);

            if(count($products) > 0) {
                $half = ceil(count($products) / 2);
                $firstHalf = array_slice($products, 0, $half);
                $secondHalf = array_slice($products, $half);
            ?>
            
            


            <div class="col-lg-6 col-12 mb-4 mb-lg-0">
                <?php foreach($firstHalf as $product): ?>
                <div class="menu-block bg-dark mb-4 rounded p-3" style="height: 250px; overflow: hidden;">
                    <div class="d-flex h-100">
                        <div class="product-image me-4 align-self-center" style="min-width: 100px;">
                            <?php if(!empty($product->getObrazok())): ?>
                                <img src="images/products/<?php echo htmlspecialchars($product->getObrazok()); ?>" 
                                    class="img-fluid rounded" style="width: 180px; height: 180px; object-fit: cover;" 
                                    alt="<?php echo htmlspecialchars($product->getNazov()); ?>">
                            <?php else: ?>
                                <div class="bg-secondary rounded d-flex align-items-center justify-content-center" 
                                    style="width: 180px; height: 180px;">
                                    <i class="bi bi-cup-hot text-white" style="font-size: 3rem;"></i>
                                </div>
                            <?php endif; ?>
                        </div>
            
                        <div class="product-info flex-grow-1 d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="text-white mb-0">
                                    <?php echo htmlspecialchars($product->getNazov()); ?>
                                    <?php if($product->getDostupneMnozstvo() <= 5): ?>
                                    <span class="badge bg-warning text-dark ms-2" style="font-size: 0.7rem;">Posledné kusy</span>
                                    <?php endif; ?>
                                </h5>
                                <strong class="text-white"><?php echo number_format($product->getCena(), 2, ',', ' '); ?> €</strong>
                            </div>
                
                            <p class="text-light small mb-2" style="max-height: 80px; overflow-y: auto;">
                                <?php 
                                $popis = htmlspecialchars($product->getPopis());
                                echo (strlen($popis) > 200) ? substr($popis, 0, 200) . '...' : $popis;
                                ?>
                            </p>
                
                            <div class="mt-auto">
                                <a href="produkt.php?id=<?php echo $product->getId(); ?>" class="btn btn-sm custom-btn">Detail</a>
                                <a href="kosik.php?action=add&id=<?php echo $product->getId(); ?>&mnozstvo=1" class="btn btn-sm custom-btn custom-border-btn ms-2">Do košíka</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="col-lg-6 col-12">
                <?php foreach($secondHalf as $product): ?>
                <div class="menu-block bg-dark mb-4 rounded p-3" style="height: 250px; overflow: hidden;">
                    <div class="d-flex h-100">
                        <div class="product-image me-4 align-self-center" style="min-width: 100px;">
                            <?php if(!empty($product->getObrazok())): ?>
                                <img src="images/products/<?php echo htmlspecialchars($product->getObrazok()); ?>" 
                                    class="img-fluid rounded" style="width: 180px; height: 180px; object-fit: cover;" 
                                    alt="<?php echo htmlspecialchars($product->getNazov()); ?>">
                            <?php else: ?>
                                <div class="bg-secondary rounded d-flex align-items-center justify-content-center" 
                                    style="width: 180px; height: 180px;">
                                    <i class="bi bi-cup-hot text-white" style="font-size: 3rem;"></i>
                                </div>
                            <?php endif; ?>
                        </div>
            
                        <div class="product-info flex-grow-1 d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="text-white mb-0">
                                    <?php echo htmlspecialchars($product->getNazov()); ?>
                                    <?php if($product->getDostupneMnozstvo() <= 5): ?>
                                    <span class="badge bg-warning text-dark ms-2" style="font-size: 0.7rem;">Posledné kusy</span>
                                    <?php endif; ?>
                                </h5>
                                <strong class="text-white"><?php echo number_format($product->getCena(), 2, ',', ' '); ?> €</strong>
                            </div>
                
                            <p class="text-light small mb-2" style="max-height: 80px; overflow-y: auto;">
                                <?php 
                                $popis = htmlspecialchars($product->getPopis());
                                echo (strlen($popis) > 200) ? substr($popis, 0, 200) . '...' : $popis;
                                ?>
                            </p>
                
                            <div class="mt-auto">
                                <a href="produkt.php?id=<?php echo $product->getId(); ?>" class="btn btn-sm custom-btn">Detail</a>
                                <a href="kosik.php?action=add&id=<?php echo $product->getId(); ?>&mnozstvo=1" class="btn btn-sm custom-btn custom-border-btn ms-2">Do košíka</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <?php 
            } else {
                // Ak nie sú žiadne produkty
                echo '<div class="col-12 text-center">';
                echo '<div class="alert alert-dark text-white">';
                echo '<p>Momentálne nemáme žiadne produkty na sklade.</p>';
                echo '</div>';
                echo '</div>';
            }
            ?>
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


            <?php
                $file_path = "parts/footer.php";
            if(!require($file_path)) {
                echo"Failed to include $file_path";
            }
            ?>  
            </main>

        <?php js(); ?>

    </body>
</html>