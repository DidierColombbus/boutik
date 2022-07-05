            </div>
        </main>
        <!-- Footer -->
        <footer class="bg-dark page-footer font-small blue pt-4">

        <!-- Footer Links -->
        <div class="container-fluid text-center text-md-left">

            <!-- Grid row -->
            <div class="row">

            <!-- Grid column -->
            <div class="col-md-6 mt-md-0 mt-3">

                <!-- Content -->
                <h5 class="text-white text-uppercase">Footer Content</h5>
                <p class="text-white">Here you can use rows and columns to organize your footer content.</p>

            </div>
            <!-- Grid column -->

            <hr class="clearfix w-100 d-md-none pb-3">

            <!-- Grid column -->
            <div class="col-md-3 mb-md-0 mb-3">

                <!-- Links -->
                <h5 class="text-white text-uppercase">Links</h5>

                <ul class="list-unstyled">
                <li>
                    <a href="index.php?categorie=chemise" class="text-white">Nos belles chemises</a>
                </li>
                <li>
                    <a href="index.php?categorie=tshirt" class="text-white">T-shirt tendance</a>
                </li>
                <li>
                    <a href="index.php?categorie=pull" class="text-white">Pull pour l'hiver</a>
                </li>
                </ul>

            </div>
            <!-- Grid column -->

            <!-- Grid column -->
            <div class="col-md-3 mb-md-0 mb-3">

                <!-- Links -->
                <h5 class="text-white text-uppercase">Links</h5>

                <ul class="list-unstyled">
                <li>
                    <a href="index.php" class="text-white">Boutique</a>
                </li>
                <li>
                    <a href="panier.php" class="text-white">Panier</a>
                </li>

                <?php if(internauteEstConnecte()) { ?>
                    <li>
                        <a href="profil.php" class="text-white">Mon profil</a>
                    </li>
                <?php } else { ?>
                    <li>
                        <a href="connexion.php" class="text-white">Connexion</a>
                    </li>
                    <li>
                        <a href="inscription.php" class="text-white">Inscription</a>
                    </li>
                <?php } ?>
                </ul>

            </div>
            <!-- Grid column -->

            </div>
            <!-- Grid row -->

        </div>
        <!-- Footer Links -->

        <!-- Copyright -->
        <div class="text-white footer-copyright text-center py-3">© 2022 Copyright:
            <a href="" class="text-white"> ***</a>
        </div>
        <!-- Copyright -->

        </footer>
        <!-- Footer -->

    </div>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.5/dist/umd/popper.min.js" integrity="sha384-Xe+8cL9oJa6tN/veChSP7q+mnSPaj5Bcu9mPX5F5xIGE0DVittaqT5lorf0EI7Vk" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
    <script src="js/app.js"></script>
</body>
</html>