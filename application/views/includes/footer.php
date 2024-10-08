 <!-- [ Layout content ] Start -->
 </div>
 <!-- [ Layout container ] End -->
 </div>
 <!-- Overlay -->
 <div class="layout-overlay layout-sidenav-toggle"></div>
 </div>
 <!-- [ Layout wrapper] End -->

 <!-- Core scripts -->
 <script src="<?= base_url() ?>assets/js/pace.js"></script>
 <script src="<?= base_url() ?>assets/js/jquery-3.3.1.min.js"></script>
 <script src="<?= base_url() ?>assets/libs/popper/popper.js"></script>
 <script src="<?= base_url() ?>assets/js/bootstrap.js"></script>
 <script src="<?= base_url() ?>assets/js/sidenav.js"></script>
 <script src="<?= base_url() ?>assets/js/layout-helpers.js"></script>
 <script src="<?= base_url() ?>assets/js/material-ripple.js"></script>
 <!-- Variables -->
 <script>
 	var base_url = '<?php echo base_url(); ?>';
 </script>
 <!-- Libs -->
 <script src="<?= base_url() ?>assets/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
 <script src="<?= base_url() ?>assets/libs/eve/eve.js"></script>
 <script src="<?= base_url() ?>assets/libs/flot/flot.js"></script>
 <script src="<?= base_url() ?>assets/libs/flot/curvedLines.js"></script>
 <script src="<?= base_url() ?>assets/libs/chart-am4/core.js"></script>
 <script src="<?= base_url() ?>assets/libs/chart-am4/charts.js"></script>
 <script src="<?= base_url() ?>assets/libs/chart-am4/animated.js"></script>
 <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
 <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
 <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
 <script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>



 <script src="<?= base_url() ?>js/general.js"></script>
 <!-- My scripts -->
 <?php foreach ($scripts as $key) {
		echo '<script src="' . base_url() . $key . '"></script>';
	} ?>
 </body>

 </html>
