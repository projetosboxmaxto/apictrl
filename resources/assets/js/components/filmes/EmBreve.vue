<template>
          <div >


              <div class="col-xs-12">
                  	       <div class="box box-default">

                      	 	  <div class="box-header">
                      	 	  	<div class="col-xs-12">

                                       <h3 style="margin-left: 0px; margin-top: 0px">Filmes em Breve</h3>
                      	 	  	</div>
                      	 	  </div>




                                 <div class="box-body">
                                 	<filme-code ptype="brv"></filme-code>
                                 </div>
                  	 	       </div>
             </div>     
          	


              <div class="col-xs-12">
                  
                     <post-simples slug="em-breve" ></post-simples>
                  
              </div>


          		<div class="col-xs-12">
			          	 <div class="box box-default">

			          	 	  <div class="box-header">
				          	 	  	<div class="col-xs-12">

				                           <h3 style="margin-left: 0px; margin-top: 0px">Background</h3>
				          	 	  	</div>
				          	 	  </div>
				          	 	  <div class="box-body">

				                         	<filme-back ptype="brv:back"></filme-back>
				          	 	  </div>


			          	 </div>
          	  </div>

          </div>

</template>

<script>
    export default {

           data: function() {
           	return {
              title: "",
              content: "",
              id: ""

           	}
           },
           mounted() {
                    

                 let self = this;

                 this.exibe_carregando = true;

                 var fn_return = function (retorno){

                             var data = retorno.data;       

                               self.title = data.title;
                               self.content = data.content;
                               self.id = data.id;

                  }

                 obj_api.call("postslug/em-breve", "get", {} , fn_return);

               },
               methods: {

                     salvar_textos(){

                            let self = this;
                            var data = {title: self.title, content: self.content};

                              var fn_return = function (retorno){

                                         obj_alert.show("Sucesso!", "Título e conteúdo alterados com sucesso!", "success", 3000);

                              }

                         obj_api.call("postedittext/"+ self.id, "put", data , fn_return);
                     }
               }


       }
 </script>      