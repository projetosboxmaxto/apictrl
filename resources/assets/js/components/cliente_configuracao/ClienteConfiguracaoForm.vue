<template>
<div>

<section class="col-lg-12">
	<section class="col-lg-9" style="padding-left: 0px; margin-left: 0px">
      <h1 style="padding-left: 0px; margin-left: 0px">Cliente Configuração

      </h1>
      <ol class="breadcrumb" style="display: none">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Forms</a></li>
        <li class="active">General Elements</li>
      </ol>
    </section>

<section class="col-lg-3" style="padding-top: 30px">
        <a href="#"  v-on:click="botao_voltar" v-if="botao_voltar_visible">
           <i class="fa fa-arrow-left"></i> Voltar para lista
        </a> 
</section>
</section>

<div class="col-lg-12">
					<div v-if="show_message == 'on' " class="alert alert-success">
					      {{message_text}}
					</div>
</div>


<section class="col-xs-12">
<div class="box"><div class="box-header with-border"></div> 
<div class="box-body">

       <div class="form-group">  
				   <h3>{{id_load + ' - ' + nome_cliente }} </h3>
				 
			
    </div> 
 <div class="form-group">  
   <checkbox_int name="f_consulta_comum" v-model="consulta_comum" v-if="!loading" ></checkbox_int>
       <label for="f_consulta_comum">Usa consulta comum (Like)?</label>

                      <button type="button" class="btn btn-default btn-xs" v-on:click="editar_palavra">Editar Palavras Chave</button>
							    
							 
		
	  </div> 
 <div class="form-group">  
   <checkbox_int name="f_consulta_elastic" v-model="consulta_elastic"  v-if="!loading"></checkbox_int>
							        <label for="f_consulta_elastic">Usa Elastic Search?</label>

                      <button type="button" class="btn btn-default btn-xs" v-on:click="editar_elastic">Editar Elastic</button>
							    
	  </div> 
	  
 </div>

</div>
 </section>

  <section class="col-xs-12">
<!--

import ClienteConfiguracaoForm from './components/cliente_configuracao/ClienteConfiguracaoForm'
import ClienteConfiguracaoList from './components/cliente_configuracao/ClienteConfiguracaoList'


Vue.component('cliente_configuracao_form', ClienteConfiguracaoForm);
Vue.component('cliente_configuracao_list', ClienteConfiguracaoList );


-->


 <div class="form-group">
                	<div class="btn-group pull-right" >


                     <button type="submit" class="btn btn-info" 
                      v-bind:disabled="disableButton"
                     v-on:click="salvar()">{{publicar_titulo}}</button> 


                     <button type="submit" class="btn btn-danger" 
                      v-bind:disabled="disableButton" v-if="id!='' && parseInt(id) > 0"
                     v-on:click="excluir()">Excluir</button> 
                 </div> 
             </div>

  </section>

</div>
</template>

<script>
import checkbox_int from '../../library/checkbox/checkbox_int';

    export default {
      props: ['id_load', 'post_type', 'show_back_button','onBack', 
      'nome_cliente',
      'onSave', 'onEdit'],
      components:{
        checkbox_int
      },
       data: function() {
            return {
            	
				      id: "",  
              id_cliente: "",  
              consulta_comum: "",  
              consulta_elastic: "",  
				
            	disableButton: false,
            	publicar_titulo: "Salvar",
            	titulo_acao: "Cliente Configuração",
            	botao_voltar_visible: false,

            	show_message: "off",
            	message_text: "",
            	message_type: "success",
              interval_message: null,
              
              loading: false,


            }
        },
        mounted() {

                let self = this;
   
                            if ( this.show_back_button != null && this.show_back_button != undefined  ){
                                     this.botao_voltar_visible = this.show_back_button;
                            }


                          if ( this.id_load == null || this.id_load == ""){
                            return;
                          }

                          this.loading = true;


                          var url =  "cliente_configuracao/" + this.id_load; console.log("monted post url: " + url );
                          var method = "get";

                          this.disableButton = true;

                          var data = { }


			              var fn_return = function (retorno){

                                           // console.log("Retorno? ");
                                           // console.log( retorno );
                                            
			              	     var item = retorno.item;
								 
								          self.carregaForm(item);
                          self.loading = false;



                                 self.disableButton = false;

			              }

			              obj_api.call(url, method, data , fn_return);



         },
         methods:{
		 
		 
            carregaForm(item){
                     var self = this;
			                 self.id = item.id;   
        self.id_cliente = item.id_cliente;   
        self.consulta_comum = item.consulta_comum;   
        self.consulta_elastic = item.consulta_elastic;   
			   
        },
        editar_elastic(){
          console.log("onEdit?", this.onEdit );
                  if ( this.onEdit != null ){
                    this.onEdit('editar_elastic');
                  }

        },
        editar_palavra(){
                  if ( this.onEdit != null ){
                    this.onEdit('editar_palavra');
                  }
        },	 
		 
            exibe_error( tipo ){
                    
            },

         	getClassFirstSection(){

         		if ( this.id != "")
         			return "col-xs-9";

         		return "col-xs-12";
         	},

         	validar(){
			
			          

             
                    	return true;
         	},


        	botao_voltar(){
        		var self = this;

                 if ( this.onBack != null && this.onBack != undefined ){
                 	console.log("clique no voltar!");
                 	 this.onBack( self );
                 }
        	},

        	salvar (tipo ){

        		if ( !this.validar() )
        			return false;

        	
                let self = this;
                var url =  "cliente_configuracao"; console.log("url: " + window.URL_API + url );
                var method = "POST";

                if (this.id != null &&  this.id != ""){
                       method = "PUT"; url =  url + "/"+ this.id;
                }

                var data = {               id: this.id,  
              id_cliente: this.id_cliente,  
              consulta_comum: this.consulta_comum,  
              consulta_elastic: this.consulta_elastic,   }


              var fn_return = function (retorno){
            
                  var item = retorno.item;
				  
				  self.carregaForm(item);

                  self.show_message = "on";
            	  self.message_text = "Cliente Configuração salvo com sucesso!";

            	  self.interval_message = setInterval(self.clear_message, 6000);

                     if ( self.onSave != null && self.onSave != undefined ){
                            self.onSave(retorno, 'save');
                     }



              }

              obj_api.call(url, method, data , fn_return);

        	},

        	clear_message(){
                 this.show_message = "off";
                 clearInterval(this.interval_message);
        	},

        	excluir(){
        		let self = this;
        		var fn_return = function(retorno, tipo){
        			self.onSave(retorno, tipo);
        			self.botao_voltar();

        		}
        		obj_api.call_delete("cliente_configuracao", this.id, fn_return);
        	}
         }

    }


</script>
