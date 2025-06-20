	<?php require_once 'app/views/templates/headerPublic.php'; ?>
<!-- Login form view for user authentication 
invalid or valid -->

	<main role="main" class="container">
		<div class="page-header" id="banner">
			<div class="row">
				<div class="col-lg-12">
					<h1>You are not logged in</h1>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-auto">
				<form action="/login/verify" method="post">
					<fieldset>
						<div class="form-group">
							<label for="username">Username</label>
							<input required type="text" class="form-control" name="username" placeholder="Enter username">
						</div>

						<div class="form-group">
							<label for="password">Password</label>
							<input required type="password" class="form-control" name="password" placeholder="Enter password">
						</div>

						<button type="submit" class="btn btn-primary">Login</button>
					</fieldset>
				</form>

				<?php if (isset($_SESSION['failedAuth']) && $_SESSION['failedAuth'] > 0): ?>
					<p class="text-danger mt-2">Login failed. Attempt <?= $_SESSION['failedAuth'] ?></p>
				<?php endif; ?>

				<p class="mt-3">
					Don't have an account?
					<a href="/create">Create an account</a>
				</p>
			</div>
		</div>
	</main>

	<?php require_once 'app/views/templates/footer.php'; ?>
<!-- create account link -->