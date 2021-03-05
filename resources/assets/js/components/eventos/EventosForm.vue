<template>
<div>

<section class="col-lg-12">
	<section class="col-lg-9" style="padding-left: 0px; margin-left: 0px">
      <h1 style="padding-left: 0px; margin-left: 0px">eventos

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
									 <label for="f_data">Data</label>
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
				 
				 	
								  <label for="f_id_programa">Programa</label>
								   <input class="form-control" name="f_id_programa" 
									 id="f_id_programa" v-model="id_programa"
							  type="text" placeholder="Programa" > {{exibe_error('id_programa')}}
                 
				 
			
    </div> 
 <div class="form-group">  
				 
				 	
								  <label for="f_id_emissora">Emissora</label>
								   <input class="form-control" name="f_id_emissora" 
									 id="f_id_emissora" v-model="id_emissora"
							  type="text" placeholder="Emissora" > {{exibe_error('id_emissora')}}
                 
				 
			
    </div> 
 <div class="form-group">  
								  <label for="f_hora_inicio">Hora Início</label>
								   <input class="form-control" name="f_hora_inicio" 
									 id="f_hora_inicio" v-model="hora_inicio"
							  type="text" placeholder="Hora Início" > {{exibe_error('hora_inicio')}}
                  
    </div> 
 <div class="form-group">  
								  <label for="f_hora_fim">Hora Fim</label>
								   <input class="form-control" name="f_hora_fim" 
									 id="f_hora_fim" v-model="hora_fim"
							  type="text" placeholder="Hora Fim" > {{exibe_error('hora_fim')}}
                  
    </div> 
 <div class="form-group">  
								  <label for="f_duracao">Duração</label>
								   <input class="form-control" name="f_duracao" 
									 id="f_duracao" v-model="duracao"
							  type="text" placeholder="Duração" > {{exibe_error('duracao')}}
                  
    </div> 
 <div class="form-group">  
				 
				 	
								  <label for="f_tempo_realizado_minutos">Tempo Realizado</label>
								   <input class="form-control" name="f_tempo_realizado_minutos" 
									 id="f_tempo_realizado_minutos" v-model="tempo_realizado_minutos"
							  type="text" placeholder="Tempo Realizado" > {{exibe_error('tempo_realizado_minutos')}}
                 
				 
			
    </div> 
 <div class="form-group">  
				 
				 	
								  <label for="f_tempo_total_minutos">tempo_total_minutos</label>
								   <input class="form-control" name="f_tempo_total_minutos" 
									 id="f_tempo_total_minutos" v-model="tempo_total_minutos"
							  type="text" placeholder="tempo_total_minutos" > {{exibe_error('tempo_total_minutos')}}
                 
				 
			
    </div> 
 <div class="form-group">  
				 
				 	
								  <label for="f_dia">dia</label>
								   <input class="form-control" name="f_dia" 
									 id="f_dia" v-model="dia"
							  type="text" placeholder="dia" > {{exibe_error('dia')}}
                 
				 
			
    </div> 
	  
 </div>

</div>
 </section>

  <section class="col-xs-12">
<!--

import EventosForm from './components/eventos/EventosForm'
import EventosList from './components/eventos/EventosList'


Vue.component('eventos_form', EventosForm);
Vue.component('eventos_list', EventosList );


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
              id_programa: "",  
              id_emissora: "",  
              hora_inicio: "",  
              hora_fim: "",  
              duracao: "",  
              tempo_realizado_minutos: "",  
              tempo_total_minutos: "",  
              dia: "",  
				
            	disableButton: false,
            	publicar_titulo: "Salvar",
            	titulo_acao: "eventos",
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


                          var url =  "eventos/" + this.id_load; console.log("monted post url: " + url );
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
        self.id_programa = item.id_programa;   
        self.id_emissora = item.id_emissora;   
        self.hora_inicio = item.hora_inicio;   
        self.hora_fim = item.hora_fim;   
        self.duracao = item.duracao;   
        self.tempo_realizado_minutos = item.tempo_realizado_minutos;   
        self.tempo_total_minutos = item.tempo_total_minutos;   
        self.dia = item.dia;   
			   
		    },
		 
		 
            exibe_error( tipo ){
                    
            },

         	getClassFirstSection(){

         		if ( this.id != "")
         			return "col-xs-9";

         		return "col-xs-12";
         	},

         	validar(){
			
			             if ( obj_alert.isvazioInput("f_data","Informe o Data!"))
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
                var url =  "eventos"; console.log("url: " + window.URL_API + url );
                var method = "POST";

                if (this.id != null &&  this.id != ""){
                       method = "PUT"; url =  url + "/"+ this.id;
                }

                var data = {               id: this.id,  
  
				 data: obj_mask.dataBanco( $("#f_data").val() ) ,  
			 
              id_programa: this.id_programa,  
              id_emissora: this.id_emissora,  
              hora_inicio: this.hora_inicio,  
              hora_fim: this.hora_fim,  
              duracao: this.duracao,  
              tempo_realizado_minutos: this.tempo_realizado_minutos,  
              tempo_total_minutos: this.tempo_total_minutos,  
              dia: this.dia,   }


              var fn_return = function (retorno){
            
                  var item = retorno.item;
				  
				  self.carregaForm(item);

                  self.show_message = "on";
            	  self.message_text = "eventos salvo com sucesso!";

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
        		obj_api.call_delete("eventos", this.id, fn_return);
        	}
         }

    }


</script>
