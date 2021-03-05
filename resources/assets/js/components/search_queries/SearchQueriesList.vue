<template>
  <div>
    <div v-bind:style="style_list()">
      <div style="padding-top: 10px">
        <div class="col-md-9">
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
        <div class="col-md-3" style="padding-top: 20px">
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
      </div>

      <div class="col-xs-12">
        <table
          id="table_data"
          class="table table-bordered table-striped display responsive"
          style="width: 100%"
        >
          <thead>
            <tr>
              <th>Id Cliente</th>
              <th>Cliente</th>
              <!-- <th>Título</th>
              <th>Deve Conter</th>
              <th>Ativo?</th>
              <th>Data</th>
              <th>id_praca</th>-->
              <th></th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
    <div v-if="action =='form'" class="col-xs-12">
      <search_queries_form
        v-bind:id_load="id"
        v-bind:onSave="onSave"
        show_back_button="true"
        v-bind:onBack="onBack"
      ></search_queries_form>
    </div>
  </div>
</template>

<script>
export default {
  data: function() {
    return {
      action: "list",
      id: "",
      table: null,
      filtro_titulo: "",
      filtro_status: "",

      show_new_button: false,
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
      var page = this.table.page.info().page;
      this.reload_table_search(page);
    },

    getObjFiltro() {
      var data = {
        filtro: this.filtro_titulo
      };

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

        obj_api.call("search_queries", "GET", filtro, function(retorno) {
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
    }
  },
  computed: {},
  mounted() {
    let self = this;

    self.button_new_text = '<i class="fa fa-user" ></i> CADASTRAR';

    var filtro = { filtro: this.filtro_titulo };

    obj_api.call("search_queries", "GET", filtro, function(retorno) {
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
          { data: "id" },
          { data: "nome" },
          /* { data: "titulo" },
          { data: "querie" },
          { data: "ativo" },
          { data: "data" },
          { data: "id_praca" }, */
          { data: "blnk" }
        ],
        order: [[1, "asc"]],

        columnDefs: [
          {
            // The `data` parameter refers to the data for the cell (defined by the
            // `data` option, which defaults to the column being worked with, in
            // this case `data: 0`.
            render: function(data, type, row) {
              return '<a href="#!" class="pull-right"><i class="fa fa-cogs"></i> Visualizar</a>';
            },
            targets: 2
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
};
</script>
