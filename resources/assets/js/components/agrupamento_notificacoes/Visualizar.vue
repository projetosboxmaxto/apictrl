<template>
  <div class="col-xs-12">
    <div class="box box-primary">
      <div class="box-body" v-if="form != null">
        <div v-if="form != null">
          <div class="col-xs-12">
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
              <div v-if="id_load != null && id_load != undefined">
                          <b>ID:</b> {{id_load}}
              </div>
            </div>
          </div>
        </div>

        <div style="max-height: 140px; overflow-y: scroll" class="col-xs-12" v-html="form.trecho"></div>

        <div class="col-xs-12" v-if="form != null && form.json != null ">
          <b>Termos encontrados</b>
          <table class="table table-striped table-bordered">
            <tr v-for="(item,index) in form.tempos" :key="index">
              <td>{{item.palavra}}</td>
              <td>{{item.tempo}}</td>
              <td>{{item.nome_cliente}}</td>
            </tr>
          </table>
        </div>

        <div>
           <button
            class="btn btn-success pull-right"
            v-if="tinder && form.status==1"
            style="margin-left: 20px"
            type="button"
            v-on:click="aprovado"
          >
            <i class="fa fa-check-circle">&nbsp;Aprovar</i>
          </button>

          <button
            class="btn btn-danger pull-right"
            v-if="form.status==1 || form.status == 3"
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
    },
    onIndicaStatus:{
      type: Function,
      default(){
        return null;
      }
    },
    tinder:{
      type: Boolean,
      default(){
        return false;
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

      let data = {
        id: id,
        status: status
      };

      if ( this.tinder ){
        data["tinder"] = 1;
      }

      //console.log("indica_status", data);
      obj_api.call("agrupamento_notificacoes_status", "POST", data, function(
        response
      ) {
        if (self.form != null && self.form != undefined) {
          self.form.status = status;
        }
      });
    },

    indica_status_global(id, status) {
      let self = this;
      var data = {
        id: id,
        status: status,
        id_operador: $("id_operador").val()
      };

      obj_api.call(
        "agrupamento_notificacoes_status_evento",
        "POST",
        data,
        function(response) {}
      );
    },
    descartar() {
      this.indica_status(this.form.id, 2);
      if ( this.onIndicaStatus != null ){
        this.onIndicaStatus(2, this.form.id );
      }
    },
    de_volta() {
      this.indica_status(this.form.id, 1);
       if ( this.onIndicaStatus != null ){
        this.onIndicaStatus(1, this.form.id );
      }
      /*
      1 - não processado,
      2 - Descartado,
      3 - Em visualização
      4 - Permitido para uso.


      */
    },
    aprovado() {
      console.log("aprovado? ");
      this.indica_status(this.form.id, 4);
      if ( this.onIndicaStatus != null ){
        this.onIndicaStatus(4, this.form.id );
      }
    },
    load_dados() {
      let self = this;

      //if (this.show_back_button != null && this.show_back_button != undefined) {
      //  this.botao_voltar_visible = this.show_back_button;
      // }

      if (this.id_load == null || this.id_load == "") {
        return;
      }

      if (! this.tinder ){

         this.indica_status(this.id_load, 3);
      }

      //this.indica_status_global(this.id_load, 2);

      var url = "agrupamento_notificacoes/" + this.id_load;

      console.log("monted post url: " + url);
      var method = "get";

      this.disableButton = true;

      var data = {};

      var fn_return = function(retorno) {
        // console.log("Retorno? ");
        // console.log( retorno );

        var item = retorno.item;

        var id_evento_arquivo = item.id_evento_arquivo;

        if (item.status == null) {
          item.status = 1;
        }

        self.form = item;

        self.disableButton = false;

        var trecho = self.form.trecho;

        if (self.form.palavras != "") {
          var ar_palavras = self.form.palavras.split(",");

          for (var z = 0; z < ar_palavras.length; z++) {
            trecho = trecho.replace(
              ar_palavras[z].toLowerCase(),
              "<span style='background: #CCCCCC'>" +
                ar_palavras[z].toLowerCase() +
                "</span>"
            );
          }
        }

        self.form.trecho = trecho;

        if (item.tempo_seg != null && item.tempo_seg > -1) {
          setTimeout(function() {
            var vid = document.getElementById("video_modal");
            if (vid != null) {
              vid.currentTime = item.tempo_seg;
              try{
               vid.play();

              }catch(Exp){

              }
            }
          }, 1000);
        }
      };

      obj_api.call(url, method, data, fn_return);
    }
  }
};
</script>