<template>
	 <div>

	 	   <div class="col-xs-12" style="margin-top: 10px">
                    

					 	   <div class="col-xs-4">
				                    
				              
										 <div class="form-group">
									                  <label for="e_nome">Nome do Site</label>
									                   <input class="form-control" 
									                   v-model="nome"
									              type="text" placeholder="Nome" >
									                </div>   

					 	   </div>  


					 	     <div class="col-xs-4">
				                    
				              
										 <div class="form-group">
									                  <label for="e_nome">Título</label>
									                   <input class="form-control" 
									                   v-model="titulo"
									              type="text" placeholder="Título" >
									                </div>   

					 	   </div>  



					 	     <div class="col-xs-4">
				                    
				              
										 <div class="form-group">
									                  <label for="e_nome">Código Google Analitcs</label>
									                   <input class="form-control" 
									                   v-model="google_analitcs"
									              type="text" placeholder="Código" >
									                </div>   

					 	   </div>  
					 	</div>
             <div class="col-xs-12">
                 <div class="col-xs-12">
                     
                      <div class="form-group">
                                                <label >URL Site</label>
                                                 <input class="form-control" 
                                                 v-model="url_site"
                                            type="text" placeholder="URL deste site" >
                                              </div>  
                 </div> 
             </div>



					 	   <div class="col-xs-12">
					 	   <div class="col-xs-12">
										 <div class="form-group">
									                  <label >Descrição</label>
									                   <textarea v-model="descricao"
									                   class="form-control"
									                   style="height: 60px" ></textarea>

									                </div> 
						     </div>    
						      </div>  

 


	 	         <div class="col-xs-12">
					 	   <div class="col-xs-3">

                                <upload-unico 
   
                                       id_parent="config" 
                            title="Imagem para Rede Social"
                             com_classes="0"
                             ptype="c:socnet"></upload-unico>
					 	   </div>


					 	    <div class="col-xs-2">
				                    
				              
										 <div class="form-group">
									                  <label for="e_nome">URL Facebook</label>
									                   <input class="form-control" 
									                   v-model="url_facebook"
									              type="text" placeholder="URL" >
									                </div>   

					 	      </div>  
 

					 	        <div class="col-xs-2">

										 <div class="form-group">
									                  <label for="e_nome">URL Instagram</label>
									                   <input class="form-control" 
									                   v-model="url_instagram"
									              type="text" placeholder="URL" >
									                </div>   

					 	        </div>  

					 	   <div class="col-xs-2">
				                    
				              
										 <div class="form-group">
									                  <label for="e_nome">URL Youtube</label>
									                   <input class="form-control" 
									                   v-model="url_youtube"
									              type="text" placeholder="URL" >
									                </div>   

					 	    </div>  


					 	    <div class="col-xs-2">
				                    
				              
										 <div class="form-group">
									                  <label for="e_nome">URL Twitter</label>
									                   <input class="form-control" 
									                   v-model="url_twitter"
									              type="text" placeholder="URL" >
									                </div>   


					 	     </div>
					 	 </div>


	 	   <div class="col-xs-12">
	 	        <div class="col-xs-12" v-if="carregar_cor">

						<div class="form-group">
                             <label>Personalizar Cores</label>
                    
                           <div>
                               <div style="float: left; width: 80px" 
                                    v-for="item in list_cores_padrao">
                                      <conf-color :idDiv='item.name' 
                                        :initialCor='getColor(item.name)'
                                        :color_name='item.name'
                                        :onSave="onSaveColor">
                                      </conf-color>
                               </div>
                               <div style="float: left; width: 80px" >
                                   <button class='btn btn-secundary' v-on:click='setDefaultColor'
                                           title='Força o carregamento das cores padrões do site' type='button'>
                                       Carregar Padrão
                                   </button>
                                   
                               </div>
                           </div>
                           </div>

                               
	 	          </div>          
	 	   </div>
             
             
	 	   <div class="col-xs-12">
                       <div class="col-xs-4" >
                               <br>  <br>  <br>
                             <upload-unico 
   
                                       id_parent="config" 
                            title="Favicon (Arquivo .zip)"
                             com_classes="1"
                             ptype="c:favi"></upload-unico>
                         
                       </div>
                   </div>
             

             
       <div class="col-xs-12" style="display: none">
         <h3 style="margin-left: 15px">Configuração API VELOXTICKETS</h3>
                       <div class="col-xs-2" >
                         
                          <div class="form-group">
                                    <label >Código Cliente</label>
                                     <input class="form-control" 
                                     v-model="velox_codigo"
                                type="text" placeholder="Código de Cliente" >
                           </div>   
                       </div>
                         <div class="col-xs-4" >
                      
                          <div class="form-group">
                                    <label >URL API</label>
                                     <input class="form-control" 
                                     v-model="velox_url_api"
                                type="text" placeholder="URL da API" >
                           </div>   
                       </div>
                          <div class="col-xs-2" >
                         
                          <div class="form-group">
                                    <label >Primeira Cidade</label>
                                     <input class="form-control" 
                                     v-model="velox_first_city"
                                type="text" placeholder="URL da API" >
                           </div>   
                       </div>
                        <div class="col-xs-4" >
                         
                          <div class="form-group">
                                    <label >Caminho Javascript Loader</label>
                                     <input class="form-control" 
                                     v-model="velox_JS"
                                type="text" placeholder="Caminho Javascript Loader" >
                           </div>   
                       </div>
                       <div class="col-xs-6" >
                         
                          <div class="form-group">
                                    <label >Caminho URL Iframe</label>
                                     <input class="form-control" 
                                     v-model="velox_iframe_url"
                                type="text" placeholder="Caminho para o IFRAME" >
                           </div>   
                       </div>
                        <div class="col-xs-6" >
                         
                          <div class="form-group">
                                    <label >Caminho inicial Iframe de Filme</label>
                                     <input class="form-control" 
                                     v-model="velox_iframe_filme"
                                type="text" placeholder="Caminho para o IFRAME" >
                           </div>   
                       </div>
                   </div>

                              

	 	         <div class="col-xs-11">
             <div class="pull-right">
                 <button v-on:click="salvar" class="btn btn-primary btn-lg">
                          Salvar
                 </button>
             </div>

	        </div>

	 </div>



	</template>


<script>
    export default {
        props: [ ],

        data: function() {
            return {

				               nome:"",
				               titulo: "",
				               google_analitcs:"",
				               descricao: "",

				               url_facebook: "",
				               url_instagram: "",
				               url_youtube: "",
				               url_twitter: "",
                       url_site: "",


                       velox_codigo: "",
                       velox_url_api: "",
                       velox_first_city: "",
                       velox_JS: "",
                       velox_iframe_url: "",
                       velox_iframe_filme: "",

				               colors: [],
				               carregar_cor: false,
                                               list_cores_padrao: [
                                                   {name: "@color-1", value: "#f5c135"},
                                                   {name: "@color-2", value: "#222222"},
                                                   {name: "@color-3", value: "#292929"},
                                                   {name: "@color-4", value: "#FFFFFF"},
                                                   {name: "@color-5", value: "#F2F2F2"},
                                                   {name: "@color-6", value: "#a6061b"},
                                                   {name: "@color-7", value: "#006489"},
                                                   {name: "@color-8", value: "#f6a234"},
                                                   {name: "@p", value: "#6a6a6a"},
                                                   {name: "@ok", value: "#4CAF50"},
                                                   {name: "@border-clean", value: "#e5e5e5"}
                                               ]
                                                   
                                                       
                                                   
                                               


            }
        },
        methods: {

          concat(pre, it){
                  return pre + it.toString();
          },

          getColor(n){
                  // console.log("Vou achar a cor" + n +"? ");
                  // console.log( this.colors );

                 for ( var i = 0; i < this.colors.length; i++ ){
                         if ( this.colors[i].name ==  n.toString() ){
                         	//console.log("achei a cor " + n + " " + this.colors[i].value );
                         	return this.colors[i].value;
                         }                
                 }

                 return "#FFFFFF";
                      
          },
          setValor(tip, value){
          	console.log("Chegou a cor? " + tip + " " + value );
          	   let colors = this.colors;
               for ( var i = 0; i < colors.length; i++ ){
                         if ( colors[i].name == tip ){
                         	       colors[i].value = value;
                         	       Vue.set(this.colors, i, colors[i]);
                                       return;
                         }                
                }


                //colors[colors.length] = 
                this.colors.push( {name: tip, value: value} );

          },

          onSaveColor(color, tip){
                this.setValor(tip, color);
          },
          
          setDefaultColor(){
              var self = this;
              self.carregar_cor = false;
              
              var fn_return = function(retorno){
                  
                            var cores = retorno.data;
                            self.colors = cores;
                            self.carregar_cor = true;
                            
                            obj_alert.show("Sucesso!","Padrão de cores retornado ao início com sucesso!",
                                "success");
                        
                  }

                 obj_api.call('configdefault', 'get', {}, fn_return); 
              
          },

          salvar(){

                 var data = {nome: this.nome,
				               titulo: this.titulo,
				               google_analitcs: this.google_analitcs,
				               descricao: this.descricao,

				               url_facebook: this.url_facebook,
				               url_instagram: this.url_instagram,
				               url_youtube: this.url_youtube,
				               url_twitter: this.url_twitter, 
                       url_site: this.url_site,

                       velox_codigo: this.velox_codigo,
                       velox_url_api: this.velox_url_api,
                       velox_first_city: this.velox_first_city,
                       velox_JS: this.velox_JS,
                       velox_iframe_url: this.velox_iframe_url,
                       velox_iframe_filme: this.velox_iframe_filme,
                        }

                 var keys = Object.keys(data).join(',');
                 var cores_a = new Array();
                 for ( var i = 0; i < this.colors.length; i++ ){
                        data[ "cor_"+ this.colors[i].name.replace("@","") ] = this.colors[i].value;
                        cores_a[cores_a.length] = "cor_"+ this.colors[i].name.replace("@","");
                 } 

                 data["keys"] = keys;
                 data["keys_cores"] = cores_a.join(",");
                 
                 console.log("enviando data "); console.log(data);

                 var fn_return = function(retorno){
                        obj_alert.show("Sucesso!","Configuração salva com sucesso!",
                        	  "success", null, 4000);
                 }

                 obj_api.call('config', 'post', data, fn_return);    

          }
        },
        computed: {
              
        },
        mounted() {

           let self = this;

           var fn_return = function (retorno){

                 let data = retorno.data;
                 let cores = [];

                 for ( var i = 0; i < data.length; i++ ){

                 	var item = data[i];

                  if(self.hasOwnProperty(item.key)){
                     self[item.key]  = item.value;
                  }

             

                 	//console.log("ITEM: " + item.key +" - " + item.value );

                 	if ( item.key != null && item.key != undefined 
                 		 && item.key.substring(0,1) == "@"){
                                 cores[cores.length] = {name: item.key, value: item.value}
                 	}

                 }

               // console.log("Cores recebidas");
               //  console.log(cores);

                 self.colors = cores;
                 self.carregar_cor = true;
           }

           obj_api.call('config','get', {}, fn_return);

           $(".content-wrapper").css('min-height', '1100px');
          }
     }
 
    </script>
