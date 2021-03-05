<template>
      <div class="box" v-bind:style="display_template">
            <div class="box-header with-border">
              <h3 class="box-title">{{title_data}}
              </h3>

              <div class="box-tools pull-right" style="display:none">
                <a class="btn btn-box-tool" id="a_show_categoria" 
                myhref="#body_categoria" href="#!"
                 ><i class="fa fa-minus"></i>
                </a>
              </div>
              <!-- /.box-tools -->
            </div>
            <!-- /.box-header -->
            <div class="box-body" id="body_categoria" style="max-height: 200px; overflow-y: scroll;">
                     <div v-for="item in items" >
                          <input type="checkbox" v-bind:value="item.id" class="chk_category" 
                                  v-bind:id="checkboxid(item.id)"
                                  v-bind:checked="ischecked(item.id)"/>
                                  <label v-bind:for="checkboxid(item.id)">{{item.name}}</label> 
                     </div>

                      <!-- Comment -->
            </div>
            <!-- /.box-body -->
          
            <div class="box-footer">
               <div class="input-group">
                  <input type="text" v-bind:name="get_nameid('novo_item')" v-bind:id="get_nameid('novo_item')" v-model="novo_item" :placeholder="placeHoder_new" class="form-control" maxlength="300">
                      <span class="input-group-btn">
                        <button type="submit" class="btn btn-primary btn-flat" v-on:click="adicionar">
                            <i class="fa fa-plus"></i>
                        </button>
                      </span>
                </div>
            </div>
          </div>

</template>

<script>
    export default {
        props: ['title', 'prefixo', 'selecteds', 'api_path', 'show'],

        data: function() {
            return {
                isFavorited: '',
                title_data: this.title,
                placeHoder_new: 'Nova ' + this.title,
                items: this.selecteds,
                novo_item: '',
                display_template: (this.show != null && this.show == "0" ? "display:none" : "")
            }
        },

        mounted() {
            

                let self = this;

                var url =  window.URL_API +"terms?type=category"; console.log("url: " + url );

                var data = new FormData();
                data.append("type", "category");
     
                $.ajax({
                        type: "GET",
                        url: url,
                        contentType: false,
                        processData: false,
                        data: data,
                        success: function (retorno) {
                            
                            console.log( retorno );
                            self.items = retorno;
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

        computed: {
         //   isFavorite() {
          //      return this.favorited;
          //  },
        },

        methods: {

            collapse_body(){

                var collapsedId = $("#a_show_categoria").attr('myhref');
                console.log("estou aqui? " + collapsedId);
                var isVisible = $(collapsedId + ' .panel-body').is(':visible');

                console.log("estou no collapse? ");
                console.log( $(this).html() );

                    if (isVisible == true) {
                        $(collapsedId).collapse('hide');
                        //$(this).html("<i class='fa fa-plus'></i>");
                        return false;
                    }else{
                        //$(this).html("<i class='fa fa-minus'></i>");
                         return false;
                    }

            },

            check_item (id, obj){

            },
            ischecked( id ){

                if ( this.selecteds != null && this.selecteds != undefined ){
                               for ( var i = 0; i < this.selecteds.length; i++ ){
                                   if ( this.selecteds[i].id == id ){
                                       return true;
                                   }
                               }
                }

                return false;

            },

            get_nameid(id){

                return this.prefixo + id;
            },

            checkboxid( id ){
                return this.prefixo + "chk_" + id;
            },

            adicionar(){

              var txt_id = this.get_nameid("novo_item");

              console.log( "item digitado: " + this.novo_item  +" - ID: " + txt_id );

                if ( this.novo_item == "" ){
                      $("#" + txt_id ).focus();
                }else{

                   let self = this;

                   var url =  window.URL_API +"terms"; console.log("url: " + url );

                   var data = new FormData();
                   data.append("type", "category");
                   data.append("name", this.novo_item);
     
                      $.ajax({
                              type: "POST",
                              url: url,
                              contentType: false,
                              processData: false,
                              data: data,
                              success: function (retorno) {
                                  
                                  if ( retorno.code == "2"){
                                    alert(retorno.msg);
                                  }

                                  if ( retorno.code=="3"){
                                      if ( self.selecteds == null )
                                          self.selecteds = [];

                                       self.selecteds.unshift(retorno.item); 
                                  }

                                  if ( retorno.code=="1"){

                                     if ( self.selecteds == null )
                                          self.selecteds = [];

                                         self.selecteds.unshift(retorno.item);
                                         self.items.unshift(retorno.item);

                                  }

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





                }


            },

        
        }
    }
</script>