<template>
<div>

<section class="col-lg-12">
	<section class="col-lg-9" style="padding-left: 0px; margin-left: 0px">
      <h1 style="padding-left: 0px; margin-left: 0px">eventos_arquivos_palavras

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
									 <label for="f_data">data</label>
                                            <input type="text" class="form-control" 
                                                            onfocus="obj_editor.setcalendar(this, event)"
                                                             v-bind:value="data | date_show"
                                                             id="f_data"
                                                            placeholder="dia/Mes/Ano"  style="width: 120px"
                                                            onkeypress="obj_mask.MascaraData(event, this)"
                                                     />
								    {{exibe_error('data')}}
									
									
									
			 </div> 
 <div class="form-group">  
				 
				 	
								  <label for="f_id_evento">id_evento</label>
								   <input class="form-control" name="f_id_evento" 
									 id="f_id_evento" v-model="id_evento"
							  type="text" placeholder="id_evento" > {{exibe_error('id_evento')}}
                 
				 
			
    </div> 
 <div class="form-group">  
				 
				 	
								  <label for="f_id_evento_arquivo">id_evento_arquivo</label>
								   <input class="form-control" name="f_id_evento_arquivo" 
									 id="f_id_evento_arquivo" v-model="id_evento_arquivo"
							  type="text" placeholder="id_evento_arquivo" > {{exibe_error('id_evento_arquivo')}}
                 
				 
			
    </div> 
 <div class="form-group">  
				 
				 	
								  <label for="f_id_cliente">id_cliente</label>
								   <input class="form-control" name="f_id_cliente" 
									 id="f_id_cliente" v-model="id_cliente"
							  type="text" placeholder="id_cliente" > {{exibe_error('id_cliente')}}
                 
				 
			
    </div> 
 <div class="form-group">  
				 
				 	
								  <label for="f_cita_diretamente">cita_diretamente</label>
								   <input class="form-control" name="f_cita_diretamente" 
									 id="f_cita_diretamente" v-model="cita_diretamente"
							  type="text" placeholder="cita_diretamente" > {{exibe_error('cita_diretamente')}}
                 
				 
			
    </div> 
 <div class="form-group">  
								  <label for="f_palavra">palavra</label>
								   <input class="form-control" name="f_palavra" 
									 id="f_palavra" v-model="palavra"
							  type="text" placeholder="palavra" > {{exibe_error('palavra')}}
                  
    </div> 
 <div class="form-group">  
								  <label for="f_tempo">tempo</label>
								   <input class="form-control" name="f_tempo" 
									 id="f_tempo" v-model="tempo"
							  type="text" placeholder="tempo" > {{exibe_error('tempo')}}
                  
    </div> 
 <div class="form-group">  
				 
				 	
								  <label for="f_tempo_seg">tempo_seg</label>
								   <input class="form-control" name="f_tempo_seg" 
									 id="f_tempo_seg" v-model="tempo_seg"
							  type="text" placeholder="tempo_seg" > {{exibe_error('tempo_seg')}}
                 
				 
			
    </div> 
 <div class="form-group">  
				 
				 	
								  <label for="f_id_dicionario_tag">id_dicionario_tag</label>
								   <input class="form-control" name="f_id_dicionario_tag" 
									 id="f_id_dicionario_tag" v-model="id_dicionario_tag"
							  type="text" placeholder="id_dicionario_tag" > {{exibe_error('id_dicionario_tag')}}
                 
				 
			
    </div> 
 <div class="form-group">  
				 
				 	
								  <label for="f_status">status</label>
								   <input class="form-control" name="f_status" 
									 id="f_status" v-model="status"
							  type="text" placeholder="status" > {{exibe_error('status')}}
                 
				 
			
    </div> 
 <div class="form-group">  
								  <label for="f_operador">operador</label>
								   <input class="form-control" name="f_operador" 
									 id="f_operador" v-model="operador"
							  type="text" placeholder="operador" > {{exibe_error('operador')}}
                  
    </div> 
 <div class="form-group">  
				 
				 	
								  <label for="f_id_operador">id_operador</label>
								   <input class="form-control" name="f_id_operador" 
									 id="f_id_operador" v-model="id_operador"
							  type="text" placeholder="id_operador" > {{exibe_error('id_operador')}}
                 
				 
			
    </div> 
 <div class="form-group">  
				 
				 	
								  <label for="f_id_materia_radiotv_jornal">id_materia_radiotv_jornal</label>
								   <input class="form-control" name="f_id_materia_radiotv_jornal" 
									 id="f_id_materia_radiotv_jornal" v-model="id_materia_radiotv_jornal"
							  type="text" placeholder="id_materia_radiotv_jornal" > {{exibe_error('id_materia_radiotv_jornal')}}
                 
				 
			
    </div> 
	  
 </div>

</div>
 </section>

  <section class="col-xs-12">
<!--

import EventosArquivosPalavrasForm from './components/eventos_arquivos_palavras/EventosArquivosPalavrasForm'
import EventosArquivosPalavrasList from './components/eventos_arquivos_palavras/EventosArquivosPalavrasList'


Vue.component('eventos_arquivos_palavras_form', EventosArquivosPalavrasForm);
Vue.component('eventos_arquivos_palavras_list', EventosArquivosPalavrasList );


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
    export default {
      props: ['id_load', 'post_type', 'show_back_button','onBack', 'onSave'],
       data: function() {
            return {
            	
				              id: "",  
              data: "",  
              id_evento: "",  
              id_evento_arquivo: "",  
              id_cliente: "",  
              cita_diretamente: "",  
              palavra: "",  
              tempo: "",  
              tempo_seg: "",  
              id_dicionario_tag: "",  
              status: "",  
              operador: "",  
              id_operador: "",  
              id_materia_radiotv_jornal: "",  
				
            	disableButton: false,
            	publicar_titulo: "Salvar",
            	titulo_acao: "eventos_arquivos_palavras",
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


                          var url =  "eventos_arquivos_palavras/" + this.id_load; console.log("monted post url: " + url );
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
        self.data = item.data;   
        self.id_evento = item.id_evento;   
        self.id_evento_arquivo = item.id_evento_arquivo;   
        self.id_cliente = item.id_cliente;   
        self.cita_diretamente = item.cita_diretamente;   
        self.palavra = item.palavra;   
        self.tempo = item.tempo;   
        self.tempo_seg = item.tempo_seg;   
        self.id_dicionario_tag = item.id_dicionario_tag;   
        self.status = item.status;   
        self.operador = item.operador;   
        self.id_operador = item.id_operador;   
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
			
			             if ( obj_alert.isvazioInput("f_id","Informe o id!"))
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
                var url =  "eventos_arquivos_palavras"; console.log("url: " + window.URL_API + url );
                var method = "POST";

                if (this.id != null &&  this.id != ""){
                       method = "PUT"; url =  url + "/"+ this.id;
                }

                var data = {               id: this.id,  
  
				 data: obj_mask.dataBanco( $("#f_data").val() ) ,  
			 
              id_evento: this.id_evento,  
              id_evento_arquivo: this.id_evento_arquivo,  
              id_cliente: this.id_cliente,  
              cita_diretamente: this.cita_diretamente,  
              palavra: this.palavra,  
              tempo: this.tempo,  
              tempo_seg: this.tempo_seg,  
              id_dicionario_tag: this.id_dicionario_tag,  
              status: this.status,  
              operador: this.operador,  
              id_operador: this.id_operador,  
              id_materia_radiotv_jornal: this.id_materia_radiotv_jornal,   }


              var fn_return = function (retorno){
            
                  var item = retorno.item;
				  
				  self.carregaForm(item);

                  self.show_message = "on";
            	  self.message_text = "eventos_arquivos_palavras salvo com sucesso!";

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
        		obj_api.call_delete("eventos_arquivos_palavras", this.id, fn_return);
        	}
         }

    }


</script>
