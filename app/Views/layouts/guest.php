<!DOCTYPE html>
<html>

<head>
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!--favicon-->
	<link rel="shortcut icon" type="image/x-icon" href="<?php echo base_url('assets/images/logo.png'); ?>">
	<!--plugins-->
	<!-- loader-->
	<link href="<?php echo base_url('assets/css/pace.min.css'); ?>" rel="stylesheet" />
	<script src="<?php echo base_url('assets/js/pace.min.js'); ?>"></script>
	<!-- Bootstrap CSS -->
	<link href="<?php echo base_url('assets/css/bootstrap.min.css'); ?>" rel="stylesheet">
	<link href="<?php echo base_url('assets/css/bootstrap-extended.css'); ?>" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
	<link href="<?php echo base_url('assets/css/app.css'); ?>" rel="stylesheet">
	<link href="<?php echo base_url('assets/css/icons.css'); ?>" rel="stylesheet">
	<title>Acceso al sistema</title>
</head>

<body class="">
	<!--wrapper-->
	<div class="wrapper">
		<div class="section-authentication-signin d-flex align-items-center justify-content-center my-5 my-lg-0">
			<div class="container-fluid">
				<div class="row min-vh-100">
					<!-- COLUMNA IZQUIERDA: FONDO + TEXTO BIENVENIDA -->
					<div class="col-lg-7 d-none d-lg-flex p-0">
						<div class="w-100 h-100" style="background-color: #ffffff; overflow: hidden;">
							<img src="<?php echo base_url('assets/images/fondo.jpg'); ?>"
								 alt="InterMet"
								 style="width: 100%; height: 100%; object-fit: cover; object-position: 20% center;">
						</div>
					</div>

					<!-- COLUMNA DERECHA: LOGIN -->
					<div class="col-lg-5 d-flex align-items-center justify-content-center">
						<div class="col mx-auto" style="max-width: 400px;">
							<div class="card">
								<div class="card-body">
									<div class="border p-4 rounded">

										<div class="d-flex justify-content-between align-items-center">
											<img src="<?php echo base_url('assets/images/logo.png'); ?>" width="100" alt="Logo" />
											<h6 class="mb-0"><?= $this->renderSection('title') ?></h6>
										</div>


										<?php if (!empty(session()->getFlashdata('error'))) { ?>
											<div class="alert border-0 border-start border-5 border-danger alert-dismissible fade show py-2">
												<div class="d-flex align-items-center">
													<div class="font-35 text-danger"><i class='bx bxs-check-circle'></i></div>
													<div class="ms-3">
														<div><?php echo session()->getFlashdata('error'); ?></div>
													</div>
												</div>
												<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
											</div>
										<?php } ?>

                                        <?php if (!empty(session()->getFlashdata('success'))) { ?>
											<div class="alert border-0 border-start border-5 border-success alert-dismissible fade show py-2">
												<div class="d-flex align-items-center">
													<div class="font-35 text-success"><i class='bx bxs-check-circle'></i></div>
													<div class="ms-3">
														<div><?php echo session()->getFlashdata('success'); ?></div>
													</div>
												</div>
												<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
											</div>
										<?php } ?>

										<?= $this->renderSection('content') ?>

									</div> <!-- border -->
								</div> <!-- card-body -->
							</div> <!-- card -->
						</div> <!-- col mx-auto -->
					</div> <!-- col-lg-5 -->
				</div> <!-- row -->
			</div> <!-- container-fluid -->
		</div> <!-- section-authentication-signin -->
	</div> <!-- wrapper -->

	<!-- Overlay de carga -->
<div id="loading-overlay" style="display:none; position:fixed; inset:0; background:rgba(255,255,255,0.98); backdrop-filter: blur(4px); z-index:9999; flex-direction:column; align-items:center; justify-content:center;">
	<div class="loader-ring-wrapper">
		<svg class="loader-ring" width="200" height="200" viewBox="0 0 200 200">
			<circle cx="100" cy="100" r="88" fill="none" stroke="#e8ecef" stroke-width="6"/>
			<circle cx="100" cy="100" r="88" fill="none" stroke="url(#gradient)" stroke-width="6"
					stroke-linecap="round" stroke-dasharray="553" stroke-dashoffset="415" class="loader-arc"/>
			<defs>
				<linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="100%">
					<stop offset="0%" stop-color="#0f2027"/>
					<stop offset="100%" stop-color="#2c5364"/>
				</linearGradient>
			</defs>
		</svg>
		<img src="<?php echo base_url('assets/images/logo.png'); ?>" class="loader-logo" alt="InterMet">
	</div>
	<p class="loader-text mt-4">Verificando credenciales</p>
</div>

<style>
.loader-ring-wrapper {
	position: relative;
	width: 200px;
	height: 200px;
	display: flex;
	align-items: center;
	justify-content: center;
	animation: fadeScaleIn 0.5s ease forwards;
}
.loader-ring {
	position: absolute;
	top: 0;
	left: 0;
	animation: rotate 1.4s linear infinite;
}
.loader-arc {
	transform-origin: center;
}
.loader-logo {
	width: 90px;
	height: 90px;
	object-fit: contain;
	animation: pulseLogo 1.8s ease-in-out infinite;
}
.loader-text {
	color: #5a6570;
	font-size: 1.25rem;
	letter-spacing: 0.5px;
	animation: fadeScaleIn 0.6s ease 0.15s both;
}

@keyframes rotate {
	from { transform: rotate(0deg); }
	to { transform: rotate(360deg); }
}
@keyframes pulseLogo {
	0%, 100% { opacity: 0.6; transform: scale(0.95); }
	50% { opacity: 1; transform: scale(1.05); }
}
@keyframes fadeScaleIn {
	from { opacity: 0; transform: scale(0.85); }
	to { opacity: 1; transform: scale(1); }
}
</style>
	<!--end wrapper-->
	<!-- Bootstrap JS -->
	<script src="<?php echo base_url('assets/js/bootstrap.bundle.min.js'); ?>"></script>
	<!--plugins-->
	<script src="<?php echo base_url('assets/js/jquery.min.js'); ?>"></script>
	<script src="<?php echo base_url('assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js'); ?>"></script>
	<!--Password show & hide js -->
	<?= $this->renderSection('script') ?>
</body>