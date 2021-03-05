<template>
  <div>
    <section class="col-lg-12">
      <section class="col-lg-9" style="padding-left: 0px; margin-left: 0px">
        <h1 style="padding-left: 0px; margin-left: 0px">Configurar Busca</h1>
        <ol class="breadcrumb" style="display: none">
          <li>
            <a href="#">
              <i class="fa fa-dashboard"></i> Home
            </a>
          </li>
          <li>
            <a href="#">Forms</a>
          </li>
          <li class="active">General Elements</li>
        </ol>
      </section>

      <section class="col-lg-3" style="padding-top: 30px">
        <a href="#" v-on:click="botao_voltar" v-if="botao_voltar_visible">
          <i class="fa fa-arrow-left"></i> Voltar para lista
        </a>
      </section>
    </section>

    <div class="col-lg-12">
      <div v-if="show_message == 'on' " class="alert alert-success">{{message_text}}</div>
    </div>

    <section class="col-xs-12">
      <div class="box">
        <div class="box-header with-border"></div>
        <div class="box-body">
          <cad_table :id_load="id_load"></cad_table>
        </div>
      </div>
    </section>
  </div>
</template>
<script>
import SearchQueriesListCadTable from "./SearchQueriesListCadTable";
export default {
  props: ["id_load", "onBack"],
  components: {
    cad_table: SearchQueriesListCadTable
  },
  methods: {
    botao_voltar() {
      var self = this;

      if (this.onBack != null && this.onBack != undefined) {
        this.onBack(self);
      }
    }
  },
  data: function() {
    return {
      botao_voltar_visible: true,
      show_message: "off",
      message_text: ""
    };
  }
};
</script>