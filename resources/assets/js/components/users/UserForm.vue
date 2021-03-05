<template>
<div>

<section class="col-lg-12">
	<section class="col-lg-9" style="padding-left: 0px; margin-left: 0px">
      <h1 style="padding-left: 0px; margin-left: 0px">Usuário

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


<section v-bind:class="getClassFirstSection()">
<div class="box"><div class="box-header with-border"></div> 
<div class="box-body">

	 <div class="form-group">
                  <label for="e_nome">Nome</label>
                   <input class="form-control" name="u_nome" 
                     id="u_nome" v-model="nome"
              type="text" placeholder="Nome" >
                </div>

 <div class="form-group">
                  <label for="e_email">Email / Login</label>
                   <input class="form-control" name="u_email" 
                     id="u_email" v-model="email"
              type="text" placeholder="E-mail" >
                </div>


 <div class="form-group">
                  <label for="e_email">Nível de Acesso</label>
                   <select class="form-control" name="u_group_id" 
                     id="u_group_id" v-model="group_id">
                         <option v-bind:value="group.id" v-for="group in group_list">{{group.nome}}</option>

                   </select>
                </div>


               <div class="form-group">
                  <label for="e_email">Senha</label>
                   <input class="form-control" name="u_senha" 
                     id="u_senha" v-model="senha"
              type="password" placeholder="Senha" >
                </div>

                 <div class="form-group">
                  <label for="e_email">Confirmar Senha</label>
                   <input class="form-control" name="u_conf_senha" 
                     id="u_conf_senha" v-model="confirmar_senha"
              type="password" placeholder="Confirmar senha" >
                </div>

</div>
</div>
 </section>


 <section class="col-xs-3" v-if="id !='' && parseInt(id) > 0">   
<upload-unico 
   
v-bind:id_parent="id" title="Imagem do Perfil" ptype="u:photo"></upload-unico>
  </section>

  <section class="col-xs-12">



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
            	email: "",
            	nome:"",
            	senha:"",
            	confirmar_senha:"",
              group_id: 1,

              group_list: [],

            	disableButton: false,
            	publicar_titulo: "Salvar",
            	titulo_acao: "Usuário",
            	botao_voltar_visible: false,

            	show_message: "off",
            	message_text: "",
            	message_type: "success",
            	interval_message: null,


            }
        },
        mounted() {

                let self = this;



                    var fn_return2 = function (retorno){
                         self.group_list = retorno.data;
                         console.log("Group list");
                         console.log(self.group_list);
                    }


                    obj_api.call("user_group", "GET", {} , fn_return2);
   
                            if ( this.show_back_button != null && this.show_back_button != undefined  ){
                                     this.botao_voltar_visible = this.show_back_button;
                            }


                          if ( this.id_load == null || this.id_load == ""){
                            return;
                          }


                          var url =  "user/" + this.id_load; console.log("monted post url: " + url );
                          var method = "get";

                          this.disableButton = true;

                          var data = { }


			              var fn_return = function (retorno){

                                           // console.log("Retorno? ");
                                           // console.log( retorno );
                                            
			              	     var item = retorno.item;

			              	     self.id = item.id;
			              	     self.email = item.email;
			              	     self.nome = item.nome;
                           self.group_id = item.group_id;
			              	     self.senha = "";
			              	     self.confirmar_senha = "";


                                 self.disableButton = false;

			              }

			              obj_api.call(url, method, data , fn_return);


                    



         },
         methods:{

         	getClassFirstSection(){

         		if ( this.id != "")
         			return "col-xs-9";

         		return "col-xs-12";
         	},

         	validar(){

                       if ( obj_alert.isvazioInput("u_nome","Informe o nome!"))
                       	    return false;

                       if ( obj_alert.isvazioInput("u_email","Informe o e-mail!"))
                       	    return false;

                       	if ( this.id == ""){
                       		    if ( obj_alert.isvazioInput("u_senha","Informe a senha!"))
                       	                 return false;

                       	        
                       	}

                       	if ( this.senha != ""){

                       		if ( obj_alert.isvazioInput("u_conf_senha","Confirme a senha"))
                       	                 return false;

                       		if ( this.senha != this.confirmar_senha){
                                  obj_alert.show("Atenção","Senha e confirmação não conferem",
                                  	"warning", null, 4000);

                                  return false;
                       		}
                       	}

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
                var url =  "user"; console.log("url: " + window.URL_API + url );
                var method = "POST";

                if (this.id != null &&  this.id != ""){
                       method = "PUT"; url =  url + "/"+ this.id;
                }

                var data = {email: this.email, login: this.email,
                            senha: this.senha, nome: this.nome, group_id: this.group_id }


              var fn_return = function (retorno){
            
                  var item = retorno.item;

              	  self.id = item.id;
      			      self.email = item.email;
      			      self.nome = item.nome;
      			      self.senha = "";
      			      self.confirmar_senha = "";

                  self.show_message = "on";
            	  self.message_text = "Usuário salvo com sucesso!";

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
        		obj_api.call_delete("user", this.id, fn_return);
        	}
         }

    }


</script>
