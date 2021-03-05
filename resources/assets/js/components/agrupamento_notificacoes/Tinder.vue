<template>
 <div>

<section class="col-xs-12" style="margin-top: 10px">
    
<section class="col-xs-6">
<div class="box" v-if="id_evento_arquivo != null && mostra_lista"><div class="box-header with-border"></div> 
<div class="box-body">
    <notificacoes :id_load="id_evento_arquivo" :id_agrupamento="id"  :onSelect="onSelect"></notificacoes>


</div>
</div>
    
</section>
<section class="col-xs-6">
<div class="box"><div class="box-header with-border"></div> 
<div class="box-body">


 <visualizar v-if="id != null"  :id_load="id" :tinder=true v-bind:onIndicaStatus="onIndicaStatus"></visualizar>
 <div v-if="id == null && !vazio">
   Carregando..
 </div>
</div>
</div>
</section>

</section>


  <div v-if="vazio">
     <b>Lista de notificações vazia!</b>
  </div>

 </div>
    
</template>
<script>
import visualizar from './Visualizar.vue';
import notificacoes from '../eventos_arquivos/Notificacoes';

export default {
 components: {
    visualizar,
    notificacoes
  },
  data: function() {
    return {
        filtro:{
            acima_de: "",
            limit: 1,
            tinder: 1,
            order: "id_evento_arquivo",
            order_type: "asc"
        },
        id: null,
        id_evento_arquivo: null,
        vazio: false,
        status: "",
        mostra_lista: true,

        form_arquivo: null,
    };
  },
  methods:{

      onSelect( item, index){
          var self = this;

          this.id = null;
          setTimeout(function(){
                    self.id = item.id;

          }, 50);

      },

      onIndicaStatus(form, status){
          var self = this;

          this.mostra_lista = false;

          this.load_data( function() {
                    self.mostra_lista = true;

          } );


      },
      load_arquivo( id_agrupamento ){

                var url = "eventos_arquivos?id_agrupamento=" + id_agrupamento;
                var fn_return = function(resultado){

                    if ( resultado.data.length > 0 ){
                        self.id = resultado.data[0].id;
                        self.status = resultado.data[0].status;
                        self.id_evento_arquivo = resultado.data[0].id_evento_arquivo; 
                    }else{
                        self.vazio = true;
                        self.id_evento_arquivo = null;
                    }

                }
                obj_api.call(url, "GET", this.filtro, fn_return);
      },
      load_data( fn_final ){

          let self = this;

          if ( self.id != null ){
              self.filtro.acima_de = self.id;
          }
          self.id = null;

                    
                var url = "agrupamento_notificacoes_filtro";
                var fn_return = function(resultado){
                    console.log("resultado tinder", resultado);
                    if ( resultado.data.length > 0 ){
                        self.id = resultado.data[0].id;
                        self.id_evento_arquivo = resultado.data[0].id_evento_arquivo;
                        self.status = resultado.data[0].status;

                        if ( fn_final != null && fn_final != undefined ){
                            fn_final();
                        }



                    }else{
                        self.vazio = true;
                    }

                }
                obj_api.call(url, "POST", this.filtro, fn_return);
      }

  },
  mounted(){

      if ( this.$route.query != null ){
          if ( this.$route.query.data != null && this.$route.query.data.toString() != "" ){
              this.filtro.dt_inicio = this.$route.query.data ;
              this.filtro.dt_fim = this.$route.query.data ;
          }
      }

             this.load_data();
  }

}
</script>