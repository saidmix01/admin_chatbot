<!-- [ Layout content ] Start -->
<div class="layout-content">

	<!-- [ content ] Start -->
	<div class="container-fluid flex-grow-1 container-p-y">
		<h4 class="font-weight-bold py-3 mb-0"><?=$module_name?></h4>
		<div class="text-muted small mt-0 mb-4 d-block breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="#"><i class="feather icon-home"></i></a></li>
				<li class="breadcrumb-item active"><?=$module_name?></li>
			</ol>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="card mb-12">
					<div class="card-body">
						<button class="btn btn-primary" onclick="open_modal('modal_new_question')">New Question</button>
					</div>
				</div>
			</div>
		</div>
		<div class="row" id="content_questions">
			<div class="col-sm-12" id="user_question_content">
				<!-- <div class="accordion" id="accordionExample">
					<div class="card">
						<div class="card-header" id="headingOne">
						<h2 class="mb-0">
							<button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
							Collapsible Group Item #1
							</button>
						</h2>
						</div>
	
						<div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">
							<div class="card-body">
								
							</div>
						</div>
					</div>
				</div> -->
			</div>
		</div>
	</div>
	<!-- [ content ] End -->
</div>

<?php $this->load->view('questions/modal_new_question');?>
