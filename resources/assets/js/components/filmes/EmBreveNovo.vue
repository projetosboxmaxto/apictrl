<template>
          <div >

    	   <span v-if="exibe_carregando"> Obtendo filmes
                        <img v-bind:src="img_loading | base_url" />
           </span>
              <div class="col-xs-12">
                  	       <div class="box box-default">

                      	 	  <div class="box-header">
                      	 	  	<div class="col-xs-12">

                                       <h3 style="margin-left: 0px; margin-top: 0px">Filmes em Breve 2</h3>
                      	 	  	</div>
                      	 	  </div>
                                 <div class="box-body">
                                     <div class="form-group">
                                              <label for="id_sel_filme">Adicionar Filme:</label>
                                         <div class="input-group input-group-sm">
                                         <select id="id_sel_filme" class="form-control" name="id_sel_filme"
                                                 v-model="id_selecionado">
                                             <option value="">{{msg_filme}}</option>
                                             <option v-for="item in items"
                                                     v-html="item.name"
                                                     v-bind:value="item.id" ></option>
                                         </select>
                                             <span class="input-group-btn">
                                              <button v-on:click="adicionar" type="button" class="btn btn-info" >
                                                  <i class="fa fa-plus"></i>  Adicionar
                                              </button> 
                                              <button v-on:click="merge"
                                               v-if="exibe_botao_excluir"
                                               type="button" class="btn btn-default" >
                                                  <i class="fa fa-align-justify"></i>  Remover em Cartaz
                                              </button> 
                                              
                                         </span>
                                         </div>
                                         
                                       
                                         
                                     </div>
                                     
                                     
                                     
              <div class="col-xs-12">
                  
                  <div class="col-xs-3"
                       
                       v-for="(item, index) in items_data" style="min-height: 210px">
                      
                      <div>
                          <a v-bind:href="getDataContent(item, 'url')" target="_blank">
                           <img v-bind:src="getDataContent(item, 'imgUrl')" style="max-height: 140px" />
                          </a>
                           <br>
                           {{getDataContent(item, 'name')}}
                           <button class="btn btn-xs btn-default" >
                               <i class="fa fa-trash" v-on:click="excluir(item, index)"></i>
                           </button>
                      </div>
                  </div>
                  
                  
              </div>
                                     
                                 </div>           
                 </div>
             </div>     
          	

                  


          		<div class="col-xs-12">
			          	 <div class="box box-default">

			          	 	  <div class="box-header">
				          	 	  	<div class="col-xs-12">

				                           <h3 style="margin-left: 0px; margin-top: 0px">Background</h3>
				          	 	  	</div>
				          	 	  </div>
				          	 	  <div class="box-body">

				                         	<filme-back ptype="brv:back"></filme-back>
				          	 	  </div>


			          	 </div>
          	  </div>

          </div>

</template>

<script>
    export default {

           data: function() {
           	return {
                      title: "",
                      content: "",
                      id: "",
                      
                      id_selecionado: "",
                      msg_filme: "--",
                      items: [],
                      items_data: [],
                      items_cartaz: [],

                      exibe_carregando: false,
                      img_loading: "loading.gif",
                      exibe_botao_excluir: false,

           	}
           },
           mounted() {
                    

                 let self = this;
                 self.msg_filme = "..Carregando";
                 this.exibe_carregando = true;
         
                 var fn_return = function (options ){

                           console.log("Retornando os filmes em breve..");
                            console.log(options);
                          self.items = options;
                          self.msg_filme = "--";
                  }


	         obj_velox_api.load_em_breve_list( fn_return ); //Carrega a lista de filmes em breve..
                 
                   self.carregaListaEmbreve();

                   var fn_return3 = function (retorno){
                               self.items_cartaz = retorno;
                               self.exibe_botao_excluir = true;

                   }

                   obj_velox_api.get_cartaz_list( fn_return3);



                        
               },
               methods: {

                   carregaListaEmbreve(){

                    let self = this;
                    self.exibe_carregando = true;

                     var fn_return = function (retorno){
                         
                         
                                   console.log("Consegui fazer a consulta?"); 
                                   console.log(retorno);
                                    self.items_data = retorno.data;       

                                    self.exibe_carregando = false;

                        }

                        obj_api.call("postgrid?type="+this.getPostType(), "get", {} , fn_return);


                   },
                   
                   getDataContent(item, tipo){
                       
                       if ( item == null || item.content == null )
                             return "";
                       
                      var obj = JSON.parse(  item.content );
                      
                      console.log(" obj obtido " + item.content );
                      console.log( obj );
                      //return "";
                         if ( obj == null || obj == undefined )
                             return "";
                      
                      
                      if ( obj[tipo] == null || obj[tipo] == undefined)
                          return "";
              
                      return obj[tipo];
                   },
                   
                    getFilmeById(id){
                      
                            var self = this;
                    
                            for ( var i = 0; i < self.items.length; i++ ){
                                console.log(self.items[i].id +" === " + id.toString() );
                                if ( self.items[i].id.toString() == id.toString() ){
                                    return self.items[i];
                                }
                            }
                            
                            return null;
                       
                    },

                     salvar_textos(){

                            let self = this;
                            var data = {title: self.title, content: self.content};

                              var fn_return = function (retorno){

                                         obj_alert.show("Sucesso!", "Título e conteúdo alterados com sucesso!", "success", 3000);

                              }

                         obj_api.call("postedittext/"+ self.id, "put", data , fn_return);
                     },

                     merge( ){

                        let self = this;


                               var ids = "";
                               
                               var itens = JSON.parse(  self.items_cartaz );

                               for (var i = 0; i < itens.length; i++) {
                                      var item =  itens[i];

                                      if ( ids != "")
                                          ids += ",";

                                     

                                        ids += item.id;
                               }


                              var data = {items_cartaz: ids,   acao: "merge"};


                              var fn_return = function (retorno){

                                         var qtde = retorno.qtde;

                                         obj_alert.show("Sucesso!", "Foram removido(s) " + 
                                           qtde.toString()+" filme(s) da lista 'em breve.'", "success", 3000);

                                        self.carregaListaEmbreve();

                              }

                             obj_api.call("postedmerge", "POST", data , fn_return);
                     },
                     
                     excluir(item, indx){
                         
                        let self = this;
                      		var fn_return = function(retorno, tipo){
                      			
                                                 self.items_data.splice(indx, 1);
                      		}
                      		obj_api.call_delete("post", item.id, fn_return);
                                       
                     },
                      adicionar(){
                          
                          var self = this;
                          
                          if ( this.id_selecionado == ""){
                              
                              obj_alert.show("Atenção!","Selecione um filme!", "warning");
                              return; //adicionar
                          }
                          
                          var item = this.getFilmeById(  this.id_selecionado );
                          
                          
                          var data = {title: self.id_selecionado, content: JSON.stringify(item), 
                                 description: "1", slug_type: "time",
                                  post_type: "mov:brv2" };


                          console.log("pre envio: ");
                          console.log(data );
                          console.log( item );

				              var fn_return = function (retorno){

				                     
                                                        self.items_data.unshift(retorno.item);
                                                        self.id_selecionado = "";
				              }

				              obj_api.call("post", "post", data , fn_return);
                          
                          
                          
                          
                          
                          
                      },
                      getPostType(){
                        return "mov:brv2";  
                      }
               }


       }
 </script>      