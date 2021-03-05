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

        <div class="col-md-1" v-if="show_id">
          <div class="form-group">
            <label>ID</label>
            <input
              type="text"
              class="form-control"
              id="filtro_id"
              v-model="filtro_id"
              placeholder="ID"
            />
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <label>Cliente</label>
            <select id="id_cliente" name="id_cliente" v-model="id_cliente" class="form-control">
              <option value=" ">--TODOS--</option>
              <option v-for="(item, index) in clientes" :key="index" :value="item.id">{{item.nome}}</option>
            </select>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label>Emissora</label>
            <select
              id="id_emissora"
              name="id_emissora"
              v-model="id_emissora"
              v-on:change="change_emissora"
              class="form-control"
            >
              <option value=" ">--TODAS--</option>
              <option v-for="(item, index) in emissoras" :key="index" :value="item.id">{{item.nome}}</option>
            </select>
          </div>
        </div>
        <div class="col-md-5">
          <div class="form-group">
            <label>Programa</label>
            <select id="id_programa" name="id_programa" v-model="id_programa" class="form-control">
              <option value=" ">--TODOS--</option>
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
          <button class="btn btn-primary btn-lg pull-right" v-on:click="reload_table_search">
            <i class="fa fa-search" v-if="!loading"></i>
            <i class="fa fa-spinner" v-if="loading"></i>
            Filtrar
          </button>
          <button
            class="btn btn-default btn-lg"
            v-if="show_new_button"
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
              <th>Data</th>
              <th>Programa</th>
              <th>Emissora</th>
              <th>Hora Início</th>
              <th>Hora Fim</th>
              <th>Duração</th>
              <th>Tempo Realizado</th>
              <th></th>
            </tr>
          </thead>
        </table>

        <div v-html="sql"></div>
      </div>
    </div>
    <div v-if="action =='form'" class="col-xs-12">
      <eventos_transcricao
        v-bind:id_load="id"
        v-bind:onSave="onSave"
        show_back_button="true"
        v-bind:onBack="onBack"
        v-bind:onSelectFilho="onSelectFilho"
      ></eventos_transcricao>
    </div>

    <div
      v-if="action =='projeto' && item_filho != null && item_filho.id != null "
      class="col-xs-12"
    >
      <evento_projeto
        v-bind:id_load="item_filho.id"
        show_back_button="true"
        v-bind:onBack="onBackProjeto"
      ></evento_projeto>
    </div>
  </div>
</template>

<script>
import evento_projeto from "./EventosProjeto.vue";
import Util from "../../library/Util";

export default {
  components: { evento_projeto },

  data: function() {
    return {
      action: "list",
      id: "",
      table: null,
      sql: "",
      filtro_titulo: "",
      filtro_status: "",

      filtro_dtinicio: "",
      filtro_dtfim: "",
      show_id: false,

      filtro_id: "",

      show_new_button: false,

      item_filho: null,
      id_cliente: null,
      id_programa: null,
      id_emissora: null,
      clientes: [],
      programas: [],
      emissoras: [],

      loading: false,

      button_new_text: "" //<i class=\"fa fa-file\" ></i> NOVA POST"
    };
  },
  methods: {
    change_emissora() {
      var self = this;
      var data_programa = {};

      if (
        this.id_emissora != null &&
        this.id_emissora != "" &&
        this.id_emissora != " "
      ) {
        data_programa["id_emissora"] = this.id_emissora;
      }

      obj_api.call("programas", "GET", data_programa, function(retorno) {
        console.log("programas");
        console.log(data_programa);
        self.programas = retorno.data;
      });
    },
    onBack(objPost) {
      //Clicou no back button.
      this.id = ""; //Voltando para a lista
      this.action = "list";
    },
    onBackProjeto(form) {
      //this.action = "form";

      this.id = ""; //Voltando para a lista
      this.action = "list";
    },

    open_form() {
      this.id = "";
      this.action = "form";
    },

    editar(datarow) {
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

        obj_api.call("eventos", "GET", filtro, function(retorno) {
          var dataSet = retorno.data;
          self.loading = false;

          self.table.clear().draw();
          self.table.rows.add(dataSet); // Add new data
          self.table.columns.adjust().draw(); // Redraw the DataTable
        });
      }
    },

    style_list() {
      if (this.action == "form" || this.action == "projeto") {
        return "display:none";
      }
      return "";
    },

    onSelectFilho(item, index) {
      console.log("select filho?");
      console.log(item);
      this.item_filho = item;
      this.action = "projeto";
    },

    getObjFiltro() {
      var data = {
        dt_inicio: Util.BrDateToUS($("#filtro_dtinicio").val()),
        dt_fim: Util.BrDateToUS($("#filtro_dtfim").val()),
        id_cliente: this.id_cliente,
        id_programa: this.id_programa,
        id_emissora: this.id_emissora,
        id: this.filtro_id
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

      obj_api.call("eventos", "GET", filtro, function(retorno) {
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
            { data: "data" },
            { data: "programa" },
            { data: "emissora" },
            { data: "hora_inicio" },
            { data: "hora_fim" },
            { data: "duracao" },
            { data: "tempo_h" },
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
              targets: 1
            },
            {
              // The `data` parameter refers to the data for the cell (defined by the
              // `data` option, which defaults to the column being worked with, in
              // this case `data: 0`.
              render: function(data, type, row) {
                return '<a href="#!"><i class="fa fa-cut"></i> Recortes</a>';
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

    if (
      self.$route.query != null &&
      self.$route.query.show_id != null &&
      self.$route.query.show_id == "1"
    ) {
      self.show_id = true;
    }

    obj_api.call("emissoras", "GET", {}, function(retorno) {
      self.emissoras = retorno.data;
    });

    this.change_emissora();

    $(document).ready(function() {
      obj_editor.loadCalendar("#filtro_dtinicio");
      obj_editor.loadCalendar("#filtro_dtfim");

      self.load_data();
    });
  }
};
</script>
