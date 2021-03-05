<template>
    <div>Aqui ???
        <button type="button"
                v-on:click="corrige" v-if="exibe"
                class="btn btn-default">Salvar</button>
    </div>
    
</template>
<script>
    export default {
            
              data: function() {
                    return {
                         items: [],
                         exibe: false

                    }
                },
            
             mounted() {
                 
                 let self = this;
                 //self.msg_filme = "..Carregando";
                 //this.exibe_carregando = true;
         
                 var fn_return = function (options ){

                           console.log("Retornando os filmes em breve..");
                            console.log(options);
                          self.items = options;
                          self.exibe = true;
                        //  self.msg_filme = "--";
                  }


	         obj_velox_api.load_em_breve_list( fn_return ); //Carrega a lista de filmes em breve..
                 
                 
             },
              methods:{
                        corrige: function (){
                            
                            var fn_return = function (res ){
                                
                                
                            }
                            
                            
                              obj_api.call("breve_ajuste", "post", 
                              {   //items_json:
                                  items_json: JSON.stringify(   this.items )
                              }, fn_return);
                        } 
              }
            
            
    }
</script>