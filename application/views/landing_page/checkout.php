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
                   <div class="container">
                       <div class="card shadow">
                           <div class="card-header bg-success text-white">
                               <h4 class="mb-0" style="color:#ffff;">Resumen de tu pedido</h4>
                           </div>
                           <div class="card-body">
                               <?php 
                                $ser_id = 0;
                                foreach ($data_service as $key) { 
                                $ser_id = $key->ser_id;
                                ?>
                               <!-- Resumen del pedido -->
                               <div class="mb-4">
                                   <p><strong>Servicio:</strong> <?=$key->ser_name?></p>
                                   <p><strong>Precio:</strong> $<?=$key->ser_price?> / mes</p>
                                   <a href="/planes" class="btn btn-sm btn-outline-secondary">Cambiar servicio</a>
                               </div>
                               <?php }?>
                               <!-- Formulario -->
                               <form id="form_checkout" method="POST" action="<?=base_url()?>checkout/shop_service">
                                   <h5 class="mb-3">Datos del cliente</h5>

                                   <input type="hidden" name="ser_id" id="ser_id" value="<?= $ser_id ?>">

                                   <div class="form-row">
                                       <div class="form-group col-md-6">
                                           <label for="us_name">Nombre completo</label>
                                           <input type="text" class="form-control" name="us_name" id="us_name" required>
                                       </div>
                                       <div class="form-group col-md-6">
                                           <label for="us_email">Correo electrónico</label>
                                           <input type="email" class="form-control" name="us_email" id="us_email"
                                               required>
                                       </div>
                                   </div>

                                   <div class="form-row">
                                       <div class="form-group col-md-6">
                                           <label for="us_tel">Número de WhatsApp</label>
                                           <div class="input-group">
                                               <div class="input-group-prepend">
                                                   <select name="us_country_code" id="us_country_code"
                                                       class="custom-select" required>
                                                       <option value="">+ Código</option>
                                                       <option value="+57" selected>🇨🇴 +57 (Colombia)</option>
                                                       <option value="+1">🇺🇸 +1 (USA)</option>
                                                       <option value="+52">🇲🇽 +52 (México)</option>
                                                       <option value="+54">🇦🇷 +54 (Argentina)</option>
                                                       <option value="+51">🇵🇪 +51 (Perú)</option>
                                                       <option value="+55">🇧🇷 +55 (Brasil)</option>
                                                       <!-- Agrega más si lo necesitas -->
                                                   </select>
                                               </div>
                                               <input type="tel" class="form-control" name="us_tel" id="us_tel"
                                                   placeholder="3001234567" required pattern="[0-9]{7,15}"
                                                   title="Solo números, entre 7 y 15 dígitos">
                                           </div>
                                       </div>

                                       <div class="form-group col-md-6">
                                           <label for="us_domain">Dominio deseado (opcional)</label>
                                           <input type="text" class="form-control" name="us_domain" id="us_domain">
                                       </div>
                                   </div>

                                   <div class="form-group">
                                       <label for="us_comments">Notas adicionales</label>
                                       <textarea class="form-control" name="us_comments" id="us_comments"
                                           rows="3"></textarea>
                                   </div>

                                   <!-- NUEVOS CAMPOS REQUERIDOS POR WOMPI -->
                                   <div class="form-row">
                                       <div class="form-group col-md-6">
                                           <label for="us_doc_type">Tipo de documento</label>
                                           <select name="us_doc_type" id="us_doc_type" class="form-control" required>
                                               <option value="">Seleccione...</option>
                                               <option value="CC">Cédula de ciudadanía</option>
                                               <option value="CE">Cédula de extranjería</option>
                                               <option value="TI">Tarjeta de identidad</option>
                                               <option value="NIT">NIT</option>
                                           </select>
                                       </div>
                                       <div class="form-group col-md-6">
                                           <label for="us_doc_number">Número de documento</label>
                                           <input type="text" name="us_doc_number" id="us_doc_number"
                                               class="form-control" required>
                                       </div>
                                   </div>

                                   <div class="form-group">
                                       <label for="us_address">Dirección</label>
                                       <input type="text" name="us_address" id="us_address" class="form-control"
                                           required>
                                   </div>
                                   <div class="form-group">
                                       <label for="us_country">País</label>
                                       <select name="us_country" id="us_country" class="form-control" required>
                                           <option value="">Seleccione su país</option>
                                           <option value="CO" selected>Colombia</option>
                                           <option value="US">Estados Unidos</option>
                                           <option value="MX">México</option>
                                           <option value="AR">Argentina</option>
                                           <option value="PE">Perú</option>
                                           <option value="CL">Chile</option>
                                           <option value="BR">Brasil</option>
                                           <!-- Agrega más países si lo necesitas -->
                                       </select>
                                   </div>
                                   <div class="form-row">
                                       <div class="form-group col-md-6">
                                           <label for="us_city">Ciudad</label>
                                           <input type="text" name="us_city" id="us_city" class="form-control" required>
                                       </div>
                                       <div class="form-group col-md-6">
                                           <label for="us_region">Departamento / Región</label>
                                           <input type="text" name="us_region" id="us_region" class="form-control"
                                               required>
                                       </div>
                                   </div>

                                   <button type="submit" class="btn btn-success btn-block"
                                       >Realizar pedido</button>
                               </form>
                           </div>
                       </div>
                   </div>

               </div>
           </div>
       </div>
   </div>
   <!-- Services End -->