<template>
  <div>
    <table class="table table-striped table-bordered">
      <thead>
        <tr>
          <th colspan="4">Recortes</th>
          <th v-if="show_remove== '1'"></th>
        </tr>

        <tr>
          <th v-if="false">Nome</th>
          <th>Hora Início</th>
          <th>Duração</th>
          <th>Operador</th>
          <th style="width: 30px"></th>
          <th style="width: 30px" v-if="show_remove== '1'"></th>
        </tr>
      </thead>

      <tbody v-if="items != null ">
        <tr v-for="(item,index) in items" :key="index">
          <td v-if="false">{{item.nome_projeto}}</td>
          <td>{{item.hora_inicio}}</td>
          <td>{{item.duracao}}</td>
          <td>{{item.nome_operador}}</td>

          <td style="width: 30px">
            <a style="cursor:pointer" v-on:click="openProjeto(item, index)">
              <span class="fa fa-edit"></span>
            </a>
          </td>
          <td style="width: 30px" v-if="show_remove== '1'">
            <a style="cursor:pointer" v-on:click="excluir(item, index)">
              <span class="fa fa-remove"></span>
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
    items: {
      default: [],
      type: Array
    },
    onSelect: {
      default: null,
      type: Function
    },
    show_remove: {
      default: "0",
      type: String
    },
    onDelete: {
      default: null,
      type: Function
    }
  },
  data: function() {
    return {};
  },
  mounted() {},
  methods: {
    openProjeto(item, index) {
      if (this.onSelect != null) {
        this.onSelect(item, index);
      }
    },
    excluir(item, index) {
      var self = this;
      obj_alert.confirm(
        "Atenção",
        "Deseja realmente remover este projeto? Não será mais possível recuperar seus arquivos.",
        "question",
        function(resposta) {
          if (resposta.value) {
            obj_api.call("project_delete", "POST", { id: item.id }, function(
              result
            ) {
              if (self.onDelete != null) {
                self.onDelete(item, index);
              }
            });
          }
        }
      );
    }
  }
};
</script>