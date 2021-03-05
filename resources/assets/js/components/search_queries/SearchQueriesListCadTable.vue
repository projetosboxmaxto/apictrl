<template>
  <div>
    <!--

import SearchQueriesListCadTable from './components/search_queries/SearchQueriesListCadTable'
import SearchQueriesListConsTable from './components/search_queries/SearchQueriesListConsTable'


Vue.component('search_queries_list_cad_table', SearchQueriesListCadTable);
Vue.component('search_queries_list_cons_table', SearchQueriesListConsTable );


    -->
    <div v-if="loading">...Carregando</div>
    <div v-if="false && cliente != null">
      <b>Cliente:</b>
      {{cliente.nome}}
    </div>
    <table class="table table-bordered table-striped" v-if="items != null && !loading">
      <thead>
        <tr>
          <th>Praça</th>
          <th>Termo obrigatório além da palavra-chave (obs: separe por vírgula caso tenha mais de um)</th>
          <th>Ativo?</th>

          <th v-if="false">
            <button class="btn btn-default" v-if="false" v-on:click="add" type="button">
              <i class="fa fa-plus"></i>
            </button>
          </th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(item, index) in items" :key="index">
          <td>{{item.titulo}}</td>
          <td>
            <textarea
              class="form-control"
              v-model="item.querie"
              v-bind:id="getIDItem('querie', index)"
              style="height: 30px"
            ></textarea>
          </td>
          <td>
            <select v-model="item.ativo" v-bind:id="getIDItem('ativo', index)">
              <option :value="0">Não</option>
              <option :value="1">Sim</option>
            </select>
          </td>

          <td v-if="false">
            <button class="btn btn-default" type="button" v-on:click="excluir(item, index)">
              <i class="fa fa-trash"></i>
            </button>
          </td>
        </tr>
      </tbody>
      <tfoot>
        <tr v-if="items.length <= 0">
          <td colspan="4">
            <i>Sem registros</i>
          </td>
        </tr>
        <tr v-if="cliente != null">
          <td colspan="2">
            <b>Qtde Palavras:</b>
            {{qtde_palavras}}
            <a
              href="#"
              onclick="alert('função em desenvolvimento');"
            >Editar</a>
          </td>
          <td>
            <button class="btn btn-primary pull-right" type="button" v-on:click="salvar">Salvar</button>
          </td>
        </tr>
      </tfoot>
    </table>

   <elastic_queries_list_cad_table v-if="false && cliente != null"  :id_load="cliente.id" ></elastic_queries_list_cad_table>


  </div>
</template>

<script>
import ElasticQueriesListCadTable from '../elastic_queries/ElasticQueriesListCadTable';
export default {
  props: ["id_load"],
    components:{
        elastic_queries_list_cad_table:  ElasticQueriesListCadTable
      },
  data: function() {
    return {
      items: [],
      cliente: null,

      ids_excluir: [],

      action: "list",
      id: "",
      table: null,
      filtro_titulo: "",
      filtro_status: "",
      qtde_palavras: 0,

      show_new_button: true,
      loading: false,
      button_new_text: "" //<i class=\"fa fa-file\" ></i> NOVA POST"
    };
  },
  methods: {
    getIDItem(nome, index) {
      return "search_queries_" + nome + "_L" + index.toString();
    },
    change_data_item(id_campo, value) {
      if (id_campo.indexOf("data") > -1) {
        var ar = id_campo.split("_L");
        var index = ar[1];
        var item = this.items[parseInt(index)];

        var arcampo = id_campo.split("search_queries_");
        var arcampo2 = arcampo[1].split("_L");
        var nome_campo = arcampo2[0];

        console.log(
          "Localizado nome do campo " + nome_campo + " e data" + value
        );

        item[nome_campo] = value;
        Vue.set(this.items, parseInt(index), item);
        console.log("Consegui alterar para o indice : " + index);
      }
    },
    change_value(event) {
      this.change_data_item(event.target.id, event.target.value);
    },
    excluir(item, index) {
      console.log("Excluir? ");

      if (item.id != null && item.id != "") {
        this.ids_excluir.unshift(item.id);
      }

      this.items.splice(index, 1);
    },
    datepicker_return(dateText, inst) {
      console.log("Cheguei aqui? ");
    },
    salvar() {
      var self = this;
      var hd_json = JSON.stringify(this.items);
      var ids_delete_json = JSON.stringify(this.ids_excluir);

      var url = "search_querie_grid";
      var method = "POST";

      var data = {
        hd_json: hd_json,
        ids_delete_json: ids_delete_json,
        id_cliente: this.id_load

        // equipamento_id: this.equipamento_id,
        // projeto_id: this.projeto_id
      };

      var fn_return = function(retorno) {
        self.items = retorno.data;

        if (retorno.success) {
          obj_alert.show(
            "Sucesso!",
            "Configuração de querie salva com sucesso!",
            "success",
            null,
            3000
          );
        }
      };

      obj_api.call(url, method, data, fn_return);
    },
    add() {
      this.items.push(this.getArrayItem());
    },
    getArrayItem() {
      var data = {
        id: "",
        id: "",
        id_cliente: "",
        titulo: "",
        querie: "",
        ativo: "",
        data: "",
        id_praca: ""
      };

      // if ( this.equipamento_id != null ){
      //        data["equipamento_id"] = this.equipamento_id;

      // }
      // if ( this.projeto_id != null ){
      //        data["projeto_id"] = this.projeto_id;

      // }

      return data;
    }
  },
  mounted() {
    let self = this;

    var url = "search_queries_edit/" + this.id_load; // +
    // this.id_load; console.log("monted post url: " + url );
    var method = "GET";
    this.disableButton = true;
    this.loading = true;

    var data = {};

    var fn_return = function(retorno) {
      self.items = retorno.data;
      self.cliente = retorno.cliente;
      self.qtde_palavras = retorno.qtde_palavras;
      self.disableButton = false;
      self.loading = false;
    };

    obj_api.call(url, method, data, fn_return);
  }
};
</script>