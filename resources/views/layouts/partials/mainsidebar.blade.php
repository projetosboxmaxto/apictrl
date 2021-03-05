<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar user panel -->
      <div class="user-panel">
        <div class="pull-left image">
          <img src="{{ config('app.base_template', '/') }}dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
          <p>Alexander Pierce</p>
          <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
        </div>
      </div>
      <!-- search form -->
      <form action="#" method="get" class="sidebar-form">
        <div class="input-group">
          <input type="text" name="q" class="form-control" placeholder="Search...">
          <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
        </div>
      </form>
      <!-- /.search form -->
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header">MAIN NAVIGATION</li>
        
         <li class="treeview">
               <a href="{{ config('app.base_path', '/') }}banner">
                       <i class="fa fa-file"></i> 
                      <span>Banner Rotativo</span> 
                         <span class="pull-right-container">
              <span class="label label-primary pull-right">4</span>
            </span>
               </a>
 
        </li>
     
        <li class="treeview">
               <a href="{{ config('app.base_path', '/') }}posts">
                <i class="fa fa-file"></i> 
                <span>Notícias</span>  </a>
               
        </li>

        <li class="treeview">
          <a href="#">
            <i class="fa fa-html5"></i>
            <span>Páginas</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">

                     <li>
                           <a href="{{ config('app.base_path', '/') }}post?id=2&post_type=page"> Cine Roxy</a>

                           <a href="{{ config('app.base_path', '/') }}post?id=3&post_type=page"> Programação</a>

                           <a href="{{ config('app.base_path', '/') }}post?id=4&post_type=page">Em Breve</a>

                            <a href="{{ config('app.base_path', '/') }}post?id=5&post_type=page">
                                Escola
                              </a>

                            <a href="{{ config('app.base_path', '/') }}post?id=6&post_type=page"> Preços</a>

                            <a href="{{ config('app.base_path', '/') }}post?id=7&post_type=page"> Meia Entrada</a>

                            <a href="{{ config('app.base_path', '/') }}post?id=8&post_type=page"> Contato</a>

                        </li>

          </ul>
        </li>

         @if (false )
          <li class="posts_page">
               <a href="{{ config('app.base_path', '/') }}posts?post_type=page"><i class="fa fa-html5"></i> Páginas</a>

               <ul>
                        <li>
                           <a href="{{ config('app.base_path', '/') }}posts?post_type=page"><i class="fa fa-html5"></i> Páginas</a>

                        </li>
               </ul>
 
        </li>
        @endif 

        <li class="posts_page">
               <a href="{{ config('app.base_path', '/') }}midia"><i class="fa fa-picture-o"></i> <span>Mídia</span> </a>
 
        </li>




      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>

  <!-- /.control-sidebar -->
  <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
  <div class="control-sidebar-bg"></div>