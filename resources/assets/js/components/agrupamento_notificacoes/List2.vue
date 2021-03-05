<template>
  <div>
    <div v-bind:style="style_list()">
      <div style="padding-top: 10px">
        <div class="col-xs-11">
          <filtro_geral :onSearch="reload_table_search" v-model="data_filtro" :show_status="false"></filtro_geral>
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
          class="table table-bordered table-striped display"
          style="width: 100%"
        >
          <thead>
            <tr>
              <th>ID</th>
              <th>Data</th>
              <th>Hora Início</th>
              <th>Programa</th>
              <th>Emissora</th>
              <th>Localizados</th>
              <th>Clientes</th>
              <th></th>
              <th></th>
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
        v-bind:id_load_arquivo="id_evento_arquivo"
        v-bind:tempo_inicio="tempo_seg"
        show_back_button="true"
        v-bind:onBack="onBackProjeto"
      ></evento_projeto>
    </div>

    <div v-if="action =='form' && false" class="col-xs-12">
      <agrupamento_notificacoes_form
        v-bind:id_load="id"
        v-bind:onSave="onSave"
        show_back_button="true"
        v-bind:onBack="onBack"
      ></agrupamento_notificacoes_form>
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
import filtro_geral from "../geral/filtro_geral";
import evento_projeto from "../eventos/EventosProjeto.vue";
import evento_transcricao from "../eventos/EventosTranscricao.vue";

export default {
  components: {
    filtro_geral: filtro_geral,
    visualizar: visualizar,
    evento_projeto: evento_projeto,
    evento_transcricao: evento_transcricao
  },
  props: {
    prop_status: {
      type: String,
      default() {
        return null;
      }
    },
    tinder:{
      type: Boolean,
      default(){
        return false;
      }
    }
  },
  data: function() {
    return {
      action: "list",
      id: "",

      id_evento: null,
      id_evento_arquivo: null,
      tempo_seg: null,
      item_filho: null,

      table: null,
      filtro_titulo: "",
      filtro_status: "",

      show_new_button: true,

      show_modal: false,
      loading: false,
      mostra_add: false,

      tempo_seg: -1,
      interval: null,
      o_interval: null,

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
    onBack(objPost) {
      //Clicou no back button.
      this.id = ""; //Voltando para a lista
      this.action = "list";
    },

    onBackProjeto(form) {
      this.id = ""; //Voltando para a lista
      this.action = "list";
      //this.action = "form";
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
      this.id = datarow.id;

      this.id_evento_arquivo = datarow.id_evento_arquivo;
      this.id_evento = datarow.id_evento;
      this.tempo_seg = datarow.tempo_seg;

      this.action = "form";

      //console.log("Vue recebeu o javascript:" + datarow.id);
      //  console.log( datarow );
    },
    onSave() {
      this.refresh_table();
    },
    callEventosBloqueados() {
      var self = this;

      if (self.interval == null) {
        var myTimer = function() {
          //code goes here that will be run every 15 seconds.
          if (!self.loading) {
            self.refresh_table();
            // setTimeout(self.getEventosBloqueados, 2000);
          }
          //self.getEventosBloqueados();
          self.interval = 1;
        };

        window.o_interval3 = setInterval(myTimer, 22000);
      }
    },

    getEventosBloqueados() {
      var self = this;
      //console.log("getEventosBloqueados " + this.$route.name);

      if (self.table == null) {
        return;
      }

      if (self.action != "list") {
        return;
      }

      // this.refresh_table();
      //return;

      var ids = new Array();
      $(".hd_input_id").each(function() {
        var input = $(this); // This is the jquery object of the input, do what you will
        ids[ids.length] = input.val();
      });

      if (ids.length <= 0) {
        return;
      }

      var id_operador = $("#id_operador").val();

      obj_api.call(
        "agrupamento_status",
        "POST",
        { ids: ids.join(",") },
        function(response) {
          //console.log(response);
          var data2 = response.data;

          for (var i = 0; i < data2.length; i++) {
            $("#row_data_" + data2[i].id.toString()).removeClass(
              "background_bloqueado"
            );
            $("#row_data_" + data2[i].id.toString()).removeClass(
              "background_azul"
            );

            if (
              data2[i].status == 2 &&
              (data2[i].bloqueado_por_id == null ||
                data2[i].bloqueado_por_id.toString() != id_operador)
            ) {
              $("#row_data_" + data2[i].id.toString()).addClass(
                "background_bloqueado"
              );
            } else {
              if (data2[i].status_notificacao == 3) {
                $("#row_data_" + data2[i].id.toString()).addClass(
                  "background_azul"
                );
              }
            }

            var campodid = "div_evento_palavras_" + data2[i].id.toString();
            $("#" + campodid).html(data2[i].palavras);
            /*console.log(
              "atualizando palavra? ",
              campodid,
              document.getElementById(campodid),
              data2[i].palavras
            ); */
          }
        }
      );
    },

    refresh_table_02() {
      var page = this.table.page.info().page;
      this.reload_table_search(page);
    },

    refresh_table() {
      console.log("refresh table?");

      if (
        this.$route.name != "notificacoes3" &&
        this.$route.name != "home" &&
        window.o_interval3 != null &&
        window.o_interval3 != undefined
      ) {
        clearInterval(window.o_interval3);
        console.log("clear interval ", window.o_interval3);
        return;
      }

      if (this.table != null) {
        this.table.ajax.reload(null, false); // user paging is not reset on reload
      }
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
          window.URL_API + "agrupamento_notificacoes_filtro2" + query_string;

        this.table.ajax.url(url);

        //console.log(url);
        //console.log(this.table );
        this.table.ajax.reload();
      }
    },

    reload_table_search__02(page) {
      var self = this;
      self.loading = true;
      if (this.table != null) {
        var filtro = this.getObjFiltro();

        obj_api.call(
          "agrupamento_notificacoes_filtro",
          "POST",
          filtro,
          function(retorno) {
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
          }
        );
      }
    },

    style_list() {
      if (this.action == "form" || this.action == "projeto") {
        return "display:none";
      }
      return "";
    }
  },
  computed: {},
  mounted() {
    let self = this;

    self.button_new_text = '<i class="fa fa-user" ></i> CADASTRAR';
    self.loading = true;
    var filtro = {}; //this.data_filtro;

    if (this.prop_status != null) {
      filtro["status"] = this.prop_status;
    }

    if ( this.tinder ){
      filtro["status"] = "4";
      filtro["status_evento"] = "1,2";
    }

    // obj_api.call("agrupamento_notificacoes_filtro", "POST", filtro, function(retorno) {
    // var dataSet = retorno.data;

    // self.data_filtro.filtro_dtinicio = Util.dateToBR(retorno.dt_inicio);
    // self.data_filtro.filtro_dtfim = Util.dateToBR(retorno.dt_fim);

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
      serverSide: true,
      rowId: "id_row",

      ajax: {
        url: window.URL_API + "agrupamento_notificacoes_filtro2" + query_string,
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

      // data: dataSet,
      columns: [
        { data: "id" },
        { data: "data" },
        { data: "hora_inicio" },
        { data: "programa" },
        { data: "emissora" },
        { data: "palavras" },
        { data: "clientes" },

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
              data +
              "<input type='hidden' value='" +
              data +
              "' class='hd_input_id' />"
            );
          },
          targets: 0
        },
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
          render: function(data, type, row) {
            return (
              '<div id="div_evento_group_' +
              row.id +
              '">' +
              row.programa +
              "</div>"
            );
          },
          targets: 3
        },
        {
          render: function(data, type, row) {
            return (
              '<div id="div_evento_palavras_' +
              row.id +
              '">' +
              row.palavras +
              "</div>"
            );
          },
          targets: 5
        },
        {
          // The `data` parameter refers to the data for the cell (defined by the
          // `data` option, which defaults to the column being worked with, in
          // this case `data: 0`.
          render: function(data, type, row) {
            return '<a href="#!" tip="trecho" ><i class="fa fa-paragraph"></i> Trecho</a>';
          },
          targets: 7
        },

        {
          // The `data` parameter refers to the data for the cell (defined by the
          // `data` option, which defaults to the column being worked with, in
          // this case `data: 0`.
          render: function(data, type, row) {
            return '<a href="#!"  tip="recorte"><i class="fa fa-cut"></i> Recorte </a>';
          },
          targets: 8
        }
      ]
    });

    self.callEventosBloqueados();

    // setTimeout(function() {
    //self.getEventosBloqueados();
    // }, 1000);

    table.on("draw.dt", function() {
      //console.log("draw.dt");
      self.getEventosBloqueados();
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
    });
    //});
    self.button_new_text = '<i class="fa fa-user" ></i> CADASTRAR';
  }
};
</script>
