<template>
  <div>
    <section class="col-lg-12">
      <section class="col-lg-9" style="padding-left: 0px; margin-left: 0px">
        <h1 style="padding-left: 0px; margin-left: 0px">Cadastro de Matéria</h1>
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
        <a href="#" v-on:click="onBack">
          <i class="fa fa-arrow-left"></i> Voltar para lista
        </a>
      </section>
    </section>
    <div class="col-xs-12" v-if="loading">
      <i class="fa fa-spinner"></i> Carregando....
    </div>
    <section class="col-lg-12" style="padding-top: 20px">
      <div class="box">
        <div class="box-header with-border"></div>
        <div class="box-body">
          <CadMateria
            v-if="id_materia_rascunho != null && form != null"
            v-bind:id_materia_rascunho="id_materia_rascunho"
            v-bind:onSave="onSave"
            v-bind:onBack="onBack"
            v-bind:onRemove="onRemove"
            :ids_recortes="ids_recortes"
            :start_clientes="start_clientes"
            :form="form"
          ></CadMateria>
        </div>
      </div>
    </section>
  </div>
</template>
<script>
import CadMateria from "../eventos/CadMateria";

export default {
  components: {
    CadMateria: CadMateria
  },
  props: ["id_materia_rascunho", "onSave", "onRemove", "onBack", "id_evento"],
  data: function() {
    return {
      ids_recortes: [],
      start_clientes: [],
      form: null,
      loading: false
    };
  },
  mounted() {
    var self = this;

    var url = "eventos/" + this.id_evento;
    console.log("monted post url: " + url);
    var method = "get";

    var data = {simples: 1};
    this.loading = true;

    var fn_return = function(retorno) {
      console.log("Retorno? ");
      console.log(retorno);

      self.form = retorno.results;
      self.loading = false;
    };

    obj_api.call(url, method, data, fn_return);

    $(document).ready(function() {
      $(".content-wrapper").css({ "max-height": "auto", height: "1300px" });
    });
  }
};
</script>
