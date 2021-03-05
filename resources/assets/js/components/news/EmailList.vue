<template>
  <div>
  <div v-bind:style="style_list()">

    <div style="padding-top: 10px">

    <div class="col-xs-12" >
          <div class="input-group">
   
                <input type="text" class="form-control"
                 v-model="filtro_titulo"
                 placeholder="Digite para pesquisar o email">
        

                   <span class="input-group-btn">

                   <button class="btn btn-primary btn-sm"  v-on:click="reload_table_search" >
 <i class="fa fa-search"></i>
                   Filtrar</button>

      <button class="btn bg-olive btn-sm"  v-on:click="open_exportar"
          >
   <i class="fa fa-file-text"></i>
      Exportar</button>

         <button class="btn btn-default btn-sm"  v-on:click="open_form" v-html="button_new_text" >
                     </button>
                   </span>
          </div>
        </div>

</div>

    <div class="col-xs-12" >
 


   


<table id="table_data" class="table table-bordered table-striped display" style="width: 100%">
        <thead>
            <tr>
                <th>ID</th>
                <th>Email</th>
                <th>Data</th>
                <th></th>
            </tr>
        </thead>
       
    </table>
  </div>
</div>
  <email-form v-if="action =='form'" v-bind:id_load="id" 
          v-bind:onSave="onSave" v-bind:post_type="getType()"
  show_back_button="true" v-bind:onBack="onBack"></email-form>
</div>
</template>

<script>
    export default {
        props: [ 'post_type'],

        data: function() {
            return {

              action: "list",
              id: "",
              table: null,
              filtro_titulo: "",
              filtro_status: "",
              type: this.getType(),

              button_new_text: "" //<i class=\"fa fa-file\" ></i> NOVA POST"
            }
        },
        methods: {

          getType(){
                if ( this.post_type != null && this.post_type != undefined )
                  return this.post_type;

                return "email";
          },

          onBack ( objPost ){
              //Clicou no back button.
              this.id = ""; //Voltando para a lista
              this.action = "list";
          },

          open_form (){
                    this.id = "";
                    this.action = "form";

          },

          editar ( datarow ){

            this.id = datarow.id;
            this.action = "form";

                     console.log("Vue recebeu o javascript:" + datarow.id );
                   //  console.log( datarow );
          },
          onSave(){
                  this.refresh_table();
          },

          refresh_table(){
                     if ( this.table != null ){
                       this.table.ajax.reload( null, false ); // user paging is not reset on reload
                     }
          },

          reload_table_search(){

                 if ( this.table != null ){

                      var url = window.URL_API +"emailnews?filtro="+
                      this.filtro_titulo;

                      this.table.ajax.url( url ); 


                      console.log(url);
                      console.log(this.table );
                    this.table.ajax.reload( ); 
                 }
                     
          },

          style_list(){
                if ( this.action == "form" ){
                  return "display:none";
                }
                return "";
              },

              open_exportar(){
                       var url = window.URL_BASE + "emailnewsdownload";
                       window.open(url,'emails_download');

              }
        },
        computed: {
              
        },
        mounted() {

           let self = this;

            self.button_new_text = "<i class=\"fa fa-envelope\" ></i> NOVO EMAIL"

                     $(document).ready(function() {
        

                        var table = $('#table_data').DataTable( {
                              //"dom" : "Bfrtip",
                            "pagingType": "full_numbers",
                            "language" : obj_datatable.getLanguage(),
                            "responsive": true,
                            "processing": true,
                                          "lengthChange": false,
                                          'searching'   : false,
                            //"serverSide": true,
                                 "ajax": {
                                "url" : window.URL_API +"emailnews",
                                "type": "GET",
                                "data": { 
                                          filter: self.filter_title }} , 

                                "columns": [
                                        { "data": "id" },
                                        { "data": "email" },
                                        { "data": "data" },
                                        { "data": "blnk" }],
                            "order": [[ 0, "desc" ]]
                            
                        } );

              self.table = table;
           
              $('#table_data tbody').on( 'click', 'a', function () {
                  var data = table.row( $(this).parents('tr') ).data();
                  self.editar(data);
                  //alert( data[0] +"'s salary is: "+ data[ 5 ] );
              } );
            });

          }
     }
 
    </script>
