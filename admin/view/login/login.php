
					<div class="card">
					<div class="card-body">
						<form id="loginform" method="post" action="/login/login">
							<div class="card-header align-items-center">
								<div class="row mb-2">
									<h4 class="">EV Charging Display<br/>Admin Portal</h4>
									<span>Login</span>
								</div>
							</div>
							<div class="card-body">
									<input type="hidden" name="redirect" value="<?= isset($_REQUEST['redirect']) ? $_REQUEST['redirect'] : '' ?>" />
									<div class="row mb-2">
										<div class="form-group">
											<!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
											<label class="control-label">Login Id</label>
											<div class="input-group">
												<span class="input-group-text" id="basic-addon11"><i class="fa fa-user"></i></span>
												<input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Login Id" name="username"/>
											</div>
										</div>
									</div>
									<div class="row mb-2">
										<div class="form-group">
											<label class="control-label">Password</label>
											<div class="input-group">
												<span class="input-group-text" id="basic-addon11"><i class="fa fa-lock"></i></span>
												<input class="form-control placeholder-no-fix" type="password" autocomplete="off" placeholder="********" name="password"/>
											</div>
										</div>
									</div>
							</div>
							<div class="card-footer">
								<div class="row mb-2">
										<button type="submit" class="btn btn-primary pull-right">
										Login <i class="m-icon-swapright m-icon-white"></i>
										</button>      
								</div>      
							</div>
						</form>
					</div>
					</div>