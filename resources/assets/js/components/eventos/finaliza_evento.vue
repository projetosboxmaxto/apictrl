<template>
  <span>
    <div
      class="alert alert-warning"
      v-if="bloqueado && form.bloqueado_por_id != null &&  form.bloqueado_por_id.toString() != id_operador_atual.toString() "
    >Este programa esta bloqueado pelo operador {{form.bloqueado_por}}</div>
    <a href="#" v-on:click="botao_voltar_interno" class="pull-right btn btn-sm btn-default">
      <i class="fa fa-arrow-left"></i>
      {{msg_botao_voltar}}
    </a>

    <button
      class="btn btn-danger btn-sm pull-right"
      v-if="id != null && form != null && form.status != 3"
      type="button"
      v-on:click="acao_bloqueio(3)"
    >
      <i class="fa fa-list-alt"></i> Finalizar este programa
    </button>
    <div
      class="alert alert-warning"
      v-if="id != null && form != null && form.status == 3"
      style="width: 200px"
    >Programa Finalizado</div>

    <a
      href="#"
      v-on:click="acao_bloqueio(2)"
      v-if="false && !bloqueado"
      class="pull-right btn btn-sm btn-danger"
    >
      <i class="fa fa-ban"></i>
      Bloquear
    </a>

    <a
      href="#"
      v-on:click="acao_bloqueio(1)"
      v-if="bloqueado && form.bloqueado_por_id != null &&  form.bloqueado_por_id.toString() == id_operador_atual.toString()"
      class="pull-right btn btn-sm btn-info"
    >
      <i class="fa thumbs-up"></i>
      Desbloquear
    </a>


    <a
      href="#"
      v-on:click="transcricao_by_programa"
      :disabled="disabled_transcricao"
      class="pull-right btn btn-sm btn-default"
      title="Realiza a busca de palavras chave neste programa"
    >
      <i class="fa fa-leanpub"></i>
       {{msg_busca_programa}}
    </a>
  </span>
</template>
<script>
export default {
  props: {
    id: {
      type: Number,
      default: null
    },
    botao_voltar: {
      type: Function,
      default: null
    },
    msg_botao_voltar: {
      type: String,
      default: null
    },
    onCarga:{
         type: Function,
         default: null
    }
  },
  data: function() {
    return {
      item: null,
      form: null,
      id_operador_atual: "",
      bloqueado: false,
      disabled_transcricao: false,
      msg_busca_programa: "Carga neste programa",
    };
  },
  methods: {
    botao_voltar_interno() {
      var form = this.form;

      var id_operador_atual = $("#id_operador").val();

      if (
        form.status == 2 &&
        (form.bloqueado_por_id == null ||
          form.bloqueado_por_id.toString() == id_operador_atual.toString())
      ) {
        this.acao_bloqueio(1, this.botao_voltar);
      } else {
        if (this.botao_voltar != null) {
          this.botao_voltar();
        }
      }
    },
    transcricao_by_programa(){
      var self = this;
      this.disabled_transcricao = true;
      this.msg_busca_programa = "Realizando  carga...";

         obj_api.call("search_evento/"+ this.form.id, "GET", {}, function (response){

           console.log("oncarga", response );

           if  ( self.onCarga != null && self.onCarga != undefined ){
              self.onCarga( response );
           }
               self.disabled_transcricao = false;
               self.msg_busca_programa = "Carga neste programa";

         });


    },
    acao_bloqueio(status, fn_final) {
      let self = this;

      if (self.form == null) {
        return;
      }

      if (status == 3) {
        obj_alert.confirm(
          "Atenção",
          "Deseja finalizar este programa? Isso irá tirar da lista de notificações.",
          "question",
          function(result) {
            if (!result.value) {
              return false;
            } else {
              self.acao_bloqueio2(status, fn_final);
            }
          }
        );
      } else {
        this.acao_bloqueio2(status, fn_final);
      }
    },
    acao_bloqueio2(status, fn_final) {
      let self = this;
      var data = {
        id: self.form.id,
        status: status,
        id_operador: $("#id_operador").val()
      };

      // console.log(data); return false;

      var fn_return = function(retorno) {
        self.form.bloqueado_por = retorno.form.bloqueado_por;
        self.form.bloqueado_por_id = retorno.form.bloqueado_por_id;
        self.form.status = retorno.form.status;
        self.form.data_status_change = retorno.form.data_status_change;

        if (retorno.form.status == null || retorno.form.status == 1) {
          self.bloqueado = false;
        }
        if (retorno.form.status != null && retorno.form.status == 2) {
          self.bloqueado = true;
        }

        if (fn_final != null) {
          fn_final();
        }
      };

      obj_api.call("eventos_status", "POST", data, fn_return);
    }
  },
  mounted() {
    var self = this;

    if ($("#id_operador").val() != "") {
      this.id_operador_atual = $("#id_operador").val();
    }

    if (this.id != null) {
      obj_api.call("eventos2/" + this.id, "get", {}, function(resultado) {
        self.item = resultado.data;
        self.form = resultado.data;
        if (self.form.status == null || self.form.status == 1) {
          self.bloqueado = false;
        }
        if (self.form.status != null && self.form.status == 2) {
          self.bloqueado = true;
        }
      });
    }
  }
};
</script>