<template>
<div>

<section class="col-lg-12">
	<section class="col-lg-9" style="padding-left: 0px; margin-left: 0px">
      <h1 style="padding-left: 0px; margin-left: 0px">Grupo de Acesso

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
								  <label for="f_nome">Nome</label>
								   <input class="form-control" name="f_nome" 
									 id="f_nome" v-model="nome"
							  type="text" placeholder="Nome" > {{exibe_error('nome')}}
                  
    </div> 
 <div class="form-group" v-if="id != '' && parseInt(id) > 1">  


  <table class="table table-bordered table-striped" style="width: auto; border: solid 4px #e3e5e8">
   <thead>
           <tr>
                     <td colspan="2">
                 <label for="f_menu_enable_json">Menu: </label>
                                  <select v-model="menu_add">
                                          <option :value="index" v-for="(item, index) in items_menu"
                                             v-if="item.menu">
                                                  {{item.title}}
                                          </option>
                                  </select>
                          <button class="btn btn-primary btn-xs pull-right"
                           v-on:click="adicionar()"
                           type="button">Adicionar</button>
                     </td>
                    
           </tr>
  </thead>

    <tbody>
      <tr v-for="(item, index) in items" :style="get_row_estilo(index)">
        <td>
           {{item.title}}
        </td>
        <td>

                          <button class="btn btn-default btn-xs" type="button"
                                     v-on:click="excluir_item(index)"
                          >
                            
                            <i class="fa fa-trash"></i>
                          </button>
        </td>

      </tr>
    </tbody>

  </table>


								   <input class="form-control" name="f_menu_enable_json" 
									 id="f_menu_enable_json" v-model="menu_enable_json"
							  type="hidden" placeholder="menu_enable_json" > 
                  
    </div> 
	  
 </div>

</div>
 </section>

  <section class="col-xs-12">
<!--

import UserGroupForm from './components/user_group/UserGroupForm'
import UserGroupList from './components/user_group/UserGroupList'


Vue.component('user_group_form', UserGroupForm);
Vue.component('user_group_list', UserGroupList );


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
      props: ['id_load',  'post_type', 'show_back_button','onBack', 'onSave'],
       data: function() {
            return {
            	
				      id: "",  
              nome: "",  
              menu_enable_json: "", 

              items_menu: [], 
              items: [],

              menu_add: -1,
				
            	disableButton: false,
            	publicar_titulo: "Salvar",
            	titulo_acao: "Grupo de Acesso",
            	botao_voltar_visible: false,

            	show_message: "off",
            	message_text: "",
            	message_type: "success",
            	interval_message: null,


            }
        },
        mounted() {

                let self = this;


                this.items_menu = this.$router.options.routes;
   
                            if ( this.show_back_button != null && this.show_back_button != undefined  ){
                                     this.botao_voltar_visible = this.show_back_button;
                            }


                          if ( this.id_load == null || this.id_load == ""){
                            return;
                          }


                          var url =  "user_group/" + this.id_load; console.log("monted post url: " + url );
                          var method = "get";

                          this.disableButton = true;

                          var data = { }


			              var fn_return = function (retorno){

                                           // console.log("Retorno? ");
                                           // console.log( retorno );
                                            
			              	     var item = retorno.item;

			              	             self.id = item.id;   
                                  self.nome = item.nome;   
                                  self.menu_enable_json = item.menu_enable_json;   
                                  
                         if ( item.menu_enable_json != null && item.menu_enable_json != ""){

                              self.items = JSON.parse( self.menu_enable_json );
                          }


                                 self.disableButton = false;

			              }

			              obj_api.call(url, method, data , fn_return);



         },
         methods:{

            excluir_item(index){
                     let self = this;
                     console.log("Removendo o índice: " + index );
                     self.items.splice(index, 1) ;

                    //self.menu_enable_json = JSON.stringify(  self.items );
            },

            adicionar(){

                var self = this;
                var index = self.menu_add;

                var item = self.items_menu[index];

                for(var i = 0; i < self.items.length; i++){
                   if ( self.items[i].name == item.name ){
                          obj_alert.show("Sucesso!","Este item do menu já existe","warning");
                          return;
                   }
                }

                self.items.push( item );

                //self.menu_enable_json = JSON.stringify(  self.items );


            },

            get_row_estilo(index){

              if ( (index % 2) > 0 ){
                return "background: #e3e5e8";
              }

              return "";

            },
		 
		 
            exibe_error( tipo ){
                    
            },

         	getClassFirstSection(){

         		if ( this.id != "")
         			return "col-xs-9";

         		return "col-xs-12";
         	},

         	validar(){
			
			             if ( obj_alert.isvazioInput("f_nome","Informe o Nome!"))
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
                var url =  "user_group"; console.log("url: " + window.URL_API + url );
                var method = "POST";

                if (this.id != null &&  this.id != ""){
                       method = "PUT"; url =  url + "/"+ this.id;
                }
                self.menu_enable_json = JSON.stringify(  self.items ); 

              
              var data = {               id: this.id,  
                                           nome: this.nome,  
                                            menu_enable_json: this.menu_enable_json,   }


              var fn_return = function (retorno){
            
                  var item = retorno.item;

              	          self.id = item.id;   
                          self.nome = item.nome;   
                          self.menu_enable_json =  item.menu_enable_json ;

                          if ( item.menu_enable_json != null && item.menu_enable_json != ""){

                              self.items = JSON.parse( self.menu_enable_json );
                          }

                          console.log("Estou aqui?? "); console.log( self.items );


                  self.show_message = "on";
            	  self.message_text = "Grupo de Acesso salvo com sucesso!";

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
        		obj_api.call_delete("user_group", this.id, fn_return);
        	}
         }

    }


</script>
