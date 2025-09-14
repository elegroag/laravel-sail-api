  <div class="wrapper">
      <div class="sidebar" data-color="black" data-image="/Online/public/img/Online/sidebar-4.jpg">
          <div class="sidebar-wrapper">
              <div class="logo">
                  <a href="https://www.syseu.com" class="simple-text logo-mini">
                      OL
                  </a>
                  <a href="https://www.syseu.com" class="simple-text logo-normal">
                      Online Adm
                  </a>
              </div>
              <div class="user">
                  <div class="photo">
                      <img src="/Online/public/img/Online/logo.jpg">
                  </div>
                  <div class="info ">
                      <a data-bs-toggle="collapse" href="#collapseExample" class="collapsed" aria-expanded="false">
                          <span>
                              <?php
                                $user = Auth::getActiveIdentity();
                                $user = explode(" ", $user['nombre']);
                                $user = strtolower($user[0] . " " . $user[1]);
                                echo $user;
                                ?>
                              <b class="caret"></b>
                          </span>
                      </a>
                      <div class="collapse" id="collapseExample" style="height: auto;">
                          <!--
                            <ul class="nav">
                                <li>
                                    <a class="profile-dropdown" href="#pablo">
                                        <span class="sidebar-mini">EP</span>
                                        <span class="sidebar-normal">Edit Profile</span>
                                    </a>
                                </li>
                            </ul>
                            --!>
                        </div>
                    </div>
                </div>
                <ul class="nav">
                    <?php list($menu, $migas) =  Menu::showMenu(); ?>
                    <?php echo $menu; ?>
                </ul>
            </div>
        </div>
        <div class="main-panel">
            <!-- Navbar -->
                          <nav class="navbar navbar-expand-lg ">
                              <div class="container-fluid">
                                  <div class="navbar-wrapper">
                                      <div class="navbar-minimize">
                                          <button id="minimizeSidebar" class="btn btn-black btn-fill btn-round btn-icon d-none d-lg-block">
                                              <i class="fa fa-ellipsis-v visible-on-sidebar-regular"></i>
                                              <i class="fa fa-navicon visible-on-sidebar-mini"></i>
                                          </button>
                                      </div>
                                      <a class="navbar-brand"> <?php echo (isset($title)) ? $title : "Sin Titulo"; ?> </a>
                                  </div>
                                  <button class="navbar-toggler navbar-toggler-right" type="button" data-bs-toggle="collapse" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
                                      <span class="navbar-toggler-bar burger-lines"></span>
                                      <span class="navbar-toggler-bar burger-lines"></span>
                                      <span class="navbar-toggler-bar burger-lines"></span>
                                  </button>
                                  <div class="collapse navbar-collapse justify-content-end">
                                      <div class="divider"></div>
                                      <a href="/Online/Online/login/salir" class="dropdown-item text-danger">
                                          <i class="nc-icon nc-button-power"></i> Salir
                                      </a>
                                  </div>
                                  </li>
                                  </ul>
                              </div>
                      </div>
                      </nav>
                      <!-- End Navbar -->
                      <div class="content-main">
                          <div class="container-fluid">
                              <div class="section">
                                  <?php  ?>
                              </div>
                          </div>
                      </div>
                      <footer class="footer">
                          <div>
                              <nav aria-label="breadcrumb">
                                  <ol class="breadcrumb" style="background-color: white;">
                                      <?php echo $migas; ?>
                                  </ol>
                              </nav>
                          </div>
                      </footer>
                  </div>
              </div>
