<template>
<div>

<section class="col-lg-12">
	<section class="col-lg-9" style="padding-left: 0px; margin-left: 0px">
      <h1 style="padding-left: 0px; margin-left: 0px">agrupamento_notificacoes

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
				 
				 	
								  <label for="f_dia">dia</label>
								   <input class="form-control" name="f_dia" 
									 id="f_dia" v-model="dia"
							  type="text" placeholder="dia" > {{exibe_error('dia')}}
                 
				 
			
    </div> 
 <div class="form-group">  
				 
				 	
								  <label for="f_id_programa">id_programa</label>
								   <input class="form-control" name="f_id_programa" 
									 id="f_id_programa" v-model="id_programa"
							  type="text" placeholder="id_programa" > {{exibe_error('id_programa')}}
                 
				 
			
    </div> 
 <div class="form-group">  
				 
				 	
								  <label for="f_id_emissora">id_emissora</label>
								   <input class="form-control" name="f_id_emissora" 
									 id="f_id_emissora" v-model="id_emissora"
							  type="text" placeholder="id_emissora" > {{exibe_error('id_emissora')}}
                 
				 
			
    </div> 
 <div class="form-group">  
								  <label for="f_palavras">palavras</label>
								   <input class="form-control" name="f_palavras" 
									 id="f_palavras" v-model="palavras"
							  type="text" placeholder="palavras" > {{exibe_error('palavras')}}
                  
    </div> 
 <div class="form-group">  
								  <label for="f_clientes">clientes</label>
								   <input class="form-control" name="f_clientes" 
									 id="f_clientes" v-model="clientes"
							  type="text" placeholder="clientes" > {{exibe_error('clientes')}}
                  
    </div> 
 <div class="form-group">  
				 
				 	
								  <label for="f_hora_inicio_seg">hora_inicio_seg</label>
								   <input class="form-control" name="f_hora_inicio_seg" 
									 id="f_hora_inicio_seg" v-model="hora_inicio_seg"
							  type="text" placeholder="hora_inicio_seg" > {{exibe_error('hora_inicio_seg')}}
                 
				 
			
    </div> 
 <div class="form-group">  
				 
				 	
								  <label for="f_hora_fim_seg">hora_fim_seg</label>
								   <input class="form-control" name="f_hora_fim_seg" 
									 id="f_hora_fim_seg" v-model="hora_fim_seg"
							  type="text" placeholder="hora_fim_seg" > {{exibe_error('hora_fim_seg')}}
                 
				 
			
    </div> 
 <div class="form-group">  
				 
				 	
								  <label for="f_tempo_seg">tempo_seg</label>
								   <input class="form-control" name="f_tempo_seg" 
									 id="f_tempo_seg" v-model="tempo_seg"
							  type="text" placeholder="tempo_seg" > {{exibe_error('tempo_seg')}}
                 
				 
			
    </div> 
 <div class="form-group">  
								  <label for="f_json">json</label>
								   <input class="form-control" name="f_json" 
									 id="f_json" v-model="json"
							  type="text" placeholder="json" > {{exibe_error('json')}}
                  
    </div> 
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
								  <label for="f_hora_inicio">hora_inicio</label>
								   <input class="form-control" name="f_hora_inicio" 
									 id="f_hora_inicio" v-model="hora_inicio"
							  type="text" placeholder="hora_inicio" > {{exibe_error('hora_inicio')}}
                  
    </div> 
 <div class="form-group">  
								  <label for="f_hora_fim">hora_fim</label>
								   <input class="form-control" name="f_hora_fim" 
									 id="f_hora_fim" v-model="hora_fim"
							  type="text" placeholder="hora_fim" > {{exibe_error('hora_fim')}}
                  
    </div> 
 <div class="form-group">  
				 
				 	
								  <label for="f_id_evento_arquivo">id_evento_arquivo</label>
								   <input class="form-control" name="f_id_evento_arquivo" 
									 id="f_id_evento_arquivo" v-model="id_evento_arquivo"
							  type="text" placeholder="id_evento_arquivo" > {{exibe_error('id_evento_arquivo')}}
                 
				 
			
    </div> 
	  
 </div>

</div>
 </section>

  <section class="col-xs-12">
<!--

import AgrupamentoNotificacoesForm from './components/agrupamento_notificacoes/AgrupamentoNotificacoesForm'
import AgrupamentoNotificacoesList from './components/agrupamento_notificacoes/AgrupamentoNotificacoesList'


Vue.component('agrupamento_notificacoes_form', AgrupamentoNotificacoesForm);
Vue.component('agrupamento_notificacoes_list', AgrupamentoNotificacoesList );


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
              dia: "",  
              id_programa: "",  
              id_emissora: "",  
              palavras: "",  
              clientes: "",  
              hora_inicio_seg: "",  
              hora_fim_seg: "",  
              tempo_seg: "",  
              json: "",  
              data: "",  
              hora_inicio: "",  
              hora_fim: "",  
              id_evento_arquivo: "",  
				
            	disableButton: false,
            	publicar_titulo: "Salvar",
            	titulo_acao: "agrupamento_notificacoes",
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


                          var url =  "agrupamento_notificacoes/" + this.id_load; console.log("monted post url: " + url );
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
        self.dia = item.dia;   
        self.id_programa = item.id_programa;   
        self.id_emissora = item.id_emissora;   
        self.palavras = item.palavras;   
        self.clientes = item.clientes;   
        self.hora_inicio_seg = item.hora_inicio_seg;   
        self.hora_fim_seg = item.hora_fim_seg;   
        self.tempo_seg = item.tempo_seg;   
        self.json = item.json;   
        self.data = item.data;   
        self.hora_inicio = item.hora_inicio;   
        self.hora_fim = item.hora_fim;   
        self.id_evento_arquivo = item.id_evento_arquivo;   
			   
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
                var url =  "agrupamento_notificacoes"; console.log("url: " + window.URL_API + url );
                var method = "POST";

                if (this.id != null &&  this.id != ""){
                       method = "PUT"; url =  url + "/"+ this.id;
                }

                var data = {               id: this.id,  
              dia: this.dia,  
              id_programa: this.id_programa,  
              id_emissora: this.id_emissora,  
              palavras: this.palavras,  
              clientes: this.clientes,  
              hora_inicio_seg: this.hora_inicio_seg,  
              hora_fim_seg: this.hora_fim_seg,  
              tempo_seg: this.tempo_seg,  
              json: this.json,  
  
				 data: obj_mask.dataBanco( $("#f_data").val() ) ,  
			 
              hora_inicio: this.hora_inicio,  
              hora_fim: this.hora_fim,  
              id_evento_arquivo: this.id_evento_arquivo,   }


              var fn_return = function (retorno){
            
                  var item = retorno.item;
				  
				  self.carregaForm(item);

                  self.show_message = "on";
            	  self.message_text = "agrupamento_notificacoes salvo com sucesso!";

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
        		obj_api.call_delete("agrupamento_notificacoes", this.id, fn_return);
        	}
         }

    }


</script>
