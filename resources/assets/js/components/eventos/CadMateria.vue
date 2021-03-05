<template>
  <div>
    <div v-if="materia.id_materia_radiotv_jornal != null ">
      <materia_gerada :id="materia.id_materia_radiotv_jornal.toString()"></materia_gerada>
    </div>
    <div v-if="materia.id_materia_radiotv_jornal == null ">
      <div class="col-xs-12" v-if="ids_recortes != null">
        <div v-if="ids_recortes.length == 1">1 Arquivo selecionado</div>
        <div v-if="ids_recortes.length  > 1">{{ids_recortes.length}} Arquivos selecionados</div>
        <div v-if="ids_recortes.length == 0">Nenhum arquivo selecionado</div>
      </div>

      <div class="col-xs-6" v-if="ids_recortes.length > 0 ">
        <div class="form-group">
          <label>Hora Início:</label>
          {{materia.hora_inicio}}
        </div>
      </div>

      <div class="col-xs-6" v-if="ids_recortes.length > 0 ">
        <div class="form-group">
          <label>Duração:</label>
          {{materia.duracao}}
        </div>
      </div>

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

      <div class="col-xs-10">
        <div class="form-group">
          <label>Adicionar Cliente</label>
          <!-- :onChange="loadUnidade" -->
          <vue_select
            class="vue-select2"
            name="select2"
            :options="items_clientes"
            v-model="id_cliente_add"
            v-if="show_items_clientes"
            :searchable="true"
            language="pt-BR"
            :placeholder="placeholder_cliente"
          ></vue_select>
        </div>
      </div>
      <div class="col-xs-2" style="padding-top: 25px">
        <button
          type="button"
          v-on:click="adicionar_novo_cliente"
          class="btn btn-default pull-right"
        >
          <i class="fa fa-plus"></i>Adicionar
        </button>
      </div>

      <div class="col-xs-12">
        <table
          class="table table-striped table-bordered"
          v-if="materia != null && materia.clientes != null && show_clientes"
        >
          <thead>
            <tr>
              <th>ID</th>
              <th>Cliente</th>
              <th>Tópico</th>
              <th>Impacto</th>
              <th>Citação Direta</th>
              <th style="width: 30px"></th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item,index) in materia.clientes" :key="index">
              {{verifica_se_tem_index_cliente(item)}}
              <td>{{item.id}}</td>
              <td>{{item.nome}}</td>
              <td>
                <select
                  v-model="item.id_topico"
                  class="form-control"
                  :key="item.loaded_topico_index"
                  v-if="item.loaded_topicos"
                >
                  <option :value="null">--SELECIONE--</option>
                  <option
                    v-for="(item2, index2) in item.list_topicos"
                    :key="index2"
                    :value="item2.id"
                    :style="item2.pai == 1 ? 'background: #E1E1E1' : 'background: white; padding-left: 10px;'"
                  >{{item2.nome}}</option>
                </select>
                <span :key="item.loaded_topico_span" v-if="!item.loaded_topicos">Carregando...</span>
              </td>
              <td>
                <select v-model="item.id_impacto" class="form-control">
                  <option
                    v-for="(item2, index2) in list_impacto"
                    :key="index2"
                    :value="item2.id"
                  >{{item2.nome}}</option>
                </select>
              </td>
              <td>
                <select v-model="item.cita_diretamente" class="form-control">
                  <option :value="1">Sim</option>
                  <option :value="0">Não</option>
                </select>
              </td>
              <td>
                <button
                  type="button"
                  v-on:click="remover_cliente(item, index)"
                  class="btn btn-default pull-right"
                >
                  <i class="fa fa-remove"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="col-xs-12" v-if="ids_recortes!= null && ids_recortes.length > 0 ">
        <grid_arquivos :ids="ids_recortes.join(',')"></grid_arquivos>
      </div>

      <div
        class="col-xs-12"
        v-if="materia.id_materia_radiotv_jornal == null || materia.id_materia_radiotv_jornal == ''"
      >
        <button
          type="button"
          v-if="materia.id_rascunho != null"
          v-on:click="excluir_materia"
          :disabled="salvando_materia"
          class="btn btn-default pull-right"
          style="margin-left: 20px"
        >
          <i class="fa fa-trash"></i>
        </button>

        <button
          type="button"
          v-on:click="gera_materia"
          :disabled="salvando_materia"
          class="btn btn-danger pull-right"
        >
          <i class="fa fa-tv"></i>
          {{msg_salvando_materia}}
        </button>

        <button
          type="button"
          v-on:click="salva_materia_rascunho"
          :disabled="salvando_materia"
          class="btn btn-info pull-right"
        >
          <i class="fa fa-radio"></i>
          {{ !salvando_materia ? "Salvar Rascunho" : msg_salvando_materia }}
        </button>
      </div>
      <div
        class="col-xs-12"
        v-if="materia.id_materia_radiotv_jornal != null && materia.id_materia_radiotv_jornal.toString() != ''"
      >
        <envio_email :clientes="materia.clientes" :id_materia="materia.id_materia_radiotv_jornal"></envio_email>
      </div>
    </div>
  </div>
</template>
<script type="text/javascript">
import Util from "../../library/Util";
import obj_cliente from "../../library/obj_cliente";
import vue_select from "../../library/vue-select/src/Select";
import grid_arquivos from "../eventos_arquivos/grid_arquivos";
import materia_gerada from "../materia_rascunho/Gerada";
import envio_email from "./EnvioEmail";

export default {
  components: {
    vue_select,
    grid_arquivos,
    materia_gerada,
    envio_email
  },
  props: {
    form: {
      default: null,
      type: Object
    },

    ids_recortes: {
      default: [],
      type: Array
    },

    start_clientes: {
      default: [],
      type: Array
    },
    id_materia_rascunho: {
      default: null,
      type: Number
    },
    onSave: {
      default: null,
      type: Function
    },
    onRemove: {
      default: null,
      type: Function
    }
  },
  watch: {
    ids_recortes: function(val) {
      this.gera_duracao();
    }
  },
  data: function() {
    return {
      list_apresentador: [],
      clientes: [],

      loading2: false,
      show_items_clientes: true,

      items_clientes: [],

      placeholder_cliente: "Carregando clientes..",
      id_cliente_add: null,
      msg_salvando_materia: "Gerar Matéria",
      salvando_materia: false,

      list_impacto: [],
      modo_email: false,
      show_clientes: true,

      materia: {
        id_rascunho: null,
        id_apresentador: null,
        titulo: "",
        sinopse: "",
        id: null,
        hora_inicio: "",
        duracao: "",
        clientes: []
      }
    };
  },
  /* computed: {
        IdsRecortes: {
            get: function() {
                return this.ids_recortes;
            },
            set: function(value) {
                this.$emit('listchange', value)
            }
        }
    }, */
  mounted() {
    var self = this;

    this.list_impacto = [
      { id: 1201231, nome: "POSITIVO" },
      { id: 1201232, nome: "NEUTRO" },
      { id: 1201233, nome: "NEGATIVO" }
    ];

    obj_api.call("midiaclip_cadastros?acao=impacto", "GET", {}, function(
      retorno
    ) {
      console.log("list impacto");
      self.list_impacto = retorno.data;
      console.log(self.list_impacto);
    });

    if (self.form != null && self.form.id_programa != null) {
      obj_api.call(
        "midiaclip_cadastros?acao=apresentador&id_programa=" +
          self.form.id_programa,
        "GET",
        {},
        function(retorno) {
          console.log("list apresentador");
          console.log(self.list_apresentador);
          self.list_apresentador = retorno.data;

          var tmp_apresentador = obj_programa.get_last_apresentador(
            self.form.id_programa
          );

          console.log("tmp_apresentador? ", tmp_apresentador);
          console.log(
            "form_id_programa? ",
            window.sessionStorage.getItem("form_id_programa")
          );
          console.log(
            "form_id_apresentador? ",
            window.sessionStorage.getItem("form_id_apresentador")
          );

          if (tmp_apresentador != null && tmp_apresentador != undefined) {
            self.materia.id_apresentador = parseInt(tmp_apresentador);
          }
        }
      );
    }
    obj_cliente.getList(function(resultado) {
      self.items_clientes = Util.getListToSelect(resultado, "id", "nome");
      self.placeholder_cliente = "Digite para pesquisar";
    });

    if (this.id_materia_rascunho == null) {
      //Se o id da matéria já esta salvo, não precisamos adicionar os clientes.
      console.log("start clientes? ", this.start_clientes );

      if (this.start_clientes != null && this.start_clientes != undefined) {
        this.adicionar_clientes(this.start_clientes);
        this.reload_topicos_clientes();
      }
    }

    if (this.id_materia_rascunho == null) {
      this.gera_duracao();
    } else {
      //obj_json

      obj_api.call(
        "materia_rascunho/" + this.id_materia_rascunho,
        "GET",
        {},
        function(retorno2) {
          ///console.log("Consegui retornar o rascunho? ");
          //console.log(retorno2);
          self.materia = retorno2.item.obj_json;
          if (retorno2.item.ids_arquivos != null) {
            self.ids_recortes = retorno2.item.ids_arquivos.split(",");
            //self.$emit(
            //  "update-ids_recortes",
            //  retorno2.item.ids_arquivos.split(",")
            //);
          }
          self.reload_topicos_clientes();
        }
      );
    }
  },
  methods: {
    //update(ids_recortes) {
    //  this.ids_recortes = ids_recortes;
    //},
    gera_duracao() {
      var int_inicio = -1;
      var duracao_segundos = 0;

      if (this.form == null) {
        return;
      }

      if (this.id_materia_rascunho != null) {
        return;
      }

      if (this.form.arquivos_cortados.length <= 0) {
        this.materia.hora_inicio = "";
        this.materia.duracao = "";
        return;
      }

      var texto = "";

      for (var i = 0; i < this.form.arquivos_cortados.length; i++) {
        if (
          this.ids_recortes.indexOf(
            this.form.arquivos_cortados[i].id.toString()
          ) > -1
        ) {
          duracao_segundos +=
            this.form.arquivos_cortados[i].tempo_realizado_minutos * 60;

          if (texto != "") {
            texto += "\n";
          }
          texto += this.form.arquivos_cortados[i].texto;

          if (
            int_inicio == -1 ||
            this.form.arquivos_cortados[i].hora_inicio_seg < int_inicio
          ) {
            int_inicio = this.form.arquivos_cortados[i].hora_inicio_seg;
          }

          var meta_dados = this.form.arquivos_cortados[i].meta_dados;
          if (meta_dados != null) {
            var obj_meta_dados = JSON.parse(meta_dados);
            if (
              obj_meta_dados.clientes != null &&
              obj_meta_dados.clientes.length > 0
            ) {
              this.adicionar_clientes(obj_meta_dados.clientes);
              this.reload_topicos_clientes();
            }
          }
        }
      }

      this.materia.sinopse = texto;

      this.materia.hora_inicio = obj_corteaudiovideo.segundoParaTexto(
        int_inicio
      );

      this.materia.duracao = obj_corteaudiovideo.segundoParaTexto(
        duracao_segundos
      );
    },

    get_item_cliente(id) {
      for (var i = 0; i < this.items_clientes.length; i++) {
        if (this.items_clientes[i].value.toString() == id.toString()) {
          var retorno = this.items_clientes[i];

          return { id: retorno.value, nome: retorno.label }; // this.items_clientes[i];
        }
      }

      return null;
    },
    excluir_materia() {
      var self = this;

      var id_materia_rascunho = this.materia.id_rascunho;

      obj_alert.confirm(
        "Atenção",
        "Deseja realmente excluir este rascunho?",
        "question",
        function(result) {
          if (result.value) {
            obj_api.call(
              "materia_rascunho_del/" + id_materia_rascunho,
              "GET",
              {},
              function(resultado2) {
                if (self.onRemove != null) {
                  self.onRemove(resultado2);
                }
              }
            );
          }
        }
      );
    },
    adicionar_novo_cliente() {
      var self = this;

      if (this.id_cliente_add == null) {
        obj_alert.show("Atenção", "Selecione o cliente!", "warning");
        return;
      }

      var item = this.get_item_cliente(this.id_cliente_add);
      console.log("cliente para adicionar???");
      console.log(item);
      this.show_items_clientes = false;
      this.adicionar_cliente(item);
      this.reload_topicos_clientes();
      this.id_cliente_add = null;
      setTimeout(function() {
        self.show_items_clientes = true;
      }, 100);
    },

    reload_topicos_clientes() {
      console.log("reload_topicos_clientes? ");
      var self = this;
      var conta = 0;
      for (var i = 0; i < self.materia.clientes.length; i++) {
        if (
          self.materia.clientes[i].loaded_topicos == null ||
          self.materia.clientes[i].loaded_topicos == false
        ) {
          var id_cliente = self.materia.clientes[i].id;
          console.log("load_cliente_topico? " + id_cliente);
          self.load_cliente_topico(id_cliente, self, conta);
          conta++;
        }
      }
    },
    get_index_cliente(id_cliente) {
      for (var i = 0; i < this.materia.clientes.length; i++) {
        if (this.materia.clientes[i].id.toString() == id_cliente.toString()) {
          return i;
        }
      }

      return -1;
    },
    load_cliente_topico(id_cliente, self, conta) {
      setTimeout(function() {
        console.log("load_cliente_topico? " + id_cliente);
        obj_api.call(
          "midiaclip_cadastros?acao=topicos&id_cliente=" + id_cliente,
          "GET",
          {},
          function(response) {
            var index_cliente = self.get_index_cliente(id_cliente);
            console.log(
              "load_cliente_topico? index_cliente ? " + index_cliente
            );
            if (index_cliente < 0) {
              return;
            }
            var clientes = self.materia.clientes;
            var o_cliente = clientes[index_cliente];

            o_cliente.loaded_topicos = true;
            o_cliente.list_topicos = response.data;
            o_cliente.topico_required = response.required;

            o_cliente.loaded_topico_index =
              id_cliente + "_index_" + new Date().getTime(); //mudando o key força ele recarregar...
            o_cliente.loaded_topico_span =
              id_cliente + "_span_" + new Date().getTime();

            Vue.set(self.materia.clientes, parseInt(index_cliente), o_cliente);

            // self.materia.clientes = clientes;

            /*

            var filtered = self.materia.clientes.filter(function(
              item_componente
            ) {
              return item_componente.loaded_topicos;
            });
            if (filtered.length == self.materia.clientes.length) {
              //self.materia.clientes;
              //Vue.set(self.materia.clientes, parseInt(index_cliente), o_cliente);
              self.show_clientes = false;
              setTimeout(function() {
                self.show_clientes = true;
              }, 1000);
            }
            */
          }
        );
      }, 20 * conta + 50);
    },

    remover_cliente(item, index) {
      this.materia.clientes.splice(index, 1);
    },
    verifica_se_tem_index_cliente(item) {
      if (item.loaded_topico_index == null) {
        item.loaded_topico_index = item.id + "_index";
      }

      if (item.loaded_topico_span == null) {
        item.loaded_topico_span = item.id + "_span";
      }

      return "";
    },
    adicionar_clientes(itens) {
      for (var i = 0; i < itens.length; i++) {
        this.adicionar_cliente(itens[i]);
      }

      this.id_cliente_add = null;
    },
    adicionar_cliente(item) {
      if (item == null) return null;

      if (!this.tem_cliente(item.id)) {
        var cita_diretamente = 0;
        if (item.citacao_direta != null && item.citacao_direta == 1) {
          cita_diretamente = 1;
        }
        this.materia.clientes.push({
          id: item.id,
          nome: item.nome,
          id_impacto: 1201232,
          cita_diretamente: cita_diretamente,
          loaded_topicos: false,
          list_topicos: null,
          id_topico: null,
          loaded_topico_index: item.id + "_index",
          loaded_topico_span: item.id + "_span"
        });
      }
    },
    tem_cliente(id) {
      if (id == undefined) return false;

      for (var i = 0; i < this.materia.clientes.length; i++) {
        var item_cliente = this.materia.clientes[i];

        if (item_cliente.id.toString() == id.toString()) {
          return true;
        }
      }
      return false;
    },
    get_str_nome_clientes() {
      var str_clientes = "";

      for (var i = 0; i < this.materia.clientes.length; i++) {
        if (str_clientes != "") {
          str_clientes += ", ";
        }
        str_clientes += this.materia.clientes[i].nome;
      }

      return str_clientes;
    },
    salva_materia_rascunho() {
      var self = this;

      self.salvando_materia = true;
      self.msg_salvando_materia = "Salvando...";

      var str_clientes = this.get_str_nome_clientes();

      var data = {
        id: this.materia.id_rascunho,
        dados_materia: JSON.stringify(this.materia),
        cliente_list: str_clientes,
        id_programa: this.form.id_programa,
        id_projeto: this.form.id,
        dia: this.form.dia,
        id_operador: $("#id_operador").val(),
        titulo: this.materia.titulo,
        ids_arquivos: this.ids_recortes.join(","),
        clientes: JSON.stringify(self.materia.clientes)
      };

      obj_programa.set_last_apresentador(
        this.form.id_programa,
        this.materia.id_apresentador
      );

      obj_api.call("materia_rascunho", "post", data, function(response) {
        self.materia.id_rascunho = response.data.id;
        self.salvando_materia = false;
        self.msg_salvando_materia = "Gerar Matéria";
        if (self.onSave != null) {
          self.onSave(self.materia);
        }
        obj_alert.show(
          "Sucesso!",
          "Rascunho salvo com sucesso!",
          "success",
          null,
          1500
        );
      });
    },
    gera_materia() {
      var self = this;

      if (this.materia.titulo == "") {
        obj_alert.show("Atenção", "Informe o título", "warning");
        return false;
      }

      if (this.ids_recortes.length <= 0) {
        obj_alert.show("Atenção", "Selecione um ou mais arquivos", "warning");
        return false;
      }

      if (this.materia.clientes.length > 0) {
        var str_obrigatorio_topico = "";
        console.log("materia clientes?" + this.materia.clientes.length );

        for (var i = 0; i < this.materia.clientes.length; i++) {
          var o_cliente = this.materia.clientes[i];
           console.log( o_cliente );
          if (o_cliente.topico_required != undefined && o_cliente.topico_required != null && o_cliente.topico_required.toString() == "1" 
                           && o_cliente.list_topicos != null &&  o_cliente.list_topicos.length > 0) {
           
           console.log( "topico required?", o_cliente.id_topico );
           if ( o_cliente.id_topico == undefined || o_cliente.id_topico == null || o_cliente.id_topico.toString() == "") {
              if ( str_obrigatorio_topico != ""){
                str_obrigatorio_topico += ", " + o_cliente.nome;
              }else{
                 str_obrigatorio_topico +=  o_cliente.nome;
              }
            }
          }
        }

        if ( str_obrigatorio_topico != ""){
             obj_alert.show("Atenção", "O tópico é obrigatório para o(s) cliente(s): " + str_obrigatorio_topico, "warning");
             return false;
        }
      }
    // alert("faz um teste aqui!");
      //return false;

      var new_id = "";

      self.salvando_materia = true;
      self.msg_salvando_materia = "Salvando...";

      var str_clientes = this.get_str_nome_clientes();

      //

      var compl_api4 = "";

      if (this.materia.clientes != null && this.materia.clientes.length > 0) {
        compl_api4 = "&clientes=" + this.materia.clientes.length.toString();
      }

      obj_programa.set_last_apresentador(
        this.form.id_programa,
        this.materia.id_apresentador
      );

      obj_api.call_midiaclip("midiaclip", "?acao=path_rtv", "GET", function(
        retorno3
      ) {
        obj_api.call_midiaclip(
          "api4",
          "materiartv/getnewid?arquivos=1" + compl_api4,
          "GET",
          function(retorno) {
            retorno.path = retorno3.Codigo;

            var data = {
              id_materia: retorno.id,
              id_materia_frags: JSON.stringify(retorno),
              //id: self.current_video.id,
              json_data: JSON.stringify(self.materia),
              clientes: JSON.stringify(self.materia.clientes),
              ids_arquivos: self.ids_recortes.join(","),

              id_programa: self.form.id_programa,
              id_projeto: self.form.id,

              id_rascunho: self.materia.id_rascunho,

              cliente_list: str_clientes,
              dia: self.form.dia,

              id_operador: $("#id_operador").val(),
              titulo: self.materia.titulo
            };
            //  console.log("Retorno do newID? " + "materiartv/getnewid?arquivos=1"+compl_api4 );
            //  console.log( JSON.stringify( data ) ); return false;

            obj_api.call("materia_new", "POST", data, function(retorno2) {
              self.materia.id_rascunho = retorno2.data.id_rascunho;
              self.materia.id_materia_radiotv_jornal =
                retorno2.data.id_materia_radiotv_jornal;

              if (self.onSave != null) {
                self.onSave(self.materia);
              }

              obj_api.call(
                "materia_gerada/" + self.materia.id_materia_radiotv_jornal,
                "get",
                {},
                function(retorno3) {
                  self.materia.clientes = retorno3.clientes;
                }
              );
              //self.materia.id = retorno2.id;
              /* self.current_video.data_insert_materia =
                retorno2.data_insert_materia;
              self.current_video.sinopse_materia = retorno2.sinopse_materia;
              self.current_video.titulo_materia = retorno2.titulo_materia;
              self.current_video.jornalista = retorno2.jornalista;
              self.current_video.id_materia_radiotv_jornal =
                retorno2.id_materia_radiotv_jornal;

              Vue.set(
                self.form.arquivos_cortados,
                self.current_video_index,
                retorno2
              );

              console.log("Retorno de salvar matéria? ");
              console.log(retorno2);
            

              self.materia.id_apresentador = null;
              self.materia.titulo = "";
              self.materia.sinopse = ""; 
              
              */

              self.salvando_materia = false;
              self.msg_salvando_materia = "Gerar Matéria";

              obj_alert.show(
                "Sucesso!",
                "Matéria criada com sucesso!",
                "success"
              );
            });
          }
        );
      });
    }
  }
};
</script>