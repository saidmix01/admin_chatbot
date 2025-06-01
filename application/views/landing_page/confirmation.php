   <!-- Page Header Start -->
   <div class="page-header container-fluid bg-secondary pt-2 pb-2 mb-5">
       <div class="container py-5">
           <div class="row align-items-center py-4">
               <div class="col-md-6 text-center text-md-left">
                   <h1 class="mb-4 mb-md-0 text-white">Checkout</h1>
               </div>
               <div class="col-md-6 text-center text-md-right">
                   <div class="d-inline-flex align-items-center">
                       <a class="btn text-white" href="">Inicio</a>
                       <i class="fas fa-angle-right text-white"></i>
                       <a class="btn text-white disabled" href="">Checkout</a>
                   </div>
               </div>
           </div>
       </div>
   </div>
   <!-- Page Header Start -->

   <!-- Services Start -->
   <div class="container-fluid">
       <div class="container">
           <div class="row">
               <div class="col-lg-12">
                   <?php if($payment_status === "APPROVED") {?>
                   <section class="container my-5">
                       <div class="card shadow-sm border-success">
                           <div class="card-body text-center">
                               <h1 class="text-success mb-3">
                                   <i class="fas fa-check-circle fa-2x"></i><br>
                                   ¡Gracias por tu compra!
                               </h1>
                               <p class="lead">Tu pago fue procesado con éxito y estamos preparando tu servicio.</p>

                               <hr>

                               <div class="row justify-content-center mt-4">
                                   <div class="col-md-6 text-left">
                                       <p><strong>Referencia:</strong> <?=$reference?></p>
                                       <p><strong>Monto:</strong> $<?= number_format($amount, 0, ',', '.') ?> COP
                                       </p>
                                       <p><strong>Estado:</strong> Pagado/<?=$status?></p>
                                   </div>
                               </div>

                               <a href="<?=base_url()?>" class="btn btn-success mt-4">Volver al inicio</a>
                           </div>
                       </div>
                   </section>
                   <?php }else{?>
                   <section class="container my-5">
                       <div class="card border-danger shadow">
                           <div class="card-body text-center">
                               <h2 class="text-danger mb-4">
                                   <i class="fas fa-times-circle fa-2x"></i><br>
                                   ¡Pago Rechazado!
                               </h2>
                               <p class="lead">Lamentablemente, tu transacción no fue aprobada.</p>
                               <p>Por favor, verifica los datos de tu tarjeta o intenta con otro método de pago.</p>
                               <a href="<?= base_url(); ?>" class="btn btn-outline-danger mt-3">
                                   Ir al inicio
                               </a>
                           </div>
                       </div>
                   </section>

                   <?php } ?>
               </div>
           </div>
       </div>
   </div>
   <!-- Services End -->