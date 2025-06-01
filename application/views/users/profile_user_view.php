<!-- [ Layout content ] Start -->
<div class="layout-content">

    <!-- [ content ] Start -->
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">Users page</h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url() ?>Home"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item active">Users</li>
            </ol>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <h6 class="card-header">Your Information</h6>
                    <div class="card-body">
                        <form id="form_user_profile">
                            <input type="hidden" name="us_id" id="us_id">
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label class="form-label">User name</label>
                                    <input type="text" class="form-control" placeholder="Name" name="us_name"
                                        id="us_name">
                                    <div class="clearfix"></div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="form-label">Domain</label>
                                    <input type="text" class="form-control" placeholder="Domain" name="us_domain"
                                        id="us_domain">
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label for="us_tel">Número de WhatsApp</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <select name="us_country_code" id="us_country_code" class="custom-select"
                                                required>
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
                            </div>
                            <div class="form-group">
                                <label for="us_address">Dirección</label>
                                <input type="text" name="us_address" id="us_address" class="form-control" required>
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
                                    <input type="text" name="us_region" id="us_region" class="form-control" required>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label class="form-label">Password</label>
                                    <input type="password" class="form-control" name="us_password" id="us_password"
                                        placeholder="Your password">
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control" name="us_password_confirm"
                                        id="us_password_confirm" placeholder="Repeat Your password">
                                </div>
                            </div>
                            <button type="submit" onclick="save_user()" id="btn_save_update"
                                class="btn btn-success">Save</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- [ content ] End -->
</div>
<script>
    var us_id_saved = '<?php echo $us_id;?>';
</script>