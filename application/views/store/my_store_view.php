<!-- [ Layout content ] Start -->
<div class="layout-content">

	<!-- [ content ] Start -->
	<div class="container-fluid flex-grow-1 container-p-y">
		<h4 class="font-weight-bold py-3 mb-0">My Store</h4>
		<div class="text-muted small mt-0 mb-4 d-block breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="#"><i class="feather icon-home"></i></a></li>
				<li class="breadcrumb-item active">My Store</li>
			</ol>
		</div>
		<div class="row">
			<div class="col-md-6">
				<div class="card mb-4" onclick="show_modal_store_info('<?=$sto_id?>','<?=$us_id ?>')">
					<div class="card-body">
						<div class="d-flex align-items-center justify-content-between">
							<div class="">
								<h2 class="mb-2"> Store Information </h2>
								<p class="text-muted mb-0">Update your store Information</p>
							</div>
							<div class="lnr lnr-store display-4 text-primary"></div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="card mb-4">
					<div class="card-body">
						<div class="d-flex align-items-center justify-content-between">
							<div class="">
								<h2 class="mb-2">View Chats</h2>
								<p class="text-muted mb-0"><span class="badge badge-success">20</span> Chats</p>
							</div>
							<div class="lnr lnr-bubble display-4 text-success"></div>
						</div>
					</div>
				</div>
			</div>
		</div>

	</div>
	<!-- [ content ] End -->
</div>

<!-- Modal My store-->
<div class="modal fade" id="update_my_store_info" tabindex="-1" aria-labelledby="sto_info" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="sto_info">Update Information</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="form_store_modal">
			<input type="hidden" name="sto_id" id="sto_id">
			<div class="form-row">
				<div class="col-md-3">
					<label for="sto_name">Name store</label>
					<input type="text" class="form-control" placeholder="Name" id="sto_name" name="sto_name">
				</div>
				<div class="col-md-3">
					<label for="sto_direction">Address store</label>
					<input type="text" class="form-control" placeholder="Address" id="sto_direction" name="sto_direction">
				</div>
				<div class="col-md-3">
					<label for="sto_email">Email store</label>
					<input type="text" class="form-control" placeholder="" id="sto_email" name="sto_email">
				</div>
				<div class="col-md-3">
					<label for="sto_direction">Phone store</label>
					<input type="number" class="form-control" placeholder="Phone" id="sto_phone" name="sto_phone">
				</div>
			</div>
			<div class="form-row">
				<div class="col-md-12">
					<label for="sto_name">Wellcome Message</label>
					<textarea class="form-control" placeholder="Write something" name="sto_wellcome_message" id="sto_wellcome_message"></textarea>
					<small class="form-text text-muted">Max 200 characteres</small>
				</div>
			</div>
		
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary" onclick="update()">Update changes</button>
		</form>
      </div>
    </div>
  </div>
</div>
