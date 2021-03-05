<template>
  <div>
    <div v-bind:style="style_list()">
      <div style="padding-top: 10px">
        <div class="col-xs-11">
          <filtro_geral :onSearch="reload_table_search" v-model="data_filtro" :show_status="false"></filtro_geral>
        </div>

        <div v-if="false">
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
                <option
                  v-for="(item, index) in clientes"
                  :key="index"
                  :value="item.id"
                >{{item.nome}}</option>
              </select>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label>Programa</label>
              <select
                id="id_programa"
                name="id_programa"
                v-model="id_programa"
                class="form-control"
              >
                <option>--TODOS--</option>
                <option
                  v-for="(item, index) in programas"
                  :key="index"
                  :value="item.id"
                >{{item.nome}}</option>
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
        </div>
        <div class="col-xs-1" style="padding-top: 20px; padding-left: 0px">
          <button class="btn btn-primary btn-lg" v-on:click="reload_table_search">
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
        <div class="col-xs-12">
          <div class="col-xs-4">
            <label>Palavras Chaves</label>
            <input
              type="text"
              class="form-control"
              name="filtro_palavra"
              id="filtro_palavra"
              v-model="data_filtro.palavra"
              placeholder="Palavra Chave"
            />
          </div>
        </div>
      </div>

      <div class="col-xs-12">
        <table
          id="table_data"
          class="table table-bordered table-striped display responsive nowrap"
          style="width: 100%"
        >
          <thead>
            <tr>
              <th data-priority="9">ID</th>
              <th data-priority="1">Cliente</th>
              <th data-priority="0">Palavras-chave</th>
              <th data-priority="8">Citação Direta</th>
              <th data-priority="5">Data</th>
              <th data-priority="6">Mídia</th>
              <th data-priority="2">Programa</th>
              <th data-priority="5">Hora Início</th>
              <th data-priority="7">Início Trecho</th>

              <th data-priority="3"></th>
              <th data-priority="11"></th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
    <div v-if="action =='form'" class="col-xs-12">
      <evento_transcricao
        v-bind:id_load="id_evento"
        v-bind:id_load_arquivo="id_evento_arquivo"
        v-bind:onSave="onSave"
        show_back_button="true"
        v-bind:onBack="onBack"
        v-bind:tempo_inicio="tempo_seg"
        v-bind:onSelectFilho="onSelectFilho"
      ></evento_transcricao>
    </div>
    <div v-if="action =='projeto' && item_filho != null " class="col-xs-12">
      <evento_projeto
        v-bind:id_load="item_filho.id"
        show_back_button="true"
        v-bind:onBack="onBackProjeto"
      ></evento_projeto>
    </div>

    <div class="modal" id="myModal" tabindex="-1" role="dialog" v-if="show_modal">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Trecho</h5>
          </div>
          <div class="modal-body">
            <visualizar :id_load="id"></visualizar>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-secondary"
              v-on:click="closeModal"
              data-dismiss="modal"
            >Fechar</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Util from "../../library/Util";
import visualizar from "./Visualizar";
import evento_projeto from "../eventos/EventosProjeto.vue";
import evento_transcricao from "../eventos/EventosTranscricao.vue";
import filtro_geral from "../geral/filtro_geral";

export default {
  components: {
    visualizar,
    evento_projeto,
    evento_transcricao,
    filtro_geral
  },
  data: function() {
    return {
      action: "list",
      id: "",
      id_evento: null,
      id_evento_arquivo: null,
      table: null,
      sql: "",
      filtro_titulo: "",
      filtro_status: "",

      filtro_dtinicio: "",
      filtro_dtfim: "",

      show_new_button: false,

      item_filho: null,
      id_cliente: null,
      id_programa: null,
      clientes: [],
      programas: [],

      loading: false,
      show_modal: false,

      tempo_seg: -1,

      data_filtro: {
        id_cliente: null,

        nome_cliente: "",
        id_programa: null,
        id_emissora: null,
        palavra: "",

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
        data["cliente_nome"] = this.data_filtro.cliente_nome;
        data["status"] = this.data_filtro.status;
        data["palavra"] = this.data_filtro.palavra;
        data["id_emissora"] = this.data_filtro.id_emissora;

        this.data_filtro.filtro_dtinicio = $("#filtro_dtinicio").val();
        this.data_filtro.filtro_dtfim = $("#filtro_dtfim").val();
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
    onSelectFilho(item, index) {
      this.item_filho = item;
      this.action = "projeto";
    },

    onBackProjeto(form) {
      this.action = "form";
    },

    open_form() {
      this.id = "";
      this.action = "form";
    },

    editar(datarow) {
      this.id = datarow.id;
      this.id_evento_arquivo = datarow.id_evento_arquivo;
      this.id_evento = datarow.id_evento;
      this.tempo_seg = datarow.tempo_seg;
      this.action = "form";

      // console.log("Vue recebeu o javascript:" + datarow.id);
      //  console.log( datarow );
    },
    visualizar(datarow) {
      this.id = datarow.id;
      this.show_modal = true;

      setTimeout(function() {
        $("#myModal").modal({ show: true });
      }, 200);

      // data-toggle="modal" data-target="#myModal"
    },
    closeModal() {
      this.show_modal = false;
    },
    onSave() {
      this.refresh_table();
    },

    refresh_table() {
      if (this.table != null) {
        this.table.ajax.reload(null, false); // user paging is not reset on reload
      }
    },

    reload_table_search2() {
      if (this.table != null) {
        var url =
          window.URL_API +
          "eventos_arquivos_palavras?filtro=" +
          this.filtro_titulo;

        this.table.ajax.url(url);

        console.log(url);
        console.log(this.table);
        this.table.ajax.reload();
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

        obj_api.call("eventos_arquivos_palavras", "GET", filtro, function(
          retorno
        ) {
          var dataSet = retorno.data;
          self.loading = false;

          self.table.clear().draw();
          self.table.rows.add(dataSet); // Add new data
          self.table.columns.adjust().draw(); // Redraw the DataTable
          //self.getIdsEventosPagina();
        });
      }
    },

    getIdsEventosPagina() {
      console.log("Get ids eventos página");
      for (var i = 0; i < self.table.rows.length; i++) {
        console.log(self.table.rows[i]);
      }
    },

    getObjFiltro2() {
      var data = {
        dt_inicio: Util.BrDateToUS($("#filtro_dtinicio").val()),
        dt_fim: Util.BrDateToUS($("#filtro_dtfim").val()),
        id_cliente: this.id_cliente,
        id_programa: this.id_programa
      };

      return data;
    },

    style_list() {
      if (this.action == "form" || this.action == "projeto") {
        return "display:none";
      }
      return "";
    },
    load_data() {
      var self = this;
      var filtro = this.getObjFiltro();
      self.loading = true;

      // console.log("Filtro?");
      // console.log(filtro);
      // console.log(this.filtro_dtinicio);

      obj_api.call("eventos_arquivos_palavras", "GET", filtro, function(
        retorno
      ) {
        var dataSet = retorno.data;
        self.loading = false;

        console.log(dataSet);

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
          /* 
              
              
         
              
              */
          columns: [
            { data: "id" },

            { data: "nome_cliente" },
            { data: "palavra" },
            { data: "cita_diretamente" },
            { data: "data" },
            { data: "nome_midia" },
            { data: "nome_programa" },
            { data: "hora_inicio" },
            { data: "tempo" },
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
              targets: 4
            },
            {
              // The `data` parameter refers to the data for the cell (defined by the
              // `data` option, which defaults to the column being worked with, in
              // this case `data: 0`.
              render: function(data, type, row) {
                if (data != null && data.toString() == "1") {
                  return "Sim";
                }
                return "Não";
              },
              targets: 3
            },
            {
              // The `data` parameter refers to the data for the cell (defined by the
              // `data` option, which defaults to the column being worked with, in
              // this case `data: 0`.
              render: function(data, type, row) {
                return '<a href="#!" tip="trecho" ><i class="fa fa-paragraph"></i> Trecho</a>';
              },
              targets: 9
            },
            {
              // The `data` parameter refers to the data for the cell (defined by the
              // `data` option, which defaults to the column being worked with, in
              // this case `data: 0`.
              render: function(data, type, row) {
                return '<a href="#!"  tip="recorte"><i class="fa fa-cut"></i> Recorte </a>';
              },
              targets: 10
            }
          ]
        });

        self.table = table;

        $("#table_data tbody").on("click", "a", function() {
          var tip = $(this).attr("tip");
          var data = table.row($(this).parents("tr")).data();

          if (tip == "trecho") {
            self.visualizar(data);
          }

          if (tip == "recorte") {
            self.editar(data);
          }
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

      self.load_data();
    });
  }
};
</script>
