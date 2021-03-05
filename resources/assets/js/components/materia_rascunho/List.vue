<template>
  <div>
    <table class="table table-striped table-bordered">
      <thead>
        <tr>
          <th colspan="4">Matérias Cadastradas</th>
        </tr>

        <tr>
          <th>Título</th>
          <th>Clientes</th>
          <th>ID-Sistema</th>
          <th style="width: 30px"></th>
        </tr>
      </thead>

      <tbody v-if="items != null ">
        <tr v-for="(item,index) in items" :key="index">
          <td>{{item.titulo}}</td>
          <td>{{item.cliente_list}}</td>
          <td>{{item.id_materia_radiotv_jornal}}</td>

          <td style="width: 30px">
            <a style="cursor:pointer" v-on:click="openProjeto(item, index)">
              <span class="fa fa-edit"></span>
            </a>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
<script>
export default {
  props: {
    id_projeto: {
      default: 0,
      type: Number
    },
    onSelect: {
      default: null,
      type: Function
    }
  },
  data: function() {
    return {
      items: []
    };
  },
  mounted() {
    this.load_data();
  },
  methods: {
    load_data() {
      let self = this;

      obj_api.call(
        "materia_rascunho?id_projeto=" + this.id_projeto.toString(),
        "get",
        {},
        function(retorno) {
          console.log(
            "Retorno materia rascunho ? " +
              "materia_rascunho?id_projeto=" +
              self.id_projeto.toString()
          );
          console.log(retorno);
          self.items = retorno.data;
        }
      );
    },
    openProjeto(item, index) {
      if (this.onSelect != null) {
        this.onSelect(item, index);
      }
    }
  }
};
</script>