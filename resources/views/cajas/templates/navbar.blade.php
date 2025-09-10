<nav class="navbar navbar-expand-lg bg-gradient-primary border-0">
	<div class="container-fluid">
		<div class="collapse navbar-collapse" id="navbarSupportedContent">
			<!-- Navbar links -->
			<ul class="navbar-nav align-items-center ml-md-auto">
				<li class="nav-item d-xl-none">
					<!-- Sidenav toggler -->
					<div class="pr-3 sidenav-toggler sidenav-toggler-dark" data-action="sidenav-pin" data-target="#sidenav-main">
						<div class="sidenav-toggler-inner">
							<i class="sidenav-toggler-line"></i>
							<i class="sidenav-toggler-line"></i>
							<i class="sidenav-toggler-line"></i>
						</div>
					</div>
				</li>
				<li class="nav-item d-sm-none">
					<a class="nav-link" href="#" data-action="search-show" data-bs-target="#navbar-search-main">
						<i class="ni ni-zoom-split-in"></i>
					</a>
				</li>
			</ul>
			<ul class="navbar-nav align-items-center ml-auto ml-md-0">
				<li class="nav-item dropdown" id="nav-notification">
					<a class="nav-link position-relative" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
						<i class="ni ni-bell-55 fs-5"></i>
						<span class="position-absolute top-1 ml-2 translate-middle badge rounded-pill bg-danger" id="badgeRound">3
							<span class="visually-hidden">notificaciones sin leer</span>
						</span>
					</a>
					<div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" style="min-width: 22rem;">
						<div class="px-3 py-2 border-bottom d-flex justify-content-between align-items-center">
							<h6 class="mb-0 fw-bold">Notificaciones</h6>
							<span class="badge bg-primary rounded-pill" id="badgenum">0 nuevas</span>
						</div>
						<div id='notificationList' class="notification-list" style="max-height: 300px; overflow-y: auto;">
						</div>
						<div class="p-2 border-top text-center">
							<a href="#" class="text-decoration-none" id="btnVerNotificaciones">Ver todas las notificaciones</a>
						</div>
					</div>
				</li>
				<li class="nav-item dropdown">
					<a class="nav-link pr-0" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
						<div class="media align-items-center">
							<span class="avatar avatar-sm rounded-circle">
								<?php echo Tag::image("Mercurio/profile-a.png", "alt: Image placeholder") ?>
							</span>
							<div class="media-body ml-2 d-none d-lg-block">
								<span class="mb-0 text-sm font-weight-normal"><?php echo $user ?></span>
							</div>
						</div>
					</a>
					<div class="dropdown-menu dropdown-menu-right">
						<a href="<?php echo Utils::getKumbiaUrl('login/salir'); ?>" class="dropdown-item">
							<i class="ni ni-user-run"></i>
							<span>Cerrar sesión</span>
						</a>
					</div>
				</li>
			</ul>
		</div>
	</div>
</nav>


<script type='text/template' id='item-notification'>
	<%
	bg_estado = 'bg-warning';
	if (estado == 'P') {
		bg_estado = 'bg-info';
	}
	%>
	<div class="dropdown-item py-3 border-bottom">
		<div class="d-flex">
			<div class="flex-shrink-0 me-3">
				<div class="<%=bg_estado %> bg-opacity-10 p-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
					<i class="ni ni-single-copy-04 text-white"></i>
				</div>
			</div>
			<div class="flex-grow-1">
				<div class="d-flex justify-content-between align-items-center">
					<h6 class="mb-0 fw-bold"><%= titulo %></h6>
					<small class="text-muted"><%= hora %></small>
				</div>
				<p class="mb-0 small text-muted"><%= (estado == 'P') ? 'Tiene una nueva notificación pendiente de lectura' : 'La notificación está leida' %></p>
			</div>
		</div>
	</div>
</script>

<script type="text/javascript">
	$('#btnVerNotificaciones').click(function() {
		window.location.href = "<?php echo Utils::getKumbiaUrl('notificaciones'); ?>";
	});

	function refreshNotificaciones() {
		$.ajax({
			url: "<?php echo Utils::getKumbiaUrl('notificaciones/refresh'); ?>",
			type: "POST",
			dataType: "json",
			data: {}
		}).done(function(response) {
			if (response && response.success == true) {
				$('#notificationList').html('');
				$.each(response.data, function(index, item) {
					let template = _.template(document.getElementById('item-notification').innerHTML);
					$('#notificationList').append(template(item));
				});
				$('#badgenum').text(response.badgenum + ' nuevas');
				$('#badgeRound').text(response.badgenum);
			}
		}).fail(function(data) {
			console.log(data);
		});
	}
	refreshNotificaciones();
	setInterval(refreshNotificaciones, 60000);
</script>