
            
          <!-- User Account: style can be found in dropdown.less -->
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                 
                   <img src="{{ config('app.base_template', '/') }}dist/img/avatar5.png" style="max-height: 20px"
              <span class="hidden-xs">{{ $user_info->nome }}</span>
            </a>
            <ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header">
                 
                       <img src="{{ config('app.base_template', '/') }}dist/img/avatar5.png"  class="img-circle" alt="User Image">
                <p>
                  {{ $user_info->nome }}
                          <?php //  {{  Auth::check() ? "Logged In" : "Logged Out"}}  ?>
                  <small>{{ $user_info->email }}</small>
                </p>
              </li>
              <!-- Menu Body -->
              <li class="user-body" style="display: none">
                <div class="row">
                  <div class="col-xs-4 text-center">
                    <a href="#">Followers</a>
                  </div>
                  <div class="col-xs-4 text-center">
                    <a href="#">Sales</a>
                  </div>
                  <div class="col-xs-4 text-center">
                    <a href="#">Friends</a>
                  </div>
                </div>
                <!-- /.row -->
              </li>
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-left">
                  <a href="{{ url('/user') }}/{{ $user_info->id }}" class="btn btn-default btn-flat">Profile</a>
                </div>
                <div class="pull-right">
                  <a href="{{ url('/logout') }}" class="btn btn-default btn-flat">Sign out</a>
                </div>
              </li>
            </ul>
          </li>
          <!-- Control Sidebar Toggle Button -->
          <li style="display: none">
            <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
          </li>