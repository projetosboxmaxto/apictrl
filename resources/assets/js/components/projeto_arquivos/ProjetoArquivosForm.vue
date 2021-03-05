<template>
<div>

<section class="col-lg-12">
	<section class="col-lg-9" style="padding-left: 0px; margin-left: 0px">
      <h1 style="padding-left: 0px; margin-left: 0px">projeto_arquivos

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
				 
				 	
								  <label for="f_id_projeto">id_projeto</label>
								   <input class="form-control" name="f_id_projeto" 
									 id="f_id_projeto" v-model="id_projeto"
							  type="text" placeholder="id_projeto" > {{exibe_error('id_projeto')}}
                 
				 
			
    </div> 
 <div class="form-group">  
				 
				 	
								  <label for="f_id_operador">id_operador</label>
								   <input class="form-control" name="f_id_operador" 
									 id="f_id_operador" v-model="id_operador"
							  type="text" placeholder="id_operador" > {{exibe_error('id_operador')}}
                 
				 
			
    </div> 
 <div class="form-group">  
								  <label for="f_arquivo">arquivo</label>
								   <input class="form-control" name="f_arquivo" 
									 id="f_arquivo" v-model="arquivo"
							  type="text" placeholder="arquivo" > {{exibe_error('arquivo')}}
                  
    </div> 
 <div class="form-group">  
								  <label for="f_meta_dados">meta_dados</label>
								   <input class="form-control" name="f_meta_dados" 
									 id="f_meta_dados" v-model="meta_dados"
							  type="text" placeholder="meta_dados" > {{exibe_error('meta_dados')}}
                  
    </div> 
 <div class="form-group">  
								  <label for="f_path">path</label>
								   <input class="form-control" name="f_path" 
									 id="f_path" v-model="path"
							  type="text" placeholder="path" > {{exibe_error('path')}}
                  
    </div> 
 <div class="form-group">  
								  <label for="f_tipo">tipo</label>
								   <input class="form-control" name="f_tipo" 
									 id="f_tipo" v-model="tipo"
							  type="text" placeholder="tipo" > {{exibe_error('tipo')}}
                  
    </div> 
	  
 </div>

</div>
 </section>

  <section class="col-xs-12">
<!--

import ProjetoArquivosForm from './components/projeto_arquivos/ProjetoArquivosForm'
import ProjetoArquivosList from './components/projeto_arquivos/ProjetoArquivosList'


Vue.component('projeto_arquivos_form', ProjetoArquivosForm);
Vue.component('projeto_arquivos_list', ProjetoArquivosList );


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
              id_projeto: "",  
              id_operador: "",  
              arquivo: "",  
              meta_dados: "",  
              path: "",  
              tipo: "",  
				
            	disableButton: false,
            	publicar_titulo: "Salvar",
            	titulo_acao: "projeto_arquivos",
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


                          var url =  "projeto_arquivos/" + this.id_load; console.log("monted post url: " + url );
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
        self.id_projeto = item.id_projeto;   
        self.id_operador = item.id_operador;   
        self.arquivo = item.arquivo;   
        self.meta_dados = item.meta_dados;   
        self.path = item.path;   
        self.tipo = item.tipo;   
			   
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
                var url =  "projeto_arquivos"; console.log("url: " + window.URL_API + url );
                var method = "POST";

                if (this.id != null &&  this.id != ""){
                       method = "PUT"; url =  url + "/"+ this.id;
                }

                var data = {               id: this.id,  
  
				 data: obj_mask.dataBanco( $("#f_data").val() ) ,  
			 
              id_projeto: this.id_projeto,  
              id_operador: this.id_operador,  
              arquivo: this.arquivo,  
              meta_dados: this.meta_dados,  
              path: this.path,  
              tipo: this.tipo,   }


              var fn_return = function (retorno){
            
                  var item = retorno.item;
				  
				  self.carregaForm(item);

                  self.show_message = "on";
            	  self.message_text = "projeto_arquivos salvo com sucesso!";

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
        		obj_api.call_delete("projeto_arquivos", this.id, fn_return);
        	}
         }

    }


</script>
