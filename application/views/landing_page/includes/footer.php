<!-- Footer Start -->
<div class="container-fluid bg-primary text-white mt-5 pt-5 px-sm-3 px-md-5">
    <div class="row pt-5">
        <div class="col-lg-4 col-md-6 mb-5">
            <a href="">
                <h3 class="text-secondary mb-3"><span class="text-white">WebCol</span>Soluciones</h3>
            </a>
            <p>En Webcolsoluciones te ofrecemos soluciones digitales reales, con atención directa, precios accesibles y
                tecnología desde nuestro propio servidor.</p>
            <div class="d-flex justify-content-start mt-4">
                <a class="btn btn-outline-light rounded-circle text-center mr-2 px-0" style="width: 38px; height: 38px;"
                    href="#"><i class="fab fa-twitter"></i></a>
                <a class="btn btn-outline-light rounded-circle text-center mr-2 px-0" style="width: 38px; height: 38px;"
                    href="#"><i class="fab fa-facebook-f"></i></a>
                <a class="btn btn-outline-light rounded-circle text-center mr-2 px-0" style="width: 38px; height: 38px;"
                    href="#"><i class="fab fa-linkedin-in"></i></a>
                <a class="btn btn-outline-light rounded-circle text-center mr-2 px-0" style="width: 38px; height: 38px;"
                    href="#"><i class="fab fa-instagram"></i></a>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 mb-5">
            <h4 class="text-white mb-4">Ponte en contacto</h4>
            <p><i class="fa fa-map-marker-alt mr-2"></i>Bucaramanga Santander Colombia</p>
            <p><i class="fa fa-phone-alt mr-2"></i>+012 345 67890</p>
            <p><i class="fa fa-envelope mr-2"></i>info@example.com</p>
        </div>
        <div class="col-lg-4 col-md-6 mb-5">
            <h4 class="text-white mb-4">Enlaces</h4>
            <div class="d-flex flex-column justify-content-start">
                <a class="text-white mb-2" href="<?=base_url()?>"><i class="fa fa-angle-right mr-2"></i>Inicio</a>
                <a class="text-white mb-2" href="<?=base_url()?>servicios"><i class="fa fa-angle-right mr-2"></i>Planes y Servicios</a>
                <a class="text-white mb-2" href="<?=base_url()?>faq"><i class="fa fa-angle-right mr-2"></i>FAQ</a>
                <a class="text-white" href="<?=base_url()?>legal"><i class="fa fa-angle-right mr-2"></i>Legal</a>
                <a class="text-white" href="<?=base_url()?>contacto"><i class="fa fa-angle-right mr-2"></i>Contacto</a>
                <a class="text-white mb-2" href="#" data-toggle="modal" data-target="#authModal"><i class="fa fa-angle-right mr-2"></i>Iniciar Sesion</a>
            </div>
        </div>
    </div>
</div>
<div class="container-fluid bg-dark text-white py-4 px-sm-3 px-md-5">
    <p class="m-0 text-center text-white">
        &copy; <a class="text-white font-weight-medium" href="#">Webcolsoluciones</a>. Todos los derechos Reservados.
        Diseñado
        por
        <a class="text-white font-weight-medium" href="<?=base_url()?>">Webcolsoluciones</a>
    </p>
</div>
<!-- Footer End -->


<!-- Back to Top -->
<a href="#" class="btn btn-lg btn-primary back-to-top"><i class="fa fa-angle-double-up"></i></a>


<!-- JavaScript Libraries -->
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
<script src="<?=base_url()?>landing_page/lib/easing/easing.min.js"></script>
<script src="<?=base_url()?>landing_page/lib/waypoints/waypoints.min.js"></script>
<script src="<?=base_url()?>landing_page/lib/counterup/counterup.min.js"></script>
<script src="<?=base_url()?>landing_page/lib/owlcarousel/owl.carousel.min.js"></script>

<!-- Contact Javascript File -->
<script src="<?=base_url()?>landing_page/mail/jqBootstrapValidation.min.js"></script>
<script src="<?=base_url()?>landing_page/mail/contact.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Template Javascript -->
<script src="<?=base_url()?>landing_page/js/main.js"></script>
<!-- Variables -->
<script>
var base_url = '<?php echo base_url();?>';
</script>
 <script src="<?= base_url() ?>js/general.js"></script>
<script src="<?=base_url()?>js/login.js"></script>
<?php
     if(isset($scripts)){
         foreach ($scripts as $key) {
             echo '<script src="' . base_url() . $key . '"></script>';
        } 
     } 
    ?>
</body>
</body>

</html>