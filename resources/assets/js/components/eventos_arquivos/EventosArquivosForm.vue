<template>
<div>

<section class="col-lg-12">
	<section class="col-lg-9" style="padding-left: 0px; margin-left: 0px">
      <h1 style="padding-left: 0px; margin-left: 0px">eventos_arquivos

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
								  <label for="f_path">path</label>
								   <input class="form-control" name="f_path" 
									 id="f_path" v-model="path"
							  type="text" placeholder="path" > {{exibe_error('path')}}
                  
    </div> 
 <div class="form-group">  
								  <label for="f_nome">Arquivo</label>
								   <input class="form-control" name="f_nome" 
									 id="f_nome" v-model="nome"
							  type="text" placeholder="Arquivo" > {{exibe_error('nome')}}
                  
    </div> 
 <div class="form-group">  
				 
				 	
								  <label for="f_id_evento">id_evento</label>
								   <input class="form-control" name="f_id_evento" 
									 id="f_id_evento" v-model="id_evento"
							  type="text" placeholder="id_evento" > {{exibe_error('id_evento')}}
                 
				 
			
    </div> 
 <div class="form-group">  
				 
				 	
								  <label for="f_tempo_realizado_minutos">Tempo</label>
								   <input class="form-control" name="f_tempo_realizado_minutos" 
									 id="f_tempo_realizado_minutos" v-model="tempo_realizado_minutos"
							  type="text" placeholder="Tempo" > {{exibe_error('tempo_realizado_minutos')}}
                 
				 
			
    </div> 
 <div class="form-group">  
								  <label for="f_hora_inicio">Hora Início</label>
								   <input class="form-control" name="f_hora_inicio" 
									 id="f_hora_inicio" v-model="hora_inicio"
							  type="text" placeholder="Hora Início" > {{exibe_error('hora_inicio')}}
                  
    </div> 
 <div class="form-group">  
				 
				 	
								  <label for="f_id_materia_radiotv_jornal">ID Matéria Gerada</label>
								   <input class="form-control" name="f_id_materia_radiotv_jornal" 
									 id="f_id_materia_radiotv_jornal" v-model="id_materia_radiotv_jornal"
							  type="text" placeholder="ID Matéria Gerada" > {{exibe_error('id_materia_radiotv_jornal')}}
                 
				 
			
    </div> 
	  
 </div>

</div>
 </section>

  <section class="col-xs-12">
<!--

import EventosArquivosForm from './components/eventos_arquivos/EventosArquivosForm'
import EventosArquivosList from './components/eventos_arquivos/EventosArquivosList'


Vue.component('eventos_arquivos_form', EventosArquivosForm);
Vue.component('eventos_arquivos_list', EventosArquivosList );


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
import grid_cliente from "../eventos/GridCliente.vue";


    export default {
      props: ['id_load', 'post_type', 'show_back_button','onBack', 'onSave'],
       data: function() {
            return {
            	
				              id: "",  
              path: "",  
              nome: "",  
              id_evento: "",  
              tempo_realizado_minutos: "",  
              hora_inicio: "",  
              id_materia_radiotv_jornal: "",  
				
            	disableButton: false,
            	publicar_titulo: "Salvar",
            	titulo_acao: "eventos_arquivos",
            	botao_voltar_visible: false,

            	show_message: "off",
            	message_text: "",
            	message_type: "success",
            	interval_message: null,


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


                          var url =  "eventos_arquivos/" + this.id_load; console.log("monted post url: " + url );
                          var method = "get";

                          this.disableButton = true;

                          var data = { }


			              var fn_return = function (retorno){

                                           // console.log("Retorno? ");
                                           // console.log( retorno );
                                            
			              	     var item = retorno.item;
								 
								 self.carregaForm(item);



                                 self.disableButton = false;

			              }

			              obj_api.call(url, method, data , fn_return);



         },
         methods:{
		 
		 
            carregaForm(item){
                     var self = this;
			                 self.id = item.id;   
        self.path = item.path;   
        self.nome = item.nome;   
        self.id_evento = item.id_evento;   
        self.tempo_realizado_minutos = item.tempo_realizado_minutos;   
        self.hora_inicio = item.hora_inicio;   
        self.id_materia_radiotv_jornal = item.id_materia_radiotv_jornal;   
			   
		    },
		 
		 
            exibe_error( tipo ){
                    
            },

         	getClassFirstSection(){

         		if ( this.id != "")
         			return "col-xs-9";

         		return "col-xs-12";
         	},

         	validar(){
			
			             if ( obj_alert.isvazioInput("f_nome","Informe o Arquivo!"))
                       	         return false;   

             
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
                var url =  "eventos_arquivos"; console.log("url: " + window.URL_API + url );
                var method = "POST";

                if (this.id != null &&  this.id != ""){
                       method = "PUT"; url =  url + "/"+ this.id;
                }

                var data = {               id: this.id,  
              path: this.path,  
              nome: this.nome,  
              id_evento: this.id_evento,  
              tempo_realizado_minutos: this.tempo_realizado_minutos,  
              hora_inicio: this.hora_inicio,  
              id_materia_radiotv_jornal: this.id_materia_radiotv_jornal,   }


              var fn_return = function (retorno){
            
                  var item = retorno.item;
				  
				  self.carregaForm(item);

                  self.show_message = "on";
            	  self.message_text = "eventos_arquivos salvo com sucesso!";

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
        		obj_api.call_delete("eventos_arquivos", this.id, fn_return);
        	}
         }

    }


</script>
