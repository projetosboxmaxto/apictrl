<template>
  <div>
    <div v-bind:style="style_list()">
      <div style="padding-top: 10px">
        <div class="col-xs-11">
          <filtro_geral
            :onSearch="reload_table_search"
            v-model="data_filtro"
            :show_status="prop_status == null "
          ></filtro_geral>
        </div>
        <div class="col-xs-1" style="padding-top: 20px">
          <button class="btn btn-primary btn-lg pull-right" v-on:click="reload_table_search">
            <i class="fa fa-search" v-if="!loading"></i>
            <i class="fa fa-spinner" v-if="loading"></i>
            Filtrar
          </button>
          <button
            v-if="mostra_add"
            class="btn btn-default btn-lg"
            v-on:click="open_form"
            v-html="button_new_text"
          ></button>
        </div>
      </div>

      <div class="col-xs-12">
        <div class="col-xs-12" v-if="loading">
          <i class="fa fa-spinner"></i> Carregando....
        </div>
        <table
          id="table_data"
          class="table table-bordered table-striped display"
          style="width: 100%"
        >
          <thead>
            <tr>
              <th>ID</th>
              <th>ID Projeto</th>
              <th>Data</th>
              <th>Título</th>
              <th>Clientes</th>
              <th>Programa</th>
              <th>Emissora</th>
              <th>Operador</th>
              <th>Dt. Cadastro</th>
              <th>status</th>
              <th>ID matéria</th>
              <th style="width: 40px"></th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
    <div v-if="action =='form'" class="col-xs-12">
      <FormMateria
        v-bind:id_materia_rascunho="id"
        :id_evento="id_evento"
        v-bind:onSave="onSave"
        v-bind:onBack="onBack"
        v-bind:onRemove="onRemove"
      ></FormMateria>
    </div>
  </div>
</template>

<script>
import Util from "../../library/Util";
import filtro_geral from "../geral/filtro_geral";
//import CadMateria from "../eventos/CadMateria";
import FormMateria from "./Form";

export default {
  components: {
    filtro_geral: filtro_geral,
    FormMateria: FormMateria
  },
  props: {
    prop_status: {
      type: String,
      default() {
        return null;
      }
    }
  },
  data: function() {
    return {
      action: "list",
      id: "",
      id_evento: "",
      table: null,
      filtro_titulo: "",
      filtro_status: "",
      loading: false,
      mostra_add: false,

      show_new_button: true,
      data_filtro: {
        id_cliente: null,

        nome_cliente: "",
        id_programa: null,
        id_emissora: null,

        filtro_dtinicio: "",
        filtro_dtfim: "",
        status: ""
      },

      button_new_text: "" //<i class=\"fa fa-file\" ></i> NOVA POST"
    };
  },
  methods: {
    getObjFiltro() {
      var data = {
        dt_inicio: Util.BrDateToUS($("#filtro_dtinicio").val()),
        dt_fim: Util.BrDateToUS($("#filtro_dtfim").val())
      };

      if (this.prop_status != null) {
        this.data_filtro.status = this.prop_status;
      }

      if (this.data_filtro != null) {
        data["id_programa"] = this.data_filtro.id_programa;
        data["id_emissora"] = this.data_filtro.id_emissora;
        data["cliente_nome"] = this.data_filtro.cliente_nome;
        data["status"] = this.data_filtro.status;
      }

      console.log("filtro?");
      console.log(data);

      return data;
    },
    onBack(objPost) {
      //Clicou no back button.
      this.id = ""; //Voltando para a lista
      this.action = "list";
    },
    onRemove(ret) {
      this.id = ""; //Voltando para a lista
      this.action = "list";
      this.refresh_table();
    },

    open_form() {
      this.id = "";
      this.action = "form";
    },

    editar(datarow) {
      this.id = datarow.id;
      this.id_evento = datarow.id_projeto;
      this.action = "form";

      console.log("Vue recebeu o javascript:" + datarow.id);
      //  console.log( datarow );
    },
    onSave() {
      this.refresh_table();
    },

    refresh_table() {
      var page = this.table.page.info().page;
      this.reload_table_search(page);
    },

     reload_table_search() {
      if (this.table != null) {

        var filtro = this.getObjFiltro();
        if ( this.tinder ){
          filtro["status"] = "4";
          filtro["status_evento"] = "1,2";
        }
        var query_string = obj_api.serialize(filtro);
        if (query_string != "") {
          query_string = "?" + query_string;
        }

        var url =
          window.URL_API + "materia_rascunho_filtro2" + query_string;

        this.table.ajax.url(url);

        //console.log(url);
        //console.log(this.table );
        this.table.ajax.reload();
      }
    },

    reload_table_search_old(page) {
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

        obj_api.call("materia_rascunho_filtro", "POST", filtro, function(
          retorno
        ) {
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

    style_list() {
      if (this.action == "form") {
        return "display:none";
      }
      return "";
    },

    load_data() {
      let self = this;

      self.button_new_text = '<i class="fa fa-user" ></i> CADASTRAR';
      var filtro = this.data_filtro;

      if (this.prop_status != null) {
        filtro["status"] = this.prop_status;
      }

       var query_string = obj_api.serialize(filtro);
        if (query_string != "") {
        query_string = "?" + query_string;
        }


        var table = $("#table_data").DataTable({
          //"dom" : "Bfrtip",
          
          pagingType: "full_numbers",
          language: obj_datatable.getLanguage(),
          responsive: true,
          processing: true,
          lengthChange: false,
          searching: false,
          pageLength: 10,
          serverSide: true,

         ajax: {
                url: window.URL_API + "materia_rascunho_filtro2" + query_string,
                headers: {
                Authorization: window.API_AUTHORIZATION,
                apiauth: window.API_MYAUTH
                },
                error: function(xhr, textStatus, errorThrown) {
                // alert(errorThrown);

                if (xhr.responseText != null && xhr.responseText != undefined) {
                    //  self.sql = xhr.responseText;

                    $("#div_error_api").html(xhr.responseText);
                }

                // console.log(textStatus);
                },
                type: "POST",
                data: filtro,
                dataFilter: function(data) {
                var json = jQuery.parseJSON(data);
                json.recordsTotal = json.total;
                json.recordsFiltered = json.total;
                json.data = json.data;

                self.data_filtro.filtro_dtinicio = Util.dateToBR(json.dt_inicio);
                self.data_filtro.filtro_dtfim = Util.dateToBR(json.dt_fim);
                self.loading = false;
                //console.log(json);

                return JSON.stringify(json); // return JSON string
                }
            },
          columns: [
            { data: "id" },
            { data: "id_projeto" },
            { data: "data" },
            { data: "titulo" },
            { data: "cliente_list" },

            { data: "programa_nome" },
            { data: "emissora_nome" },
            { data: "nome_operador" },
            { data: "data_cadastro" },
            { data: "status" },
            { data: "id_materia_radiotv_jornal" },
            { data: "blnk" }
          ],
          order: [[0, "desc"]],

          columnDefs: [
            {
              // The `data` parameter refers to the data for the cell (defined by the
              // `data` option, which defaults to the column being worked with, in
              // this case `data: 0`.
              render: function(data, type, row) {
                return (
                  "<span style='display:none'>" +
                  data +
                  "</span>" +
                  Util.dateToBR(data)
                );
              },
              targets: 2
            },
            {
              // The `data` parameter refers to the data for the cell (defined by the
              // `data` option, which defaults to the column being worked with, in
              // this case `data: 0`.
              render: function(data, type, row) {
                return (
                  "<span style='display:none'>" +
                  data +
                  "</span>" +
                  Util.dateToBR(data)
                );
              },
              targets: 8
            },
            {
              // The `data` parameter refers to the data for the cell (defined by the
              // `data` option, which defaults to the column being worked with, in
              // this case `data: 0`.
              render: function(data, type, row) {
                return (
                  "<span style='display:none'>" +
                  data +
                  "</span>" +
                  (data == null || data == "0" ? "Rascunho" : "Matéria Criada")
                );
              },
              targets: 9
            },
            {
              // The `data` parameter refers to the data for the cell (defined by the
              // `data` option, which defaults to the column being worked with, in
              // this case `data: 0`.
              render: function(data, type, row) {
                var str_nome = "Editar";
                var icone = "fa fa-edit";
                //console.log(row);

                if (row.status != null && row.status == "1") {
                  str_nome = "Ver";
                  icone = "fa fa-tv";
                }

                return (
                  '<a href="#!"><i class="fa fa-tv"></i>' + str_nome + "</a>"
                );
              },
              targets: 11
            }
          ]

          /*, "columnDefs": [ {
                      "targets": 3,
                      "data": null,
                      "defaultContent": "<button>Click!</button>"
                  } 
                  ] */
        });

        self.table = table;

        $("#table_data tbody").on("click", "a", function() {
          var data = table.row($(this).parents("tr")).data();
          self.editar(data);
          //alert( data[0] +"'s salary is: "+ data[ 5 ] );
        });
    }
  },
  computed: {},
  mounted() {
    let self = this;

    $(document).ready(function() {
      self.load_data();
    });
  }
};
</script>
