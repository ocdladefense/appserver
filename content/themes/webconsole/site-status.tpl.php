<?php


?>
<!-- Row of Headings -->
<div class="row justify-content-center m-2 mb-4">
	<div class="col col-md-4 col-lg-3 font-weight-bold">
			<h4>Name:</h4>
	</div>
	<div class="col col-md-4 col-lg-3 font-weight-bold">
			<h4>Domain:</h4>
	</div>
	<div class="col col-md-2 font-weight-bold text-center">
			<h4>Condition:</h4>
	</div>
	<?php // if(hasAccess($adminUser)): ?>
	<div class="col col-md-2 pl-md-5 font-weight-bold text-center">
			<h4>Details:</h4>
	</div>
	<?php // endif; ?>
</div>

<!-- Loop through every site in the sites.json and print a new row with all info for that site-->
<?php foreach ($sites as $site): ?>
	
	<!-- there will be a new local variable here called $result -->
	
	
	<div class="row justify-content-center m-2">

			<!-- print the name of the site -->
			<div class="col col-md-4 col-lg-3">
				<strong><?php echo $site["name"] ?></strong>
			</div>

			<!-- print the domain of the site -->
			<div class="col col-md-4 col-lg-3">
				<strong><?php echo $site["domain"] ?></strong>
			</div>

			<!-- Create a "Site Status" button that is styled based on the status of the site -->
			<div class="col col-md-2 text-center"> 
				<?php if($site["overallSiteStatus"] == SITE_HEALTHY) : ?>
					<a role="button" class="btn btn-success" href="<?php echo $site["domain"] ?>">Healthy</a>
				<?php else: $allSitesHealthy = false; ?>
					<a role="button" class="btn btn-danger" href="<?php echo $site["domain"] ?>">Critical</a>
			<?php endif; ?>
			</div>


			<!-- If the user is an admin, create an info button that shows additional information -->
			<?php // if(hasAccess($adminUser)) : ?>
					<div class="col col-md-2 pl-md-5 text-center">
							<a role="button" class="btn btn-primary" href="#<?php echo preg_replace('/\s/', '', $site["name"]) ?>" data-toggle="collapse" aria-expanded="false" aria-controls="<?php echo preg_replace('/\s/', '', $site["name"]) ?>">Info</a>
					</div>
					<div class="collapse container" id="<?php echo preg_replace('/\s/', '', $site["name"]) ?>">
							<div class="card card-body bg-light my-3">

									<!-- Row of headings -->
									<div class="row my-2">
											<div class="col">
													<h5>Path Name:</h5>
											</div>
											<div class="col-3 text-center">
													<h5>Expected Status:</h5>
											</div>
											<div class="col-3 text-center">
													<h5>Actual Status:</h5>
											</div>
											<div class="col-2 text-center">
													<h5>Condition:</h5>
											</div>
									</div>

									<!-- Loop through urls and make a new line for each one -->
									<?php foreach ($site["probeResults"] as $probeResult): ?>
										<div class="row my-2">

												<div class="col col-lg-4">
														<?php echo $site["domain"]."$probeResult->path" ?>
												</div>

												<div class="col col-lg-3 text-center font-weight-bold">
														<?php echo "$probeResult->expectedStatusCode" ?>
												</div>

												<div class="col-3 col-lg-3 text-center font-weight-bold">
														<?php echo "$probeResult->actualStatusCode" ?>
												</div>

												<div class="col-2 col-lg-2 text-center"> 
														<?php if($probeResult->expectedStatusCode == $probeResult->actualStatusCode) { ?>
															<a role="button" class="btn btn-success" href="<?php echo $site["domain"]."$probeResult->path" ?>">Healthy</a>
														<?php } else { ?>
															<a role="button" class="btn btn-danger" href="<?php echo $site["domain"]."$probeResult->path" ?>">Critical</a>
														<?php } ?>
												</div>
										</div>    
									<?php endforeach; ?>
							</div> <!-- End of card -->
					</div>
			<?php // endif; ?> <!-- End of admin info button -->
	</div>
<?php endforeach; ?> <!-- End of site loop -->



<div class="row mt-5">
	<div class="col-4"></div>
	<div class="card card-body bg-light col-4">
		<div class="row">
			<div class="col text-center">
					<h3>Overall Status:</h3>
			</div>
		</div>
		<div class="row">	
			<div class="col text-center">
					<?php if($allSitesHealthy) { ?>
							<div class="btn btn-success btn-lg active">
									Healthy
							</div>
					<?php } else { ?>
							<div class="btn btn-danger btn-lg active">
									Critical
							</div>
					<?php } ?>
			</div>
		</div>
	</div>
	<div class="col-4"></div>
</div>