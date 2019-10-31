<?php


?>
<!-- Row of Headings -->
<div class="row m-2 mb-4">
				<div class="col font-weight-bold">
						<h5>Domain Name:</h5>
				</div>
				<div class="col font-weight-bold">
						<h5>Status Code:</h5>
				</div>
				<div class="col font-weight-bold text-center">
						<h5>Condition:</h5>
				</div>
				<?php if(hasAccess($adminUser)): ?>
				<div class="col font-weight-bold text-center">
						<h5>Additional Info:</h5>
				</div>
				<?php endif; ?>
</div>

<!-- Loop through every site in the sites.json and print a new row with all info for that site-->
<?php foreach ($sites->sites as $site): ?>
		
		<!-- there will be a new local variable here called $result -->
		
		
		<div class="row m-2">

				<!-- print the domain of the site -->
				<div class="col">
						<strong><?php echo "$result->domain" ?></strong>
				</div>

				<!-- print the current status of the site -->
				<div class="col">
						<?php echo $request->getStatus() ?>
				</div>

				<!-- Create a "Site Status" button that is styled based on the status of the site -->
				<?php if($site->expectedStatus == $request->getStatus()) : ?>
						<div class="col text-center"> 
								<a role="button" class="btn btn-success" href="<?php echo $site->domain ?>">Online</a>
						</div>
				<?php else: 
						$allSitesHealthy = false;    
				?>
						<div class="col text-center"> 
								<a role="button" class="btn btn-danger" href="<?php echo $site->domain ?>">Offline</a>
						</div>
				<?php endif; ?>

				<!-- If the user is an admin, create an info button that shows additional information -->
				<?php if(hasAccess($adminUser)) : ?>
						<div class="col text-center">
								<a role="button" class="btn btn-primary" href="#<?php echo preg_replace('/\s/', '', $site->name) ?>" data-toggle="collapse" aria-expanded="false" aria-controls="<?php echo preg_replace('/\s/', '', $site->name) ?>">Info</a>
						</div>
						<div class="collapse container" id="<?php echo preg_replace('/\s/', '', $site->name) ?>">
								<div class="card card-body m-3">
										<div class="row m-2 pl-3">
												<strong>Remote Repository:&nbsp;</strong> <?php echo $site->repositoryUrl ?>
										</div>

										<!-- Row of headings -->
										<div class="row m-2 mt-4">
												<div class="col">
														<h6>Path Name:</h6>
												</div>
												<div class="col">
														<h6>Expected Status Code:</h6>
												</div>
												<div class="col">
														<h6>Current Status Code:</h6>
												</div>
												<div class="col text-center">
														<h6>Path Condition:</h6>
												</div>
										</div>

										<!-- Loop through urls and make a new line for each one -->
										<?php foreach ($site->urls as $url): ?>
												<div class="row m-2">
														<div class="col">
																<?php echo "$url->path" ?>
														</div>
														<div class="col">
																<?php echo "$url->expectedStatusCode" ?>
														</div>
														<div class="col">
																<?php echo "$url->actualStatusCode" ?>
														</div>
														<?php if($url->expectedStatusCode == $url->actualStatusCode) { ?>
																<div class="col text-center"> 
																		<a role="button" class="btn btn-success" href="<?php echo $url->path ?>">Healthy</a>
																</div>
														<?php } else { ?>
																<div class="col text-center"> 
																		<a role="button" class="btn btn-danger" href="<?php echo $url->path ?>">Critical</a>
																</div>
														<?php } ?>
												</div>    
										<?php endforeach; ?>
								</div> <!-- End of card -->
						</div>
				<?php endif; ?> <!-- End of admin info button -->
		</div>
<?php endforeach; ?> <!-- End of site loop -->

<div class="row m-2 mt-4">
		<div class="col-8">
				<h2>Overall Status:</h2>
		</div>
		<div class="col-4 text-center">
				<?php if($allSitesHealthy) { ?>
						<div class="btn btn-success">
								Healthy
						</div>
				<?php } else { ?>
						<div class="btn btn-danger">
								Unhealthy
						</div>
				<?php } ?>
		</div>
</div>