<template>
  <div>
    <div class="col-xs-12">
      <div class="col-xs-4">
        <label>ID matéria:</label>
        {{form.id}}
      </div>
      <div class="col-xs-4">
        <div class="form-group">
          <label>Hora Início:</label>
          {{form.hora_inicio}}
        </div>
      </div>

      <div class="col-xs-4">
        <div class="form-group">
          <label>Duração:</label>
          {{form.duracao}}
        </div>
      </div>

      <div class="col-xs-4">
        <div class="form-group">
          <label>Título:</label>
          {{form.titulo}}
        </div>
      </div>

      <div class="col-xs-4">
        <div class="form-group">
          <label>Emissora:</label>
          {{form.emissora}}
        </div>
      </div>

          <div class="col-xs-4">
        <div class="form-group">
          <label>Programa:</label>
          {{form.programa}}
        </div>
      </div>

      <div class="col-xs-4">
        <div class="form-group">
          <label>Apresentador:</label>
          {{form.apresentador}}
        </div>
      </div>
      <div class="col-xs-4">
        <div class="form-group">
          <label>Situação:</label>
          {{situacao}}
        </div>
      </div>

      <div class="col-xs-12">
        <video
          v-if="this.index_arquivo != null"
          v-bind:src="form.arquivos[this.index_arquivo].url"
          width="99%"
          height="330"
          preload="auto"
          controls="controls"
        ></video>
      </div>

      <div class="col-xs-12">
        <table class="table table-striped table-bordered">
          <thead>
            <tr>
              <th colspan="4">Arquivos</th>
            </tr>
          </thead>
          <tbody v-if="form.arquivos != null ">
            <tr v-for="(item,index) in form.arquivos" :key="index">
              <td>{{item.nome}}</td>

              <td style="width: 30px">
                <a style="cursor:pointer" v-on:click="openFile(item, index)">
                  <span class="glyphicon glyphicon-play-circle"></span>
                </a>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="col-xs-12">
        <table class="table table-striped table-bordered">
          <thead>
            <tr>
              <th>Cliente</th>
              <th>Tópico</th>
              <th>Impacto</th>
              <th>Citação Direta</th>
            </tr>
          </thead>
          <tbody v-if="form.clientes != null ">
            <tr v-for="(item,index) in form.clientes" :key="index">
              <td>{{item.cliente}}</td>
              <td>{{item.topico}}</td>
              <td>{{item.impacto}}</td>
              <td>{{item.cita_cliente != null && item.cita_cliente == 1? "Sim":"Não" }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div
        class="col-xs-12"
        v-if="id != null && form != null && form.id != null && id.toString().length > 1 "
      >
        <div class="panel panel-default">
          <div class="panel-body">
            <button
              type="button"
              class="btn btn-default pull-right"
              v-on:click="modo_email = !modo_email"
              style="margin-left: 20px"
            >Enviar esta matéria por e-mail</button>
            <envio_email
              :id_materia="id"
              v-if="modo_email"
              :onEnviado="onEnviado"
              :clientes="form.clientes"
            ></envio_email>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import envio_email from "../eventos/EnvioEmail";
export default {
  components: {
    envio_email
  },
  props: {
    id: {
      default: "0",
      type: String
    }
  },
  data: function() {
    return {
      form: {},
      index_arquivo: null,
      show_modal: false,
      modo_email: false
    };
  },
  computed: {
    situacao: {
      get: function() {
        if (this.form == undefined || this.form == null) {
          return "Rascunho";
        }

        var form = this.form;

        if (form.status == null || form.status == 0) {
          return "Rascunho";
        }
        if (form.status == 1) {
          return "Matéria cadastrada, aguardando correção.";
        }
        if (form.status == 2) {
          return "Matéria corrigida, aguardando liberação.";
        }
        if (form.status == 3) {
          return "Matéria liberada, aguardando envio de email.";
        }
        if (form.status == 4) {
          return "Matéria já enviada por email.";
        }

        return form.status;
      }
    }
  },
  mounted() {
    var self = this;

    obj_api.call("materia_gerada/" + this.id, "get", {}, function(retorno) {
      self.form = retorno.data;
    });
  },
  methods: {
    onEnviado() {
      this.form.status = 4;
    },
    closeModal() {
      this.show_modal = false;
    },
    openFile(item, index) {
      console.log("Estou aqui? " + index);
      var self = this;
      self.index_arquivo = null;
      self.show_modal = true;
      setTimeout(function() {
        self.index_arquivo = index;
      }, 50);
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