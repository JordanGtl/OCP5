<!-- Header -->
    <header>
        <div class="container">
            <div class="row">
                <div class="col-lg-12 header-img">
                    <img class="img-responsive" src="https://gtl-studio.com/images/header.png" width="400" alt="">
                    <div class="intro-text">
                        <span class="name">Jordan GUILLOT</span>
                        <hr class="star-light">
                        <span class="skills">Développeur, plus qu'un métier, une passion !</span>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Portfolio Grid Section -->
    <section id="portfolio">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <h2>Portfolio</h2>
                    <hr class="star-primary">
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4 portfolio-item">
                    <a href="#portfolioModal1" class="portfolio-link" data-toggle="modal">
                        <div class="caption">
                            <div class="caption-content">
                                <i class="fa fa-search-plus fa-3x"></i>
                            </div>
                        </div>
                        <img src="https://gtl-studio.com/images/vincentv.png" class="img-responsive" alt="">
                    </a>
                </div>
                <div class="col-sm-4 portfolio-item">
                    <a href="#portfolioModal2" class="portfolio-link" data-toggle="modal">
                        <div class="caption">
                            <div class="caption-content">
                                <i class="fa fa-search-plus fa-3x"></i>
                            </div>
                        </div>
                        <img src="https://gtl-studio.com/images/clementb.png" class="img-responsive" alt="">
                    </a>
                </div>
                <div class="col-sm-4 portfolio-item">
                    <a href="#portfolioModal3" class="portfolio-link" data-toggle="modal">
                        <div class="caption">
                            <div class="caption-content">
                                <i class="fa fa-search-plus fa-3x"></i>
                            </div>
                        </div>
                        <img src="https://gtl-studio.com/images/tiliaze.png" class="img-responsive" alt="">
                    </a>
                </div>
                <div class="col-sm-4 portfolio-item">
                    <a href="#portfolioModal4" class="portfolio-link" data-toggle="modal">
                        <div class="caption">
                            <div class="caption-content">
                                <i class="fa fa-search-plus fa-3x"></i>
                            </div>
                        </div>
                        <img src="https://gtl-studio.com/images/wowgraph.png" class="img-responsive" alt="" style="height:230px;">
                    </a>
                </div>
                <div class="col-sm-4 portfolio-item">
                    <a href="#portfolioModal5" class="portfolio-link" data-toggle="modal">
                        <div class="caption">
                            <div class="caption-content">
                                <i class="fa fa-search-plus fa-3x"></i>
                            </div>
                        </div>
                        <img src="https://gtl-studio.com/images/starhead.png" class="img-responsive" alt="">
                    </a>
                </div>
                <div class="col-sm-4 portfolio-item">
                    <a href="#portfolioModal6" class="portfolio-link" data-toggle="modal">
                        <div class="caption">
                            <div class="caption-content">
                                <i class="fa fa-search-plus fa-3x"></i>
                            </div>
                        </div>
                        <img src="https://gtl-studio.com/images/coming.png" class="img-responsive" alt="">
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="success" id="about">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <h2>A propos de moi</h2>
                    <hr class="star-light">
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4 col-lg-offset-2">
                    <p>Je suis un jeune développeur qui cherche toujours a s'améliorer et perfectionner sont travail. L'envie d'apprendre et la soif de connaissance dans le milieu du développement sont mes principal source de motiviations.</p>
                </div>
                <div class="col-lg-4">
                    <p>Pour moi le site parfait n'existe pas, c'ets pour cela qu'il faut améliorer et revenir en continue sur le travail déjà éffectuer afin de l'améliorer, l'optimiser. C'est le but premeir de mon cms GTL.</p>
                </div>
                <div class="col-lg-8 col-lg-offset-2 text-center">
                    <a href="/public/PDF/cv.pdf" class="btn btn-lg btn-outline">
                        <i class="fa fa-download"></i> Télécharger le CV
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <h2>Contact</h2>
                    <hr class="star-primary">
                </div>
            </div>
            <div class="row">
                <div class="col-lg-8 col-lg-offset-2">
                    <!-- To configure the contact form email address, go to mail/contact_me.php and update the email address in the PHP file on line 19. -->
                    <!-- The form should work on most web servers, but if the form is not working you may need to configure your web server differently. -->
                    <form name="sentContact"  method="post" action="" novalidate>
                        <div class="row control-group">
                            <div class="form-group col-xs-12 floating-label-form-group controls">
                                <label>Nom/Prénom</label>
                                <input type="text" name="name" class="form-control" placeholder="Nom Prénom" id="name" required data-validation-required-message="Veuillez saisir votre Nom/prénom.">
                                <p class="help-block text-danger"></p>
                            </div>
                        </div>
                        <div class="row control-group">
                            <div class="form-group col-xs-12 floating-label-form-group controls">
                                <label>Adresse email</label>
                                <input type="email" name="email" class="form-control" placeholder="Adresse email" id="email" required data-validation-required-message="Veuillez saisir votre adresse email.">
                                <p class="help-block text-danger"></p>
                            </div>
                        </div>
                        <div class="row control-group">
                            <div class="form-group col-xs-12 floating-label-form-group controls">
                                <label>Message</label>
                                <textarea rows="5" name="message" class="form-control" placeholder="Message" id="message" required data-validation-required-message="Veuillez saisir un message."></textarea>
                                <p class="help-block text-danger"></p>
                            </div>
                        </div>
                        <br>
                        <div id="success"></div>
                        <div class="row">
                            <div class="form-group col-xs-12">
                                <button type="submit" class="btn btn-success btn-lg">Envoyer</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

   