<template>
  <div>
    <table class="table table-striped table-bordered">
      <thead>
        <tr>
          <th colspan="4">Arquivos selecionados</th>
        </tr>
      </thead>
      <tbody v-if="items != null ">
        <tr v-for="(item,index) in items" :key="index">
          <td>{{item.nome}}</td>
          <td>{{item.hora_inicio}}</td>
          <td>{{item.duracao}}</td>

          <td style="width: 30px">
            <a style="cursor:pointer" v-on:click="openFile(item, index)">
              <span class="glyphicon glyphicon-play-circle"></span>
            </a>
          </td>
        </tr>
      </tbody>
    </table>

    <div class="modal" id="myModalByFile" tabindex="-1" role="dialog" v-if="show_modal">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Vídeo</h5>
          </div>
          <div class="modal-body">
            <video
              v-if="this.index_arquivo != null"
              v-bind:src="items[this.index_arquivo].url_load"
              width="99%"
              height="330"
              preload="auto"
              controls="controls"
            ></video>
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
export default {
  props: {
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
    },
    ids: {
      default: "0",
      type: String
    }
  },
  data: function() {
    return {
      items: [],
      show_modal: false,
      index_arquivo: 0
    };
  },

  mounted() {
    var self = this;

    obj_api.call("eventos_arquivos_byid", "get", { ids: this.ids }, function(
      retorno
    ) {
      self.items = retorno.data;
    });
  },
  methods: {
    closeModal() {
      this.show_modal = false;
    },
    openFile(item, index) {
      console.log("Estou aqui? " + index);
      var self = this;
      self.index_arquivo = null;
      self.show_modal = true;
      self.index_arquivo = index;

      setTimeout(function() {
        $("#myModalByFile").modal({ show: true });
      }, 200);
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