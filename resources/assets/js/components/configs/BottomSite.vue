<template>
          <div>
 


          	<div class="col-xs-8">
               <div class="form-group">
                  <label >Nome</label>
                   <input v-if="em_edicao" class="form-control"  v-model="title"
                                type="text" placeholder="Nome" >

                    <span v-if="!em_edicao" v-html="title"></span>            
                </div>

                 <div class="form-group">
                  <label for="post_title">Descrição</label>
                   <input v-if="em_edicao" class="form-control"  v-model="content"
                                type="text" placeholder="Descrição" >


                    <span v-if="!em_edicao" v-html="content"></span> 
                </div>

                 <div class="form-group">
                  <label for="post_title">URL Google Maps</label>
                   <input v-if="em_edicao" class="form-control"  v-model="description"
                                type="text" placeholder="URL Google Maps" >


                   <span v-if="!em_edicao" >
                       <a v-html="show_description()" v-bind:href="{description}" target="_blank"></a>
                       
                   </span>



                </div>
            </div>


          	<div class="col-xs-4" style="padding-top: 15px">

						<upload-unico 
						   v-if="id !='' && parseInt(id) > 0"
						v-bind:id_parent="id" title="Imagem"
                        v-bind:habilita_edicao="getEmEdicao()" 
						 ptype="bot:img" com_classes="0"></upload-unico>
          	</div>

          	
                   <div v-bind:class="getClassBotao()">
                   <div class="pull-right">
                        <button class="btn btn-primary"  v-if="show_add_button" 
                            v-on:click="add">
                        	<i class="fa fa-plus-o"></i> Adicionar

                        </button>

                        <button class="btn btn-secondary" v-if="!em_edicao" 
                  v-on:click="hab_edicao()">Editar</button>
                 
                        <button class="btn btn-primary"
                         v-on:click="update" 
                         v-if="em_edicao && !show_add_button" >Salvar</button>

                           <button class="btn btn-danger"
                         v-on:click="excluir" 
                         v-if="em_edicao && !show_add_button" >Remover</button>

                   </div>
                       
                  </div>
         
      

          </div>





</template>
<script>
    export default {
    	   props: ['item', 'botao_add', 'onSave'],

           data: function() {
            return {

                  title: "",
                  description: "",
                  content: "",
                  id: "",

                  items: [],

                  em_edicao: false,
                  show_add_button: false
            }
           },
           mounted() {
                       
                       if ( this.item != null && this.item != undefined ){

                       	           this.id = this.item.id;
                       	           this.content = this.item.content;
                       	           this.description = this.item.description;

                                   if (this.item.title_clean != null && this.item.title_clean != undefined ){

                                        this.title = this.item.title_clean;
                                   }else{

                                        this.title = this.item.title;
                                   }
                       }

                       if ( this.botao_add != null && this.botao_add != undefined ){
                                 this.show_add_button = this.botao_add=="1";
                                 if ( this.show_add_button ){
                                 	       this.em_edicao = true;
                                 }
                       }

           },


	        methods: {
                    
                    show_description(){
                        
                        if ( this.description.length > 80 ){
                            return this.description.substr(0, 80)+" ...";
                        }
                        
                        return this.description;
                        
                        
                    },

	        	getClassBotao(){
                          
                          if ( this.id !='' && parseInt(this.id) > 0 )
                          	  return "col-xs-12";


                          	return "col-xs-8";
	        	},

	        	getEmEdicao(){
	        		if ( this.em_edicao )
	        			return "1";

	        		return "0";
	        	},
	        	hab_edicao(){
	        		this.em_edicao = true;
	        	},

	        	add(){

              let self = this;

	        		if ( this.title == ""){
	        			obj_alert.show("Atenção","Informe o nome!","warning");
	        			return;
	        		}

              var data = {title: this.title, content: this.content, 
                          description: this.description, slug_type: "time",
                          post_type: "bottom" };


                          console.log("pre envio: ");
                          console.log(data );

              var fn_return = function (retorno){

                     if ( self.onSave != null && self.onSave != undefined ){
                            self.onSave(retorno, 'save');

                            self.title = "";
                            self.content = "";
                            self.description = "";
                     }

              }

              obj_api.call("post", "post", data , fn_return);
	        	},

            update(){

              let self = this;

              var data = {title: this.title, content: this.content, 
                          description: this.description,
                          post_type: "bottom" };


              var fn_return = function (retorno){

                   //  self.em_edicao = false;
                   obj_alert.show("Sucesso!","rodapé atualizado com sucesso!","success", null, 3000);


                     if ( self.onSave != null && self.onSave != undefined ){
                            self.onSave(retorno, 'update');
                     }

                     self.em_edicao = false;

              }

              obj_api.call("post/" +self.id , "put", data , fn_return);
 

            },

            excluir( ){



              let self = this;

              var data = {post_type: "bottom" };


              var fn_return = function (retorno){

                   //  self.em_edicao = false;
                   obj_alert.show("Sucesso!","rodapé removido com sucesso!","success", null, 3000);



                     if ( self.onSave != null && self.onSave != undefined ){
                            self.onSave(retorno, 'delete');
                     }

              }

              obj_api.call("post/" +self.id , "delete", data , fn_return);

            }

         

	        }


    }
</script>