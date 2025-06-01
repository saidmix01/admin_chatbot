    <!-- Modal -->
    <div class="modal fade" id="authModal" tabindex="-1" role="dialog" aria-labelledby="authModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="authModalLabel">Bienvenido</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs mb-3" id="authTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="login-tab" data-toggle="tab" href="#login" role="tab">Iniciar
                                sesión</a>
                        </li>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content">
                        <!-- Iniciar sesión -->
                        <div class="tab-pane fade show active" id="login" role="tabpanel">
                            <form id="form_login">
                                <div class="form-group">
                                    <label for="loginEmail">Correo electrónico</label>
                                    <input type="email" class="form-control" name="us_email" id="us_email" required>
                                </div>
                                <div class="form-group">
                                    <label for="loginPassword">Contraseña</label>
                                    <input type="password" name="us_password" class="form-control" id="us_password" required>
                                </div>
                                <button type="submit" onclick="login('form_login');" class="btn btn-primary btn-block">Ingresar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>