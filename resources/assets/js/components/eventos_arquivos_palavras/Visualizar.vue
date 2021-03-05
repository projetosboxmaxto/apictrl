<template>
  <div class="col-xs-12">
    <div class="box box-primary">
      <div class="box-body" v-if="form != null">
        <div v-if="form != null">
          <video
            v-if="form.nome_arquivo.indexOf('.mp4') >-1"
            id="video_modal"
            v-bind:src="form.url_load"
            width="99%"
            height="330"
            preload="auto"
            controls="controls"
          ></video>
          <audio
            v-if="form.nome_arquivo.indexOf('.mp3') >-1"
            id="video_modal"
            v-bind:src="form.url_load"
            width="99%"
            preload="auto"
            controls="controls"
          ></audio>
          <div>
            <b>Vídeo em reprodução:</b>
            {{form.nome_arquivo}}
          </div>
        </div>

        <div>{{form.trecho}}</div>
        <div>
          <button
            class="btn btn-danger pull-right"
            v-if="form.status==1"
            type="button"
            v-on:click="descartar"
          >
            <i class="fa fa-trash">&nbsp;Descartar da lista</i>
          </button>

          <button
            class="btn btn-info pull-right"
            v-if="form.status==2"
            type="button"
            v-on:click="de_volta"
          >
            <i class="fa fa-plus">&nbsp;Trazer notificação de volta</i>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  props: {
    id_load: {
      type: Number,
      default() {
        return null;
      }
    }
  },
  data: function() {
    return {
      form: null,
      disableButton: true
    };
  },
  mounted() {
    this.load_dados();
  },
  methods: {
    indica_status(id, status) {
      let self = this;
      obj_api.call(
        "eventos_arquivos_palavras_status",
        "POST",
        {
          id: id,
          status: status
        },
        function(response) {
          self.form.status = status;
        }
      );
    },
    descartar() {
      this.indica_status(this.form.id, 2);
    },
    de_volta() {
      this.indica_status(this.form.id, 1);
    },
    load_dados() {
      let self = this;

      //if (this.show_back_button != null && this.show_back_button != undefined) {
      //  this.botao_voltar_visible = this.show_back_button;
      // }

      if (this.id_load == null || this.id_load == "") {
        return;
      }

      var url = "eventos_arquivos_palavras/" + this.id_load;
      console.log("monted post url: " + url);
      var method = "get";

      this.disableButton = true;

      var data = {};

      var fn_return = function(retorno) {
        // console.log("Retorno? ");
        // console.log( retorno );

        var item = retorno.item;

        self.form = item;

        self.disableButton = false;

        if (item.tempo_seg != null && item.tempo_seg > -1) {
          setTimeout(function() {
            var vid = document.getElementById("video_modal");
            if (vid != null) {
              vid.currentTime = item.tempo_seg;
              vid.play();
            }
          }, 1000);
        }
      };

      obj_api.call(url, method, data, fn_return);
    }
  }
};
</script>