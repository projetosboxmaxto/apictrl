<template>
  <div>
  <div v-bind:style="style_list()">

    <div style="padding-top: 10px">
    <div class="col-md-9">
              <div class="form-group">
                <label>Filtrar</label>
                <input type="text" class="form-control"
                 v-model="filtro_titulo"
                 placeholder="Digite para pesquisar">
               
              </div>
          </div>
    <div class="col-md-3" style="padding-top: 20px">
                   <button class="btn btn-primary btn-lg"  v-on:click="reload_table_search" >Filtrar</button>
         <button class="btn btn-default btn-lg" v-if="show_new_button"  v-on:click="open_form" v-html="button_new_text" >
                     </button>

    </div>  
</div>

    <div class="col-xs-12" >
 


   


<table id="table_data" class="table table-bordered table-striped display" style="width: 100%">
        <thead>
            <tr>
                      <th>ID</th>  
                      <th>Cliente</th>  
                      <th>Usa consulta comum (Like?)</th>  
                      <th>Usa Elastic Search ?</th>  
                <th></th>
            </tr>
        </thead>
       
    </table>
  </div>
</div>
   <div v-if="action =='form'" class="col-xs-12">
         <cliente_configuracao_form  v-bind:id_load="id" 
          :nome_cliente="nome_cliente"
          v-bind:onSave="onSave" 
          :onEdit="onEdit"
          show_back_button="true" v-bind:onBack="onBack"></cliente_configuracao_form>
  </div>
  <div v-if="action =='editar_elastic'" class="col-xs-12">
    
        

        <section class="col-lg-12">
          <section class="col-lg-9" style="padding-left: 0px; margin-left: 0px">
              <h1 style="padding-left: 0px; margin-left: 0px">Elastic Search - {{nome_cliente}}

              </h1>
            </section>

        <section class="col-lg-3" style="padding-top: 30px">
                <a href="#"  v-on:click="onEdit('form')" >
                  <i class="fa fa-arrow-left"></i> Voltar para configuração
                </a> 
        </section>
        </section>
         <elastic_queries_list_cad_table  v-bind:id_load="id" 
          :nome_cliente="nome_cliente"
          show_back_button="true" v-bind:onBack="onBack"></elastic_queries_list_cad_table>
  </div>

    <div v-if="action =='editar_palavra'" class="col-xs-12">
    
        

        <section class="col-lg-12">
          <section class="col-lg-9" style="padding-left: 0px; margin-left: 0px">
              <h1 style="padding-left: 0px; margin-left: 0px">Filtro Geral - {{nome_cliente}}

              </h1>
            </section>

        <section class="col-lg-3" style="padding-top: 30px">
                <a href="#"  v-on:click="onEdit('form')" >
                  <i class="fa fa-arrow-left"></i> Voltar para configuração
                </a> 
        </section>
        </section>
        
        <section class="col-lg-12" >
         <tab_palavras_chave  v-bind:id_load="id" 
          :nome_cliente="nome_cliente"
          show_back_button="true" v-bind:onBack="onBack"></tab_palavras_chave>
            </section>
  </div>

  

</div>
</template>

<script>
import TabPalavrasChave from './TabPalavrasChave';
import ElasticQueriesListCadTable from '../elastic_queries/ElasticQueriesListCadTable';

    export default {
      components:{
        tab_palavras_chave: TabPalavrasChave,
        elastic_queries_list_cad_table:  ElasticQueriesListCadTable
      },

        data: function() {
            return {

              action: "list",
              id: "",
              nome_cliente: "",
              table: null,
              filtro_titulo: "",
              filtro_status: "",

              show_new_button: false,

              button_new_text: "" //<i class=\"fa fa-file\" ></i> NOVA POST"
            }
        },
        methods: {


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
            this.nome_cliente = datarow.nome;
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
          onEdit( tipo ){

            this.action = tipo;
          },
          getObjFiltro (){
            var data = {filtro: this.filtro_titulo}
            return data;
          },

           reload_table_search(page) {
                  var self = this;
                  self.loading = true;

                  // this.filtro_dtinicio = $("#filtro_dtinicio").val();
                  // this.filtro_dtfim = $("#filtro_dtfim").val();

                  if (this.table != null) {
                    /* var url =
                      window.URL_API + "eventos?ret=api&filtro=" + this.filtro_titulo;

                    this.table.ajax.url(url);

                    this.table.ajax.reload(); */

                    var filtro = this.getObjFiltro();

                    obj_api.call("cliente_configuracao", "GET", filtro, function(retorno) {              
                      console.log( retorno  );
                      var dataSet = retorno.data;

                      self.table.clear().draw();
                      self.table.rows.add(dataSet); // Add new data

                      if (page != null && page != undefined && page > 0) {
                        // self.table.displayStart = page; //fnPageChange(page, true); //this.table.displayStart
                        self.table.columns.adjust().draw(); // Redraw the DataTable
                      } else {
                        self.table.columns.adjust().draw();
                      }
                      self.loading = false;
                    });
                  }
              },

              style_list(){
                if ( this.action != "list"  ){
                  return "display:none";
                }
                return "";
              },

              load_data(){
                            let self = this;

                            self.button_new_text = '<i class="fa fa-user" ></i> CADASTRAR';

                            var filtro = { filtro: this.filtro_titulo };

                            obj_api.call("cliente_configuracao", "GET", filtro, function(retorno) {
                
                              var dataSet = retorno.data;

                              var table = $("#table_data").DataTable({
                                //"dom" : "Bfrtip",
                                pagingType: "full_numbers",
                                language: obj_datatable.getLanguage(),
                                responsive: true,
                                processing: true,
                                lengthChange: false,
                                searching: false,
                                data: dataSet,

                                columns: [
                                      { "data": "id" },  
                                      { "data": "nome" },  
                                      { "data": "consulta_comum" },  
                                      { "data": "consulta_elastic" },  
                                  { data: "blnk" }
                                ],
                                order: [[1, "asc"]],

                                columnDefs: [
                                  {
                                    // The `data` parameter refers to the data for the cell (defined by the
                                    // `data` option, which defaults to the column being worked with, in
                                    // this case `data: 0`.
                                    render: function(data, type, row) {
                                      return data != null &&  data == 1 ? "Sim" : "Não";
                                    },
                                    targets: 2
                                  },
                                    {
                                    // The `data` parameter refers to the data for the cell (defined by the
                                    // `data` option, which defaults to the column being worked with, in
                                    // this case `data: 0`.
                                    render: function(data, type, row) {
                                      return data != null &&  data == 1 ? "Sim" : "Não";
                                    },
                                    targets: 3
                                  },
                                    {
                                          // The `data` parameter refers to the data for the cell (defined by the
                                          // `data` option, which defaults to the column being worked with, in
                                          // this case `data: 0`.
                                          render: function(data, type, row) {
                                            return '<a href="#!" class="pull-right"><i class="fa fa-cogs"></i> Visualizar</a>';
                                          },
                                          targets: 4
                                        }
                                ]
                              });

                              self.table = table;

                              $("#table_data tbody").on("click", "a", function() {
                                var data = table.row($(this).parents("tr")).data();
                                self.editar(data);
                                //alert( data[0] +"'s salary is: "+ data[ 5 ] );
                              });
                            });

                            $(document).ready(function() {
                              console.log("URL: " + window.URL_API + "search_queries");
                              console.log("Type: " + self.type);
                            });
                          }
        },
        computed: {
              
        },
        mounted() {

           let self = this;

           self.button_new_text =  "<i class=\"fa fa-user\" ></i> CADASTRAR";
              this.load_data(); 

          }
     }
 
    </script>
