<?php echo $this->renderWidget('core::notification'); ?>
<div class="main">
	<main class="content p-0 ">
		<div class="container-fluid p-0">
			<div class="row justify-content-center mx-auto">
				<div class="col-12 p-0">
					<div class="card border-0 shadow-none">
						<div class="card-body">
						<?php echo $this->renderWidget('core::message');?>
                        <div class="row align-items-center">
								<?php echo $this->render('backend.version.list.filter', []);?>
							</div>
							<div class="row align-items-center">
								<?php echo $this->render('backend.version.form', []);?>
							</div>
							<form action="<?php echo $this->link_list ?>" method="POST" id="formList">
								<input type="hidden" value="<?php echo $this->token ?>" name="token">
            					<input type="hidden" value="DELETE" name="_method">
								<table id="datatables-buttons" class="table table-striped border-top border-1" style="width:100%">
									<thead>
										<tr>
											<th width="10px">
												<input type="checkbox" id="select_all">
											</th>
											<th>Title</th>
											<th>Version</th>
											<th>Description</th>
											<th>Change Logs</th>
											<th>Release Date</th>
											<th>Number of Feedback</th>
											<th>Action</th>
										</tr>
									</thead>
									<tbody>
										<?php while($this->list->hasRow()) echo $this->render('backend.version.list.row', []); ?> 
									</tbody>
								<?php
								?>
								</table>
							</form>
							<div class="row g-3 align-items-center">
								<?php echo $this->renderWidget('core::pagination'); ?>
							</div>
						</div>
					</div>
				</div>
			</div>

		</div>
	</main>


</div>
</div>