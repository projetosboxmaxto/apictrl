<template>
  <div v-if="clientes.length > 0 ">
    <label>Selecione os clientes que vão receber o e-mail</label>
    <table class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>
            <input type="checkbox" v-model="selectAll" title="Selecionar todos" />
          </th>
          <th>Cliente</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(item,index) in clientes" :key="index">
          <th>
            <input type="checkbox" v-model="selected" :value="item.id_cliente" number />
          </th>
          <td>
            <span v-html="item.cliente"></span>
          </td>
        </tr>
      </tbody>
      <tfoot v-if="this.selected.length > 0 ">
        <tr>
          <td colspan="2">
            <button
              type="button"
              class="btn btn-default btn-danger pull-right"
              style="margin-left: 20px; color: white"
              :disabled="enviando"
              v-on:click="enviar_email_final"
            >
              <i class="fa fa-envelope"></i>
              {{enviando ? "enviando.." : "Enviar e-mail"}}
            </button>

            <button
              type="button"
              :disabled="enviando"
              v-on:click="enviar_email_teste"
              class="btn btn-default pull-right"
              style="margin-left: 20px"
            >
              <i class="fa fa-envelope"></i>
              {{enviando ? "enviando.." : "Enviar e-mail (Teste)"}}
            </button>
          </td>
        </tr>
      </tfoot>
    </table>
  </div>
</template>
<script>
export default {
  props: {
    clientes: {
      type: Array,
      default: []
    },
    id_materia: {
      type: String,
      default: ""
    },
    onEnviado: {
      type: Function,
      default: null
    }
  },

  data: function() {
    return {
      selected: [],
      enviando: false
    };
  },
  computed: {
    selectAll: {
      get: function() {
        return this.clientes
          ? this.selected.length == this.clientes.length
          : false;
      },
      set: function(value) {
        var selected = [];

        if (value) {
          this.clientes.forEach(function(item) {
            selected.push(item.id_cliente);
          });
        }

        this.selected = selected;
      }
    }
  },
  methods: {
    enviar_email_teste() {
      this.enviar_email(true);
    },
    enviar_email_final() {
      this.enviar_email(false);
    },
    enviar_email(teste) {
      var self = this;
      this.enviando = true;
      var continuacao = "";
      if (teste) {
        continuacao += "&teste=1";
      }
      var ids_clientes = this.selected.join(",");
      var url =
        "?acao=enviar_email&id=" +
        this.id_materia +
        "&ids_clientes=" +
        ids_clientes +
        continuacao;
      console.log(url);
      obj_api.call_midiaclip("sistema", url, "GET", function(response) {
        var txt = response.Nome;
        self.enviando = false;
        if (!teste) {
          //self.form.status = 4;
          if (self.onEnviado != null) {
            self.onEnviado(true);
          }
        }

        obj_alert.show(
          "E-mail enviado com sucesso!",
          "A matéria foi enviada para os seguintes contatos: " + txt,
          "success",
          null
        );
      });
    }
  }
};
</script>