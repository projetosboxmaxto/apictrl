<template>
  <span>
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar" style="min-height: 100%">
      <!-- Sidebar user panel -->
      <div class="user-panel" style="display: none">
        <div class="pull-left image">
          <img v-bind:src="img_user | template_url" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
          <p>Alexander Pierce</p>
          <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
        </div>
      </div>
      <!-- search form -->
      <!--
      <form action="#" method="get" class="sidebar-form">
        <div class="input-group">
          <input type="text" name="q" class="form-control" placeholder="Search...">
          <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
        </div>
      </form>
    -->
      <!-- /.search form -->
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header">MENU DE NAVEGAÇÃO</li>
        
        <li v-for="(item, index) in items" v-if="item.menu" v-bind:class="menuactive(item.name, index)">
                 <router-link :to="{ name: item.name }"><i :class="item.icon"></i>
                       <span v-html="item.title"> </span></router-link>
            
        </li>

      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>

  <!-- /.control-sidebar -->
  <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
  <div class="control-sidebar-bg"></div>
 </span>

  </template>
<script>
    export default {

        data: function() {
            return {

                     img_user: "dist/img/user2-160x160.jpg",
                     img_mostra_menu: false,
                     items: []
               }
        },
        
        mounted(){
            console.log("router"); console.log( this.$router );
            this.items = this.$router.options.routes;
        },

        methods: {
            
              menuactive(route, index){
    
          
                  if ( index == 0 && ( this.$route.name =="home" || this.$route.name == null ) ){
                                    console.log("Router nome: " + this.$route.name );
                        return "active";
                  }
                  
                 // console.log("path? " + this.$route.name  );
                 // console.log(this.$router.options.routes);
                     if (  this.$route.name == route ){
                         return "active";
                     }
                     
                     return "";
              },

              clickMenuPage (){

                    this.img_mostra_menu = !this.img_mostra_menu;
                    //console.log("img mostra menu? " + this.img_mostra_menu);
              },
              getStyleMenuPage(){

                    if ( this.img_mostra_menu )
                         return "display: block";

                   return "display:none";    
              }

                //clickMenuPage  getStyleMenuPage

        }

    }
</script>