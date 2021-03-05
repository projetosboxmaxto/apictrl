<template>
  <div class="col-xs-12" style="margin-top: 10px">
    <div v-if="loading">..Carregando</div>

    <section class="col-xs-12">
      <a
        href="#"
        v-on:click="botao_voltar"
        class="pull-right btn btn-sm btn-default"
        v-if="botao_voltar_visible"
      >
        <i class="fa fa-arrow-left"></i> Voltar para o programa
      </a>
    </section>

    <div class="col-xs-5">
      <div v-if="current_video != null">
        <video
          id="video_main"
          v-if="show_video"
          v-bind:src="current_video.url_load"
          width="99%"
          height="330"
          preload="auto"
          controls="controls"
        ></video>
        <div>
          <b>Vídeo em reprodução:</b>
          {{current_video.nome}}
        </div>
      </div>
    </div>
    <div class="col-xs-7">
      <div class="box box-primary">
        <div class="box-body">
          <div class="box-header with-border">
            <h3 class="box-title">Texto transcrito</h3>
          </div>
          <div
            id="divTexto"
            class="col-xs-12"
            v-if="current_video != null && current_text_list != null "
            style="max-height: 500px; overflow-y: scroll;"
          >
            <span
              v-for="(item,index) in current_text_list"
              :key="index"
              v-bind:style="getStyle(item)"
            >
              <a
                href="#!"
                v-if="item.alternatives[0] != undefined "
                class="link_video_legenda"
                v-on:click="click_time(item)"
                v-html="item.alternatives[0].text + ' '"
              ></a>
            </span>
          </div>
        </div>
      </div>

      <div class="box box-default" v-if="current_video != null && current_video.tipo == 'cut' ">
        <div class="box-body">
          <div class="box-header with-border">
            <h3 class="box-title">Dados do programa</h3>
          </div>
          <div class="col-xs-12">
            <div class="form-group">
              <b>Hora Início:</b>
              {{current_video.hora_inicio}}
              &nbsp;&nbsp;
              <b>Duração:</b>
              {{current_video.duracao}}
              &nbsp;&nbsp;
              <b>Veículo:</b>
              {{form.meta.emissora}}
              &nbsp;&nbsp;
              <b>Programa:</b>
              {{form.meta.programa}}
            </div>
          </div>

          <div v-if="current_video.id_materia_radiotv_jornal != null">
            <div class="col-xs-12">
              <div class="form-group">
                <label style="color: red">Matéria Gerada =</label>
                <b>ID:</b>
                {{current_video.id_materia_radiotv_jornal}}
                &nbsp;&nbsp;
                <b>Título:</b>
                {{current_video.titulo_materia}}
                &nbsp;&nbsp;
                <b>Apresentador:</b>
                {{current_video.jornalista}}
                &nbsp;&nbsp;
                <b>Dt Cadastro:</b>
                {{current_video.data_insert_materia | datetime_show}}
              </div>
            </div>
          </div>

          <div v-if="current_video.id_materia_radiotv_jornal == null">
            <div class="col-xs-6">
              <div class="form-group">
                <label>Título</label>
                <input
                  type="text"
                  name="materia_titulo"
                  id="materia_titulo"
                  class="form-control"
                  v-model="materia.titulo"
                />
              </div>
            </div>
            <div class="col-xs-6">
              <div class="form-group">
                <label>Apresentador</label>
                <select
                  name="materia_id_apresentador"
                  id="materia_id_apresentador"
                  class="form-control"
                  v-model="materia.id_apresentador"
                >
                  <option
                    v-for="(item,index) in list_apresentador"
                    :key="index"
                    :value="item.id"
                  >{{item.nome}}</option>
                </select>
              </div>
            </div>

            <div class="col-xs-12">
              <div class="form-group">
                <label>Sinpose</label>
                <textarea v-model="materia.sinopse" class="form-control"></textarea>
              </div>
            </div>

            <div class="col-xs-12">
              <button
                type="button"
                v-on:click="gera_materia"
                :disabled="salvando_materia"
                class="btn btn-danger pull-right"
              >
                <i class="fa fa-tv"></i>
                {{msg_salvando_materia}}
              </button>
            </div>
          </div>
        </div>
      </div>

      <div
        class="box box-primary"
        v-if="current_video != null && current_text_list != null && clientes.length > 0 "
      >
        <div class="box-body">
          <grid_cliente :items="clientes"></grid_cliente>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import grid_cliente from "../eventos/GridCliente.vue";

export default {
  components: {
    grid_cliente
  },
  props: ["id_load", "post_type", "show_back_button", "onBack", "onSave"],
  data: function() {
    return {
      id: "",
      path: "",
      current_video: null,
      nome: "",
      id_evento: "",
      tempo_realizado_minutos: "",
      hora_inicio: "",
      id_materia_radiotv_jornal: "",

      disableButton: false,
      publicar_titulo: "Salvar",
      titulo_acao: "eventos_arquivos",
      botao_voltar_visible: false,

      show_message: "off",
      message_text: "",
      message_type: "success",
      interval_message: null
    };
  },

  mounted() {
    let self = this;

    if (this.show_back_button != null && this.show_back_button != undefined) {
      this.botao_voltar_visible = this.show_back_button;
    }

    if (this.id_load == null || this.id_load == "") {
      return;
    }

    var url = "eventos_arquivos/" + this.id_load;
    // console.log("monted post url: " + url);
    var method = "get";

    this.disableButton = true;

    var data = {};

    var fn_return = function(retorno) {
      // console.log("Retorno? ");
      // console.log( retorno );

      var item = retorno.item;

      self.carregaForm(item);

      self.disableButton = false;
    };

    obj_api.call(url, method, data, fn_return);
  },
  methods: {
    carregaForm(item) {
      var self = this;
      self.id = item.id;
      self.path = item.path;
      self.nome = item.nome;
      self.id_evento = item.id_evento;
      self.tempo_realizado_minutos = item.tempo_realizado_minutos;
      self.hora_inicio = item.hora_inicio;
      self.id_materia_radiotv_jornal = item.id_materia_radiotv_jornal;
      self.current_video = item.current_video;
    }
  }
};
</script>