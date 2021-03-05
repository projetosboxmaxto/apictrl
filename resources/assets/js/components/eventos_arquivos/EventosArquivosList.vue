<template>
  <div>
    <div v-bind:style="style_list()">
      <div style="padding-top: 10px">
        <div class="col-md-2">
          <div class="form-group">
            <label>Data Início</label>
            <input
              type="text"
              class="form-control"
              id="filtro_dtinicio"
              v-model="filtro_dtinicio"
              placeholder="Data Início"
            />
          </div>
        </div>

        <div class="col-md-2">
          <div class="form-group">
            <label>Data Fim</label>
            <input
              type="text"
              class="form-control"
              id="filtro_dtfim"
              v-model="filtro_dtfim"
              placeholder="Data Fim"
            />
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label>Cliente</label>
            <select id="id_cliente" name="id_cliente" v-model="id_cliente" class="form-control">
              <option>--TODOS--</option>
              <option v-for="(item, index) in clientes" :key="index" :value="item.id">{{item.nome}}</option>
            </select>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label>Programa</label>
            <select id="id_programa" name="id_programa" v-model="id_programa" class="form-control">
              <option>--TODOS--</option>
              <option v-for="(item, index) in programas" :key="index" :value="item.id">{{item.nome}}</option>
            </select>
          </div>
        </div>

        <div class="col-md-9" v-if="false">
          <div class="form-group">
            <label>Filtrar</label>
            <input
              type="text"
              class="form-control"
              v-model="filtro_titulo"
              placeholder="Digite para pesquisar"
            />
          </div>
        </div>
        <div class="col-md-2" style="padding-top: 20px">
          <button class="btn btn-primary btn-lg" v-on:click="reload_table_search">
            <i class="fa fa-search" v-if="!loading"></i>
            <i class="fa fa-spinner" v-if="loading"></i>
            Filtrar
          </button>
          <button
            class="btn btn-default btn-lg"
            v-if="false"
            v-on:click="open_form"
            v-html="button_new_text"
          ></button>
        </div>
      </div>

      <div class="col-xs-12">
        <table
          id="table_data"
          class="table table-bordered table-striped display"
          style="width: 100%"
        >
          <thead>
            <tr>
              <th>ID</th>
              <th data-priority="0">Programa</th>
              <th>Data</th>
              <th data-priority="1">Hora Início</th>
              <th>Tempo</th>
              <th>ID Matéria Gerada</th>
              <th>Título Matéria</th>
              <th>Cliente(s)</th>
              <th></th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
    <div v-if="action =='form'" class="col-xs-12">
      <evento_projeto
        v-bind:id_load="id_evento"
        v-bind:id_load_arquivo="id"
        v-bind:onSave="onSave"
        show_back_button="true"
        v-bind:onBack="onBack"
        v-bind:onSelectFilho="onSelectFilho"
      ></evento_projeto>
    </div>
  </div>
</template>

<script>
import Util from "../../library/Util";
import evento_projeto from "../eventos/EventosProjeto.vue";
export default {
  components: { evento_projeto },
  data: function() {
    return {
      action: "list",
      id: "",
      id_evento: "",
      table: null,
      filtro_titulo: "",
      filtro_status: "",

      filtro_dtinicio: "",
      filtro_dtfim: "",

      show_new_button: true,

      item_filho: null,
      id_cliente: null,
      id_programa: null,
      clientes: [],
      programas: [],
      loading: false,

      button_new_text: "" //<i class=\"fa fa-file\" ></i> NOVA POST"
    };
  },
  methods: {
    onBack(objPost) {
      //Clicou no back button.
      this.id = ""; //Voltando para a lista
      this.action = "list";
    },

    onSelectFilho(item, index) {
      this.item_filho = item;
      this.action = "projeto";
    },

    open_form() {
      this.id = "";
      this.action = "form";
    },

    editar(datarow) {
      // this.id = datarow.id;
      this.id_evento = datarow.id_evento;
      this.id = datarow.id;
      this.action = "form";

      console.log("Vue recebeu o javascript:" + datarow.id);
      //  console.log( datarow );
    },
    onSave() {
      this.refresh_table();
    },

    refresh_table() {
      if (this.table != null) {
        this.table.ajax.reload(null, false); // user paging is not reset on reload
      }
    },

    reload_table_search() {
      var self = this;
      self.loading = true;

      this.filtro_dtinicio = $("#filtro_dtinicio").val();
      this.filtro_dtfim = $("#filtro_dtfim").val();

      if (this.table != null) {
        /* var url =
          window.URL_API + "eventos?ret=api&filtro=" + this.filtro_titulo;

        this.table.ajax.url(url);

        this.table.ajax.reload(); */

        var filtro = this.getObjFiltro();

        obj_api.call("eventos_arquivos2", "GET", filtro, function(retorno) {
          var dataSet = retorno.data;
          self.loading = false;

          self.table.clear().draw();
          self.table.rows.add(dataSet); // Add new data
          self.table.columns.adjust().draw(); // Redraw the DataTable
        });
      }
    },

    style_list() {
      if (this.action == "form") {
        return "display:none";
      }
      return "";
    },
    getObjFiltro() {
      var data = {
        dt_inicio: Util.BrDateToUS($("#filtro_dtinicio").val()),
        dt_fim: Util.BrDateToUS($("#filtro_dtfim").val()),
        id_cliente: this.id_cliente,
        id_programa: this.id_programa
      };

      return data;
    },
    load_data() {
      var self = this;
      var filtro = this.getObjFiltro();
      self.loading = true;

      // console.log("Filtro?");
      // console.log(filtro);
      // console.log(this.filtro_dtinicio);

      obj_api.call("eventos_arquivos2", "GET", filtro, function(retorno) {
        var dataSet = retorno.data;
        self.loading = false;

        self.filtro_dtinicio = Util.dateToBR(retorno.dt_inicio);
        self.filtro_dtfim = Util.dateToBR(retorno.dt_fim);

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
            { data: "id" },
            { data: "programa_nome" },
            { data: "data" },
            { data: "hora_inicio" },
            { data: "tempo_h_realizado" },
            { data: "id_materia_radiotv_jornal" },
            { data: "titulo_materia" },
            { data: "blnk" }
          ],
          order: [[0, "desc"]],

          columnDefs: [
            {
              // The `data` parameter refers to the data for the cell (defined by the
              // `data` option, which defaults to the column being worked with, in
              // this case `data: 0`.
              render: function(data, type, row) {
                var ar = data.split(" ");
                var pedaco_data = ar[0].split("-");

                var data_saida =
                  pedaco_data[2] + "/" + pedaco_data[1] + "/" + pedaco_data[0];
                return (
                  "<span style='display:none'>" + data + "</span>" + data_saida
                );
              },
              targets: 2
            },
            {
              // The `data` parameter refers to the data for the cell (defined by the
              // `data` option, which defaults to the column being worked with, in
              // this case `data: 0`.
              render: function(data, type, row) {
                var meta_dados_tmp = row.meta_dados;

                if (meta_dados_tmp.indexOf("{") > -1) {
                  try {
                    var obj_tmp = JSON.parse(meta_dados_tmp);
                    var clientes = obj_tmp.clientes;
                    var str = "";
                    for (var uu = 0; uu < clientes.length; uu++) {
                      if (str != "") {
                        str += ", ";
                      }

                      str += clientes[uu].nome;
                    }

                    return str;
                  } catch (Exp) {}
                }
                return " ";
              },
              targets: 7
            },
            {
              // The `data` parameter refers to the data for the cell (defined by the
              // `data` option, which defaults to the column being worked with, in
              // this case `data: 0`.
              render: function(data, type, row) {
                return '<a href="#!"><i class="fa fa-cut"></i> Visualizar</a>';
              },
              targets: 8
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
    }
  },
  computed: {},
  mounted() {
    let self = this;

    self.button_new_text = '<i class="fa fa-user" ></i> CADASTRAR';

    obj_api.call("clientes", "GET", {}, function(retorno) {
      self.clientes = retorno.data;
    });

    obj_api.call("programas", "GET", {}, function(retorno) {
      self.programas = retorno.data;
    });

    $(document).ready(function() {
      obj_editor.loadCalendar("#filtro_dtinicio");
      obj_editor.loadCalendar("#filtro_dtfim");
      console.log("URL: " + window.URL_API + "eventos_arquivos2");
      console.log("Type: " + self.type);

      self.load_data();
    });
  }
};
</script>
