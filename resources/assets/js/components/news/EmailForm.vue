<template>
<div>

<section class="col-lg-9">
      <h1>
        E-mail

      </h1>
      <ol class="breadcrumb" style="display: none">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Forms</a></li>
        <li class="active">General Elements</li>
      </ol>
    </section>
</section>

<section class="col-lg-3" style="padding-top: 30px">
        <a href="#"  v-on:click="botao_voltar">
           <i class="fa fa-arrow-left"></i> Voltar para lista
        </a> 
</section>

<div class="col-lg-12">
					<div v-if="show_message == 'on' " class="alert alert-success">
					      {{message_text}}
					</div>
</div>

<section class="col-lg-12">

 <div class="form-group">
                  <label for="post_title">Título</label>
                   <input class="form-control" name="e_email" 
                     id="e_email" v-model="email"
              type="text" placeholder="E-mail" >
                </div>


 <div class="form-group">
                	<div class="btn-group pull-right" >


                     <button type="submit" class="btn btn-info" 
                      v-bind:disabled="disableButton"
                     v-on:click="salvar('publish')">{{publicar_titulo}}</button> 


                     <button type="submit" class="btn btn-danger" 
                      v-bind:disabled="disableButton" v-if="id!='' && parseInt(id) > 0"
                     v-on:click="excluir('publish')">Excluir</button> 
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

            	disableButton: false,
            	publicar_titulo: "Salvar",
            	titulo_acao: "Email",
            	botao_voltar_visible: true,

            	show_message: "off",
            	message_text: "",
            	message_type: "success",
            	interval_message: null,


            }
        },
         mounted() {

           let self = this;


                          if ( this.id_load == null || this.id_load == ""){
                            return;
                          }


                          var url =  window.URL_API +"emailnews/" + this.id_load; console.log("monted post url: " + url );

                          this.disableButton = true;

                          var data = new FormData();
               
                          $.ajax({
                                  type: "GET",
                                  url: url,
                                  contentType: false,
                                  processData: false,
                                  data: data,
                                  success: function (retorno) {

                                  	if ( retorno.results.length <= 0 )
                                  		return;

                                    var item = retorno.results;
                                      
                                      console.log("LOAD: ");
                                      console.log(retorno);
                                      self.email = item.email;
                                      self.id = item.id;
                                      self.disableButton = false;
                                  },
                                  error: function (xhr, status, p3, p4) {
                                      var err = "Error " + " " + status + " " + p3 + " " + p4;
                                      if (xhr.responseText && xhr.responseText[0] == "{")
                                          err = JSON.parse(xhr.responseText).Message;


                                      console.error(err);

                                  }
                              }).fail(function (response) {
                                    console.log("Falha ao tentar obter dados");
                                    console.log(response);   
                              });

         },
         methods:{


        	botao_voltar(){
        		var self = this;

                 if ( this.onBack != null && this.onBack != undefined ){
                 	console.log("clique no voltar!");
                 	 this.onBack( self );
                 }
        	},

        	salvar (tipo ){

        	
                let self = this;
                var url =  window.URL_API +"emailnews"; console.log("url: " + url );
                var method = "POST";

                if (this.id != null &&  this.id != ""){
                       method = "PUT"; url =  url + "/"+ this.id;
                }

                var data = {email: this.email}


                  console.log("Enviado com o método "+ method + " - URL: " + url );
                  console.log( data );
     
                      $.ajax({
                              type: method,
                              url: url,
                              contentType: "application/x-www-form-urlencoded",
                              processData: true,
                              data: data,
                              success: function (retorno) {
                                  
                                  console.log(retorno);
                                   self.id = retorno.item.id;

                                   if ( self.onSave != null ){
                                      	self.onSave(retorno, tipo)
                                   }

			            	               self.show_message = "on";
			            	               self.message_text =  "Email salvo com sucesso!";
			            	               self.message_type = "success";

			            	               this.interval_message = setInterval(self.clear_message, 4000); 

                                  this.novo_item = "";
                              },
                              error: function (xhr, status, p3, p4) {
                                  var err = "Error " + " " + status + " " + p3 + " " + p4;
                                  if (xhr.responseText && xhr.responseText[0] == "{")
                                      err = JSON.parse(xhr.responseText).Message;


                                  console.error(err);

                              }
                          }).fail(function (response) {
                                console.log("Falha ao tentar obter dados");
                                console.log(response);   
                          });
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
        		obj_api.call_delete("emailnews", this.id, fn_return);
        	}
         }

    }


</script>
