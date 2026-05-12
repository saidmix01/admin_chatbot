
            </div>
            <!-- / Main content -->
        </div>
    </div>

    <!-- Core scripts -->
    <script src="<?=base_url()?>assets/js/pace.js"></script>
    <script src="<?=base_url()?>assets/js/jquery-3.3.1.min.js"></script>
    <script src="<?=base_url()?>assets/libs/popper/popper.js"></script>
    <script src="<?=base_url()?>assets/js/bootstrap.js"></script>
    <script src="<?=base_url()?>assets/js/sidenav.js"></script>
    <script src="<?=base_url()?>assets/js/layout-helpers.js"></script>
    <script src="<?=base_url()?>assets/js/material-ripple.js"></script>

    <script>var base_url = '<?=base_url()?>';</script>

    <!-- Global libs -->
    <script src="<?=base_url()?>js/general.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <?php if(!empty($scripts)): ?>
    <?php foreach($scripts as $script): ?>
    <script src="<?=base_url()?><?=$script?>"></script>
    <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>