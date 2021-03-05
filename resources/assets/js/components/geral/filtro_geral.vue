<template>
  <div v-if="data_filtro != null ">
    <div class="col-md-2">
      <div class="form-group">
        <label>Data Início</label>
        <input
          type="text"
          class="form-control"
          id="filtro_dtinicio"
          v-model="data_filtro.filtro_dtinicio"
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
          v-model="data_filtro.filtro_dtfim"
          placeholder="Data Fim"
        />
      </div>
    </div>
    <div class="col-md-2">
      <div class="form-group">
        <label>Cliente</label>
        <input
          type="text"
          class="form-control"
          id="filtro_nome_cliente"
          v-model="data_filtro.cliente_nome"
          placeholder="Nome do cliente"
        />
      </div>
    </div>

    <div class="col-md-2">
      <div class="form-group">
        <label>Emissora</label>
        <select
          id="id_emissora"
          name="id_emissora"
          v-model="data_filtro.id_emissora"
          v-on:change="change_emissora"
          class="form-control"
        >
          <option value=" ">--TODAS--</option>
          <option v-for="(item, index) in emissoras" :key="index" :value="item.id">{{item.nome}}</option>
        </select>
      </div>
    </div>

    <div class="col-md-3">
      <div class="form-group">
        <label>Programa</label>
        <select
          id="id_programa"
          name="id_programa"
          v-model="data_filtro.id_programa"
          class="form-control"
        >
          <option :value="-1">--TODOS--</option>
          <option v-for="(item, index) in programas" :key="index" :value="item.id">{{item.nome}}</option>
        </select>
      </div>
    </div>
    <div class="col-md-1" v-if="show_status">
      <div class="form-group">
        <label>Status</label>
        <select
          id="filtro_status"
          name="filtro_status"
          v-model="data_filtro.status"
          class="form-control"
        >
          <option value="-1">--</option>
          <option value="1">Mat. Criada</option>
          <option value="0 ">Rascunho</option>
        </select>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  props: {
    mostra_add: {
      type: Boolean,
      default() {
        return false;
      }
    },
    show_status: {
      type: Boolean,
      default() {
        return false;
      }
    },

    onSearch: {
      type: Function
    },
    onNovo: {
      type: Function,
      default() {
        return null;
      }
    },

    data_filtro: {
      default() {
        return null;
      },
      type: Object
    }
  },
  model: {
    prop: "data_filtro",
    event: "input"
  },
  watch: {
    data_filtro: {
      deep: true,
      handler: function(val) {
        this.$emit("input", this.data_filtro);
      }
    }
  },
  data: function() {
    return {
      show_new_button: true,

      item_filho: null,
      clientes: [],
      programas: [],
      emissoras: [],
      loading: false,

      button_new_text: "" //<i class=\"fa fa-file\" ></i> NOVA POST"
    };
  },
  methods: {
    open_form() {
      if (this.onNovo != null) {
        this.onNovo();
      }
    },
    change_emissora() {
      var self = this;
      var data_programa = {};

      if (
        this.data_filtro.id_emissora != null &&
        this.data_filtro.id_emissora != "" &&
        this.data_filtro.id_emissora != " "
      ) {
        data_programa["id_emissora"] = this.data_filtro.id_emissora;
      }

      obj_api.call("programas", "GET", data_programa, function(retorno) {
        console.log("programas");
        console.log(data_programa);
        self.programas = retorno.data;
      });
    }
  },
  mounted() {
    let self = this;

    if (this.data_filtro == null) {
      this.data_filtro = {
        id_cliente: null,

        nome_cliente: "",
        id_programa: null,
        id_emissora: null,

        filtro_dtinicio: "",
        filtro_dtfim: ""
      };
    }

    self.button_new_text = '<i class="fa fa-user" ></i> CADASTRAR';

    //obj_api.call("clientes", "GET", {}, function(retorno) {
    //  self.clientes = retorno.data;
    //});

    obj_api.call("emissoras", "GET", {}, function(retorno) {
      self.emissoras = retorno.data;
    });

    this.change_emissora();

    //obj_api.call("programas", "GET", {}, function(retorno) {
    //  self.programas = retorno.data;
    //});

    $(document).ready(function() {
      obj_editor.loadCalendar("#filtro_dtinicio");
      obj_editor.loadCalendar("#filtro_dtfim");
      console.log("URL: " + window.URL_API + "eventos_arquivos2");
      console.log("Type: " + self.type);
    });
  }
};
</script>