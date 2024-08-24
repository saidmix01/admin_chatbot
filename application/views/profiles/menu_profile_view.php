<!-- [ Layout content ] Start -->
<div class="layout-content">
	<input type="hidden" id="pro_id" name="pro_id" value="<?=$pro_id;?>">
	<!-- [ content ] Start -->
	<div class="container-fluid flex-grow-1 container-p-y">
		<h4 class="font-weight-bold py-3 mb-0">Menu Profile page</h4>
		<div class="text-muted small mt-0 mb-4 d-block breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="<?= base_url() ?>Home"><i class="feather icon-home"></i></a></li>
				<li class="breadcrumb-item"><a href="<?= base_url() ?>Profiles"> Profiles</a></li>
				<li class="breadcrumb-item active">Menu Profile</li>
			</ol>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="card mb-12">
					<div class="card-header">
						<h6 class="card-header-title mb-0">All Menus</h6>
					</div>
					<div class="card-body">
						<form id="form_menu_profiles">
							<div id="checkbox_menus"></div>
							<hr>
							<button type="submit" class="btn btn-success btn-sm" id="btn_save_mp" style="display: none;" onclick="save_men_prof();"> Save</button>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- [ content ] End -->
</div>
