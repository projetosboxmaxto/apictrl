<template>
<div>

<section class="col-lg-12">
	<section class="col-lg-9" style="padding-left: 0px; margin-left: 0px">
      <h1 style="padding-left: 0px; margin-left: 0px">materia_rascunho

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
				 
				 	
								  <label for="f_id_projeto">id_projeto</label>
								   <input class="form-control" name="f_id_projeto" 
									 id="f_id_projeto" v-model="id_projeto"
							  type="text" placeholder="id_projeto" > {{exibe_error('id_projeto')}}
                 
				 
			
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
								  <label for="f_titulo">titulo</label>
								   <input class="form-control" name="f_titulo" 
									 id="f_titulo" v-model="titulo"
							  type="text" placeholder="titulo" > {{exibe_error('titulo')}}
                  
    </div> 
 <div class="form-group">  
								  <label for="f_cliente_list">cliente_list</label>
								   <input class="form-control" name="f_cliente_list" 
									 id="f_cliente_list" v-model="cliente_list"
							  type="text" placeholder="cliente_list" > {{exibe_error('cliente_list')}}
                  
    </div> 
 <div class="form-group">  
								  <label for="f_ids_arquivos">ids_arquivos</label>
								   <input class="form-control" name="f_ids_arquivos" 
									 id="f_ids_arquivos" v-model="ids_arquivos"
							  type="text" placeholder="ids_arquivos" > {{exibe_error('ids_arquivos')}}
                  
    </div> 
 <div class="form-group">  
								  <label for="f_dados_materia">dados_materia</label>
								   <input class="form-control" name="f_dados_materia" 
									 id="f_dados_materia" v-model="dados_materia"
							  type="text" placeholder="dados_materia" > {{exibe_error('dados_materia')}}
                  
    </div> 
 <div class="form-group">  
				 
				 	
								  <label for="f_id_programa">id_programa</label>
								   <input class="form-control" name="f_id_programa" 
									 id="f_id_programa" v-model="id_programa"
							  type="text" placeholder="id_programa" > {{exibe_error('id_programa')}}
                 
				 
			
    </div> 
 <div class="form-group">  
				 
				 	
								  <label for="f_dia">dia</label>
								   <input class="form-control" name="f_dia" 
									 id="f_dia" v-model="dia"
							  type="text" placeholder="dia" > {{exibe_error('dia')}}
                 
				 
			
    </div> 
 <div class="form-group">  
				 
				 	
								  <label for="f_id_operador">id_operador</label>
								   <input class="form-control" name="f_id_operador" 
									 id="f_id_operador" v-model="id_operador"
							  type="text" placeholder="id_operador" > {{exibe_error('id_operador')}}
                 
				 
			
    </div> 
 <div class="form-group">  
									 <label for="f_data_cadastro">data_cadastro</label>
                                            <input type="text" class="form-control" 
                                                            onfocus="obj_editor.setcalendar(this, event)"
                                                             v-bind:value="data_cadastro | date_show"
                                                             id="f_data_cadastro"
                                                            placeholder="dia/Mes/Ano"  style="width: 120px"
                                                            onkeypress="obj_mask.MascaraData(event, this)"
                                                     />
								    {{exibe_error('data_cadastro')}}
									
									
									
			 </div> 
 <div class="form-group">  
				 
				 	
								  <label for="f_status">status</label>
								   <input class="form-control" name="f_status" 
									 id="f_status" v-model="status"
							  type="text" placeholder="status" > {{exibe_error('status')}}
                 
				 
			
    </div> 
 <div class="form-group">  
				 
				 	
								  <label for="f_id_materia_radiotv_jornal">ID matéria no sistema midiaclip</label>
								   <input class="form-control" name="f_id_materia_radiotv_jornal" 
									 id="f_id_materia_radiotv_jornal" v-model="id_materia_radiotv_jornal"
							  type="text" placeholder="ID matéria no sistema midiaclip" > {{exibe_error('id_materia_radiotv_jornal')}}
                 
				 
			
    </div> 
	  
 </div>

</div>
 </section>

  <section class="col-xs-12">
<!--

import MateriaRascunhoForm from './components/materia_rascunho/MateriaRascunhoForm'
import MateriaRascunhoList from './components/materia_rascunho/MateriaRascunhoList'


Vue.component('materia_rascunho_form', MateriaRascunhoForm);
Vue.component('materia_rascunho_list', MateriaRascunhoList );


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
              id_projeto: "",  
              data: "",  
              titulo: "",  
              cliente_list: "",  
              ids_arquivos: "",  
              dados_materia: "",  
              id_programa: "",  
              dia: "",  
              id_operador: "",  
              data_cadastro: "",  
              status: "",  
              id_materia_radiotv_jornal: "",  
				
            	disableButton: false,
            	publicar_titulo: "Salvar",
            	titulo_acao: "materia_rascunho",
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


                          var url =  "materia_rascunho/" + this.id_load; console.log("monted post url: " + url );
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
        self.id_projeto = item.id_projeto;   
        self.data = item.data;   
        self.titulo = item.titulo;   
        self.cliente_list = item.cliente_list;   
        self.ids_arquivos = item.ids_arquivos;   
        self.dados_materia = item.dados_materia;   
        self.id_programa = item.id_programa;   
        self.dia = item.dia;   
        self.id_operador = item.id_operador;   
        self.data_cadastro = item.data_cadastro;   
        self.status = item.status;   
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
                var url =  "materia_rascunho"; console.log("url: " + window.URL_API + url );
                var method = "POST";

                if (this.id != null &&  this.id != ""){
                       method = "PUT"; url =  url + "/"+ this.id;
                }

                var data = {               id: this.id,  
              id_projeto: this.id_projeto,  
  
				 data: obj_mask.dataBanco( $("#f_data").val() ) ,  
			 
              titulo: this.titulo,  
              cliente_list: this.cliente_list,  
              ids_arquivos: this.ids_arquivos,  
              dados_materia: this.dados_materia,  
              id_programa: this.id_programa,  
              dia: this.dia,  
              id_operador: this.id_operador,  
  
				 data_cadastro: obj_mask.dataBanco( $("#f_data_cadastro").val() ) ,  
			 
              status: this.status,  
              id_materia_radiotv_jornal: this.id_materia_radiotv_jornal,   }


              var fn_return = function (retorno){
            
                  var item = retorno.item;
				  
				  self.carregaForm(item);

                  self.show_message = "on";
            	  self.message_text = "materia_rascunho salvo com sucesso!";

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
        		obj_api.call_delete("materia_rascunho", this.id, fn_return);
        	}
         }

    }


</script>
