<template >
 <div>
     <h4 v-if="items.length > 0 ">
           {{items[0].emissora +" - " + items[0].programa +" - " + items[0].hora_inicio }}
     </h4>
    <table class="table table-striped table-bordered">
    <thead>
           <tr>
                <th>Palavras</th>  
                <th>Clientes</th>  
                <th style="width: 30px"></th>      

           </tr>

    </thead>
        
        <tbody v-if="items != null ">
            <tr v-for="(item,index) in items" :key="index"
            
                 v-bind:class="id_agrupamento != null && id_agrupamento == item.id ? 'bg-light-blue-active': ''">
                  <td>{{item.palavras}}</td>
                  <td>{{item.clientes}}</td>

                
                <td style="width: 30px"><a v-bind:style="id_agrupamento != null && id_agrupamento == item.id ? 'cursor:pointer; color: white': 'cursor:pointer' "
                    v-on:click="open_agrupamento(item, index)">
                 <span class="glyphicon glyphicon-play-circle"></span></a></td>
                </tr>
    </tbody>
</table>
 </div>   

</template>
<script>

export default {
  components: {
  },
  props: ["id_load",  "onSelect", "id_agrupamento"],
  data: function() {
    return {
      items: [],
      current: null,
       filtro:{
            acima_de: "",
            tinder: 1,
            order: "id",
            order_type: "asc",
            id_evento_arquivo: ""
        },
    };
  },

  methods:{
      open_agrupamento(item, index){

          if ( this.onSelect != null ){
              this.onSelect(item);
          }

      }
  },

  mounted() {
      var self = this;

      if ( this.id_load != null && this.id_load != undefined ){

          console.log("agrupamento filtro? ");
                var url = "agrupamento_notificacoes_filtro";
                var fn_return = function(resultado){
                    console.log("agrupamento filtro", resultado.data );
                    self.items = resultado.data;

                }
                this.filtro.id_evento_arquivo = this.id_load;
                obj_api.call(url, "POST", this.filtro, fn_return);



      }
  }
}
</script>