<template>
  <div>
    <table class="table table-striped table-bordered">
      <thead>
        <tr>
          <th colspan="4">Clientes</th>
        </tr>

        <tr>
          <th>Nome</th>
          <th>Palavras-Chave</th>
          <th>Citação Direta</th>
        </tr>
      </thead>

      <tbody v-if="items != null ">
        <tr v-for="(item,index) in items" :key="index">
          <td>
            <a
              href="#"
              v-if="item.citacao_direta != null"
              v-on:click="buscar(item.nome)"
            >{{item.nome}}</a>
            <span v-if="item.citacao_direta == null">{{item.nome}}</span>
          </td>
          <td>
            <div v-if="item.palavras != null">
              <span v-for="(item2, index2) in item.palavras" :key="index2">
                <a href="#" v-on:click="buscar(item2.nome)">{{item2.nome}}</a>&nbsp;&nbsp;
              </span>
            </div>
          </td>
          <td>{{item.citacao_direta != null ? "Sim" : "Não"}}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
<script>
export default {
  props: {
    items: {
      default: [],
      type: Array
    },
    onSelect: {
      default: null,
      type: Function
    }
  },
  data: function() {
    return {};
  },
  mounted() {},
  methods: {
    show_palavras(palavras) {
      if (palavras == null || palavras == undefined) return "";
    },
    buscar(palavra) {
      //document.getElementById("divTexto").find(palavra);
      // $("#divTexto").find()
      //window.find(palavra, true);
      //console.log("vou tentar buscar? " + palavra);

      var links = document.getElementById("divTexto").getElementsByTagName("a");

      for (var i = 0; i < links.length; i++) {
        var link = links[i];

        if (link.innerText.toLowerCase().indexOf(palavra.toLowerCase()) > -1) {
          link.click();
          return false;
        }
      }

      window.find(palavra);
    },
    openProjeto(item, index) {
      if (this.onSelect != null) {
        this.onSelect(item, index);
      }
    }
  }
};
</script>