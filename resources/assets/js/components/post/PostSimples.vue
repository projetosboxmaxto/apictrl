<template>

                 <div class="box" style="border: none">


                                 <div class="box-body ">
                                        <div class="col-xs-12">
                                                <div class="form-group">
                                                        <label for="post_title">Título</label>
                                                         <input class="form-control" name="post_title" 
                                                           id="post_title" v-model="title"
                                                    type="text" placeholder="Título" >
                                                      </div>
                                              <div class="form-group" v-if="slug != 'newsletter' " >
                                                                 <label for="post_subtitle" >Sub-Título</label>
                                                                  
                                               <input class="form-control" name="post_subtitle" 
                                                           id="post_subtitle" v-model="subtitle"
                                                    type="text" placeholder="SubTítulo" >

                                                       </div>
                                                       <div class="form-group" >
                                                                 <label for="post_content" >Texto</label>
                                                                 
                                                                 <textarea class="form-control" v-model="content" name="post_content" 
                                                                   id="post_content"  style="height: 100px"></textarea> 


                                                       </div>

                                                <div class="form-group pull-right">
                                                       <button 
                                                        v-on:click="salvar_textos"
                                                       class="btn btn-primary"
                                                              type="button" >Salvar </button>
                                                </div>
                                        </div>
                                </div>
                              </div>


</template>

<script>
    export default {
           props: ['slug'],

           data: function() {
           	return {
              title: "",
              content: "",
              
              subtitle: "",
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
                               self.subtitle = data.subtitle;

                  }

                 obj_api.call("postslug/" + this.slug, "get", {} , fn_return);

               },
               methods: {

                     salvar_textos(){

                            let self = this;
                            var data = {title: self.title, content: self.content, subtitle: self.subtitle };

                              var fn_return = function (retorno){

                                         obj_alert.show("Sucesso!", "Título e conteúdo alterados com sucesso!", "success", 3000);

                              }

                         obj_api.call("postedittext/"+ self.id, "put", data , fn_return);
                     }
               }


       }
 </script>      