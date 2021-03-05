<template>
  <div>
    <div class="content-wrapper" style="min-height: 800px">
      <router-view></router-view>
    </div>

    <footer class="main-footer">
      <div class="pull-right hidden-xs">
        <b>Versão</b> 1.0
      </div>
      <strong>
        Copyright &copy; 2019
        <a v-bind:href="url_sistema">{{nome_sistema}} </a>
      </strong>
    </footer>

    <MenuApp></MenuApp>

    <div id="div_g_errors"></div>
  </div>
</template>
<script>
export default {

      data: function() {
            return {

                     nome_sistema: "MIDIACLIP",
                     url_sistema: "http://midiaclip.com.br"
               }
        },

        mounted(){
          if ( window.K_CLIENTE_NOME != null && window.K_CLIENTE_NOME != undefined  &&  window.K_CLIENTE_NOME != ""){
              this.nome_sistema =  window.K_CLIENTE_NOME;
          }
          if ( window.K_CLIENTE_URL != null &&  window.K_CLIENTE_URL != undefined  && window.K_CLIENTE_URL != ""){
              this.url_sistema =  window.K_CLIENTE_URL;
          }
        }

};
</script>