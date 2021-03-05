<template>
  <div>
    <div class="col-xs-12" style="margin-top: 10px">
      <div v-if="loading">..Carregando</div>

      <section class="col-xs-12">
        <div class="col-xs-6">
          <h4>{{nome_programa}}</h4>
        </div>

        <div class="col-xs-6">
          <finaliza_evento
            v-if="form != null"
            :id="form.id"
            :botao_voltar="botao_voltar"
            :msg_botao_voltar="msg_botao_voltar"
          ></finaliza_evento>
          <a
            v-if="false"
            href="#"
            v-on:click="botao_voltar"
            class="pull-right btn btn-sm btn-default"
          >
            <i class="fa fa-arrow-left"></i> Voltar para lista
          </a>
        </div>
      </section>

      <div class="col-xs-5">
        <div class="box box-primary">
          <div class="box-body">
            <video
              id="video_main"
              v-if="current_video != null"
              v-bind:src="current_video.url_load"
              width="99%"
              v-bind:height="current_video.url_load.indexOf('.mp3') > -1 ? 100 : 330"
              preload="auto"
              v-on:timeupdate="setCurrentTime"
              controls="controls"
            ></video>

            <div style="max-height: 300px; overflow-y: scroll">
              <table class="table table-striped table-bordered">
                <tbody v-if="form != null && form.arquivos != null ">
                  <tr
                    v-for="(item,index) in form.arquivos"
                    :key="index"
                    v-bind:class="current_video != null && current_video.id == item.id ? 'bg-light-blue-active': ''"
                  >
                    <td>{{item.nome}}</td>
                    <td style="width: 30px">
                      <a
                        style="cursor:pointer"
                        v-if="tem_clientes(item)"
                        v-on:click="abrir_clientes(item, index)"
                      >
                        <i
                          class="fa fa-bell"
                          v-bind:style="current_video != null && current_video.id == item.id ? 'color: white': '' "
                        ></i>
                      </a>
                    </td>
                    <td style="width: 30px" v-if="false">
                      <input
                        type="checkbox"
                        @change="add_video(item.id, index)"
                        v-bind:name="'chk_'+item.id"
                        v-bind:id="'chk_'+item.id"
                      />
                    </td>
                    <td style="width: 30px">
                      <a style="cursor:pointer" v-on:click="openVideo(item, index)">
                        <span
                          class="glyphicon glyphicon-play-circle"
                          v-bind:style="current_video != null && current_video.id == item.id ? 'color: white': '' "
                        ></span>
                      </a>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div v-if="form != null && form.arquivos != null && form.arquivos.length > 0 ">
              <table class="table table-striped table-bordered">
                <tfoot>
                  <tr>
                    <td colspan="3">
                      <div class="col-xs-12 input-group">
                        <input
                          type="text"
                          placeholder="Nome do projeto"
                          id="nome_projeto"
                          v-model="nome_projeto"
                          class="form-control"
                          maxlength="100"
                          v-if="false"
                        />
                        <span class="input-group-btn">
                          <button
                            type="button"
                            v-on:disabled="loading_filhos"
                            class="pull-right btn btn-primary"
                            v-on:click="novoProjeto"
                          >
                            <i class="fa fa-cut"></i>
                            Criar Recortes
                          </button>
                        </span>
                      </div>
                    </td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
        </div>

        <div class="box box-primary">
          <div class="box-body" style="max-height: 200px">
            <div v-if="loading_filhos">..Carregando</div>
            <grid_filhos
              :items="items_filhos"
              v-if="show_filho"
              :onSelect="onSelectFilho"
              :onDelete="onDeleteFilho"
              show_remove="1"
            ></grid_filhos>
          </div>
        </div>
      </div>

      <div class="col-xs-7">
        <div class="box box-primary">
          <div class="box-body">
            <div
              id="divTexto"
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

        <div
          class="box box-primary"
          v-if="current_video != null && current_text_list != null && clientes.length > 0 "
        >
          <div class="box-body" style="max-height: 250px; overflow-y:scroll">
            <grid_cliente :items="clientes"></grid_cliente>
          </div>
        </div>
      </div>

      <!--

 onloadedmetadata="obj_corteaudiovideo.loadControls(this)" onplay="obj_corteaudiovideo.setPlayVideo(this)" onpause="obj_corteaudiovideo.onpause(this)"

      -->
    </div>

    <div class="modal" id="myModal" tabindex="-1" role="dialog" v-if="show_modal">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Notificações</h5>
          </div>
          <div class="modal-body">
            <div v-if="meta_dados_visualizar">
              <grid_cliente :items="meta_dados_visualizar.clientes"></grid_cliente>
            </div>
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
<style>
a.link_video_legenda {
  color: #333;
}
a.link_video_legenda:hover {
  background-color: #c3dcde;
}
</style>

<script>
import grid_filhos from "./GridFilhos.vue";
import grid_cliente from "./GridCliente.vue";
import Util from "../../library/Util";
import finaliza_evento from "./finaliza_evento.vue";

export default {
  components: {
    grid_filhos,
    grid_cliente,
    finaliza_evento
  },
  props: [
    "id_load",
    "post_type",
    "show_back_button",
    "onBack",
    "onSave",
    "onSelectFilho",
    "id_load_arquivo",
    "tempo_inicio"
  ],
  data: function() {
    return {
      form: null,
      current_time: -1,

      current_video: null,
      current_text_list: null,

      disableButton: false,
      publicar_titulo: "Salvar",
      titulo_acao: "eventos",
      botao_voltar_visible: false,

      show_message: "off",
      message_text: "",
      message_type: "success",
      interval_message: null,

      obj_video: null,
      loading: false,

      arquivos_ids: [],
      items_filhos: [],
      show_filho: false,
      loading_filhos: false,

      clientes: [],

      bloqueado: false,
      nome_programa: "",
      nome_projeto: "",

      id_operador_atual: -1,
      index_arquivos: 0,
      meta_dados_visualizar: null,
      show_modal: false,

      msg_botao_voltar: "Voltar"
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

    this.abreProjetoUsuario();
    return false; //Esse componente não vai mais ser usado por enquanto.

    if ($("#id_operador").val() != "") {
      this.id_operador_atual = $("#id_operador").val();
    }

    var url = "eventos/" + this.id_load;
    console.log("monted post url: " + url);
    var method = "get";

    this.disableButton = true;
    self.loading = true;

    var data = {};

    var fn_return = function(retorno) {
      console.log("Retorno? ");
      console.log(retorno);

      self.form = retorno.results;
      self.nome_programa =
        retorno.item.meta.programa +
        " - " +
        retorno.item.meta.emissora +
        " - " +
        Util.dateToBR(retorno.item.meta.data);

      if (self.form.status == null || self.form.status == 1) {
        self.bloqueado = false;
      }
      if (self.form.status != null && self.form.status == 2) {
        self.bloqueado = true;
      }
      self.disableButton = false;
      self.loading = false;
      self.load_filhos();

      if (self.form.status == null || self.form.status == 1) {
        //Não bloqueado...
        self.acao_bloqueio(2);
      }

      if (
        self.id_load_arquivo != null &&
        self.id_load_arquivo != undefined &&
        self.id_load_arquivo != ""
      ) {
        for (var o = 0; o < self.form.arquivos.length; o++) {
          if (
            self.form.arquivos[o] != null &&
            self.form.arquivos[o].id.toString() ==
              self.id_load_arquivo.toString()
          ) {
            self.openVideo(self.form.arquivos[o], o);
            if (
              self.tempo_inicio != null &&
              self.tempo_inicio != undefined &&
              self.tempo_inicio > -1
            ) {
              setTimeout(function() {
                var video_main = document.getElementById("video_main");
                console.log("video_main?", self.tempo_inicio);
                if (video_main != null) {
                  video_main.currentTime = self.tempo_inicio;
                  // video_main.play();

                  //break;
                }
              }, 500);
            } else {
            }
          }
        }

        self.msg_botao_voltar = "Voltar para a lista";
      } else {
        //  if ( self.form.arquivos.length == 1 ){
        //        self.openVideo( self.form.arquivos[0], 0 );
        //    }
      }
    };

    obj_api.call(url, method, data, fn_return);

    $(".content-wrapper").css({ "max-height": "auto", height: "1100px" });
  },
  methods: {
    abrir_clientes(item, index) {
      this.index_arquivos = index;

      if (
        item.meta_dados != null &&
        item.meta_dados != undefined &&
        item.meta_dados.indexOf("{") > -1
      ) {
        var obj = JSON.parse(item.meta_dados);

        this.meta_dados_visualizar = obj;
      }

      this.show_modal = true;

      setTimeout(function() {
        $("#myModal").modal({ show: true });
      }, 200);
    },
    closeModal() {
      this.show_modal = false;
    },
    tem_clientes(item) {
      if (
        item.meta_dados != null &&
        item.meta_dados != undefined &&
        item.meta_dados.indexOf("{") > -1
      ) {
        var obj = JSON.parse(item.meta_dados);

        if (obj.clientes.length <= 0) {
          return false;
        }

        if (obj.clientes.length > 0) {
          return true;
        }
      }

      return false;
    },
    acao_bloqueio(status, fn_final) {
      let self = this;

      if (self.form == null) {
        return;
      }

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
    },
    selecionaProjeto(id) {
      if (this.onSelectFilho == null || this.onSelectFilho == undefined) {
        return;
      }

      for (var i = 0; i < this.items_filhos.length; i++) {
        if (this.items_filhos[i].id.toString() == id.toString()) {
          this.onSelectFilho(this.items_filhos[i]);
        }
      }
    },
    selecionaProjeto2(id) {
      var self = this;
      if (this.onSelectFilho == null || this.onSelectFilho == undefined) {
        return;
      }

      obj_api.call("eventos_filhos2?id=" + id, "GET", {}, function(retorno) {
        self.onSelectFilho(retorno.data[0]);
      });
    },
    abreProjetoUsuario() {
      var self = this;
      var data = {
        // arquivos: JSON.stringify(arquivos),
        id_operador: $("#id_operador").val(),
        nome_projeto: $("#nome_operador").val()
      };

      var fn_return_inicio = function(retorno) {
        self.selecionaProjeto2(retorno.id);
        //console.log(
        //   "selecionaProjeto " + retorno.id + " id_evento =  " + self.id_load
        // );
      };

      obj_api.call(
        "project_hasfilho/" + self.id_load,
        "POST",
        data,
        fn_return_inicio
      );
    },
    novoProjeto() {
      var self = this;

      var arquivos = [];

      self.loading_filhos = true;

      var data = {
        // arquivos: JSON.stringify(arquivos),
        id_operador: $("#id_operador").val(),
        nome_projeto: $("#nome_operador").val()
      };
      //self.nome_projeto
      // console.log(data); return false;

      var fn_return_inicio = function(retorno) {
        if (retorno.has) {
          self.selecionaProjeto(retorno.id);
        } else {
          var fn_return = function(retorno) {
            console.log("Retorno? ");
            console.log(retorno);
            self.load_filhos(self.selecionaProjeto, retorno.id);
            self.nome_projeto = "";
            setTimeout(function() {
              //self.selecionaProjeto(retorno.id);
            }, 50);
          };

          obj_api.call("project/" + self.form.id, "POST", data, fn_return);
        }
      };

      console.log("has filhos?");
      obj_api.call(
        "project_hasfilho/" + self.form.id,
        "POST",
        data,
        fn_return_inicio
      );
      //obj_api.callFormData("project/" +this.form.id,"POST", data, fn_return );
    },

    load_filhos(fn_resultado, idtmp) {
      var self = this;

      self.items_filhos = [];
      self.show_filho = false;
      self.loading_filhos = true;

      obj_api.call("eventos_filhos?id=" + this.form.id, "GET", {}, function(
        retorno
      ) {
        self.items_filhos = retorno.data;
        self.show_filho = true;
        self.loading_filhos = false;
        if (
          fn_resultado != null &&
          fn_resultado != undefined &&
          idtmp != null &&
          idtmp != undefined
        ) {
          fn_resultado(idtmp);
        }
      });
    },

    add_video(id, index) {
      var check = document.getElementById("chk_" + id);

      if (!check.checked) {
        var inde = this.arquivos_ids.indexOf(id);
        if (inde > -1) {
          this.arquivos_ids.splice(inde, 1);
        }
      } else {
        var inde = this.arquivos_ids.indexOf(id);
        if (inde < 0) {
          this.arquivos_ids.push(id);
        }
      }

      console.log("add? ");
      console.log(id);
      console.log(this.arquivos_ids);
      console.log(check.checked);
    },

    getStyle(item) {
      if (
        item.start_time < this.current_time &&
        item.end_time >= this.current_time
      ) {
        return "background-color: #CCCCCC";
      }

      return "";
    },

    click_time(item) {
      if (this.obj_video == null) {
        this.obj_video = document.getElementById("video_main");
      }

      this.obj_video.currentTime = item.start_time;
      this.obj_video.play();
    },

    setCurrentTime() {
      if (this.obj_video == null) {
        this.obj_video = document.getElementById("video_main");
      }

      this.current_time = this.obj_video.currentTime;
    },
    onDeleteFilho(item, index) {
      var self = this;
      this.items_filhos.splice(index, 1);
      this.show_filho = false;

      setTimeout(function() {
        self.show_filho = true;
      }, 100);
    },

    openVideo(item, index) {
      this.current_video = null;

      this.current_text_list = null;

      this.current_video = item;
      this.current_text_list = JSON.parse(item.json);
      this.clientes = [];

      if (item.meta_dados != null && item.meta_dados != undefined) {
        var obj = JSON.parse(item.meta_dados);

        if (obj.clientes != null) {
          this.clientes = obj.clientes;
        }
      }

      this.setaPalavrasChave();

      this.obj_video = document.getElementById("video_main");
    },

    setaPalavrasChave() {
      console.log("seta palavras chave? ");
      console.log(this.clientes);

      for (var o = 0; o < this.current_text_list.length; o++) {
        if (
          this.clientes.length > 0 &&
          this.current_text_list[o].alternatives.length > 0 &&
          this.current_text_list[o].alternatives[0].text != null &&
          this.current_text_list[o].alternatives[0].text != undefined
        ) {
          for (var i = 0; i < this.clientes.length; i++) {
            if (
              this.clientes[i].palavras != null &&
              this.clientes[i].palavras != undefined
            ) {
              for (var zz = 0; zz < this.clientes[i].palavras.length; zz++) {
                if (
                  this.current_text_list[o].alternatives == null ||
                  this.current_text_list[o].alternatives == undefined
                ) {
                  continue;
                }

                if (this.current_text_list[o].alternatives.length <= 0) {
                  continue;
                }
                this.current_text_list[
                  o
                ].alternatives[0].text = this.current_text_list[
                  o
                ].alternatives[0].text.replace(
                  this.clientes[i].palavras[zz].nome.toLowerCase() + "",
                  "<span class='palavra_destaque'>" +
                    this.clientes[i].palavras[zz].nome.toLowerCase() +
                    "</span> "
                );
              }
            } else {
              this.current_text_list[
                o
              ].alternatives[0].text = this.current_text_list[
                o
              ].alternatives[0].text.replace(
                this.clientes[i].nome.toLowerCase() + "",
                "<span class='palavra_destaque'>" +
                  this.clientes[i].nome.toLowerCase() +
                  "</span> "
              );
            }
          }
        }
      }
    },

    carregaForm(item) {
      var self = this;
    },

    exibe_error(tipo) {},

    getClassFirstSection() {
      if (this.id != "") return "col-xs-9";

      return "col-xs-12";
    },

    validar() {
      if (obj_alert.isvazioInput("f_data", "Informe o Data!")) return false;

      return true;
    },

    botao_voltar() {
      var self = this;

      if (
        self.form.bloqueado_por_id != null &&
        self.id_operador_atual.toString() ==
          self.form.bloqueado_por_id.toString()
      ) {
        this.acao_bloqueio(1, function() {
          //Vou dar um segundo pra ele não ficar tão empatado numa tela só...

          if (self.onBack != null && self.onBack != undefined) {
            console.log("clique no voltar!");
            self.onBack(self);
          }
        });
      } else {
        if (self.onBack != null && self.onBack != undefined) {
          console.log("clique no voltar!");
          self.onBack(self);
        }
      }
    },

    salvar(tipo) {},

    clear_message() {
      this.show_message = "off";
      clearInterval(this.interval_message);
    },

    excluir() {
      let self = this;
    }
  }
};
</script>
