<template>
          <div>

          	<span v-if="exibe_carregando"> Obtendo filmes
                        <img v-bind:src="img_loading | base_url" />
           </span>

          	<div class="col-xs-2">

                 <div class="input-group">
                  <input type="text"  v-model="codigo_novo" 
                         placeholder="Código do filme" id="filme_codigo" class="form-control" maxlength="50">
                      <span class="input-group-btn">
                        <button type="submit" class="btn btn-primary btn-flat" v-on:click="adicionar">
                            <i class="fa fa-plus"></i>
                        </button>
                      </span>
                </div>
          	</div>

          	<div class="col-xs-10">

				          	<div v-for="item in items" class="col-xs-2">
				          		<div class="input-group">
                                   <label>
                                         {{getTitle(item) }}
                                   </label>
				                      <span class="input-group-btn">

                                     
                              <button type="submit" class="btn btn-default btn-flat" v-on:click="change_description(item)">
                                    <i v-bind:class="getClassChecked(item)"></i>
                                </button>

				                        <button type="submit" class="btn btn-default btn-flat"
                                   v-on:click="remove_item(item)">
				                            <i class="fa fa-trash"></i>
				                        </button>
				                      </span>
                             </div>
				          	</div>


            </div>

          </div>
</template>
<script>
    export default {
    	   props: [ 'ptype'],

           data: function() {
            return {

                  codigo_novo: "",
                  items: [],

                   exibe_carregando: false,
                   img_loading: "loading.gif"

                  }
              },
           mounted() {
            

                let self = this;

                this.exibe_carregando = true;

                 var fn_return = function (retorno){

				             self.items = retorno.data;       

                             self.exibe_carregando = false;

				  }

				 obj_api.call("postgrid?type="+this.getPostType(), "get", {} , fn_return);

           },
		   methods: {

				   	getPostType(){
                              let post_type = "mov:";

                              if ( this.ptype == null || this.ptype == undefined ){
                              	 post_type = "mov:brv"; //Em breve.
                              }else{
                              	 post_type += this.ptype;
                              }

                              return post_type;

				   	},

            getClassChecked(item){

                 if ( item.description =="1")
                     return "fa fa-check-square";


                  return "fa fa-stop";
            },

            getTitle(item){

               if ( item.title_clean != null && item.title_clean != undefined)
                         return item.title_clean;

               return item.title;


            },

		        	adicionar(){
                             
                              if ( this.codigo_novo == ""){
                              	 $("#filme_codigo").focus();
                              	 return;
                              }

                              let post_type = this.getPostType();
                              let self = this;

                       var data = {title: this.codigo_novo, content: "", 
                                 description: "1", slug_type: "time",
                                  post_type: post_type };


                          console.log("pre envio: ");
                          console.log(data );

				              var fn_return = function (retorno){

				                     
                                    self.items.unshift(retorno.item);
                                    self.codigo_novo = "";

				              }

				              obj_api.call("post", "post", data , fn_return);
		        	},

              change_description(item){

                let new_description = item.description;
                let self = this;

                if ( new_description == "1"){
                    new_description = "";
                }else{
                    new_description = "1";

                }

                var index = this.items.indexOf(item);

                var fn_return = function(retorno){

                         Vue.set(self.items, index, retorno.item);
                }

                let title = item.title;
                let post_type = this.getPostType();

                if ( item.title_clean != null && item.title_clean != undefined){
                     title = item.title_clean;
                }


                var data = {title: title, content: "", 
                                 description: new_description, slug_type: "time",
                                  post_type: post_type };

                obj_api.call("post/"+item.id, "put", data , fn_return);

              },

		        	remove_item(item){
                              let self = this;

                              var fn_return = function (retorno){

                              	if ( retorno.code == "1"){

                                        self.items.splice(self.items.indexOf(item), 1);
                              	}

				              }


				                obj_api.call("post/"+item.id, "delete", {} , fn_return);
		        	},

              check_item(item){

                console.log(arguments);
              }

		        }
          }

  </script>        