<template>
  <div>


  	<div class="col-xs-12" style="margin-top: 10px">

<div v-if="loading">
   ..Carregando
</div>


<section class="col-xs-12"   >
        <a href="#"  v-on:click="botao_voltar" class="pull-right btn btn-sm btn-default" v-if="botao_voltar_visible">
           <i class="fa fa-arrow-left"></i> {{msg_botao_voltar}}
        </a> 
</section>

<div class="col-xs-5">

    <div class="box box-primary">
<div class="box-body">
<table class="table table-striped table-bordered">
    <thead>
           <tr>
                <th colspan="2">
                       Arquivo principal
                </th>    

           </tr>

    </thead>
        
        <tbody v-if="form != null && form.arquivos != null ">
            <tr v-for="(item,index) in form.arquivos" :key="index">
                  <td>{{item.nome}}</td>
                
                <td style="width: 30px"><a style="cursor:pointer"
                    v-on:click="openVideo(item, index)">
                 <span class="glyphicon glyphicon-play-circle"></span></a></td>
                </tr>

                <tfoot v-if="form != null && form.arquivos != null && form.arquivos.length > 0 ">
        
        </tfoot>

    </tbody>
</table>
<div v-if="current_video != null" >
 <video id="video_main" v-if="show_video"

 v-bind:src="current_video.url_load" width="99%" height="330" preload="auto" 
  v-on:timeupdate="setCurrentTime" 
  controls="controls" ></video>
  <div><b>Vídeo em reprodução: </b> {{current_video.nome}} </div>
  </div>




 <table class="table" v-if="current_video != null && current_video.tipo == 'join' " >
      <tr>
          <td>
               <label>Tempo Atual:</label>
               <input type="text" name="tx_currentTime" id="tx_currentTime" class="t_readonly"
                readonly="readonly" style="width: 70px" :value="duracao_atual" />

  <input type="hidden" id="txtDuracao" v-bind:value="current_video.duracao" >
          </td>
          <td>
              <input type="button" name="bt_catch_start" onclick="obj_corteaudiovideo.catchTime('start')" id="bt_catch_start" value="Capturar Início" 
                 data-toggle="tooltip"
                 title="Pega o tempo atual e indica como o início do corte"  
                 class="btn btn-default btn-xs" style="width: 120px"/>
                 <br />
                 <input type="button" name="bt_catch_end" id="bt_catch_end" onclick="obj_corteaudiovideo.catchTime('end')" value="Capturar Fim"
                 data-toggle="tooltip"
                  title="Pega o tempo atual e indica como o fim do corte" 
                 class="btn btn-default btn-xs" style="width: 120px"/>
          </td>

          <td>

           <button type="button" class="btn btn-default btn-xs" title="Pausa o vídeo ou áudio" data-toggle="tooltip" id="btPausar"  onclick="obj_corteaudiovideo.pauseVideo()"  >
      <span class='glyphicon glyphicon-pause'></span> Pausa</button>
          </td>
          <td>

     <label>Velocidade:</label><br />
       <select name="video_velocidade" id="video_velocidade" onchange="obj_corteaudiovideo.setaVelocidade(this.value)">
                 <option value="1">Normal</option>
                 <option value="1.25">1,25</option>
                 <option value="1.5">1,5</option>
                 <option value="2">2</option>
       </select>
          </td>

      </tr>


 </table>



 <table class="table" v-if="current_video != null && current_video.tipo == 'join'">
      <tr>
           <td style="width: 40px">Início:</td>
           <td>
                    <input type="range" name="rg_start" id="rg_start" min="0" v-bind:max="max_video_time" style="width: 99%" onchange="obj_corteaudiovideo.sendTime(this)"> 
                </td>
                <td style="width: 75px">

                    <input type="text" id="sp_start" name="sp_start" style="width: 70px" onchange="obj_corteaudiovideo.changeTextTime(this)" />
                </td>

      </tr>
      <tr>
              <td style="width: 40px">Fim:</td>
          
            <td>
                <input type="range" name="rg_end" id="rg_end" min="0" v-bind:max="max_video_time" style="width: 99%"   onchange="obj_corteaudiovideo.sendTime(this)"> 

            </td>
                <td style="width: 75px">
                <input type="text" id="sp_end" name="sp_end" style="width: 70px" onchange="obj_corteaudiovideo.changeTextTime(this)"  />
                    </td>
      </tr>
      <tr>
                    <td colspan="3">

                            <div class="col-xs-12 input-group"  style="padding-top: 10px">
                                            <input
                                            type="text"
                                            placeholder="Nome do recorte"
                                            id="nome_projeto"
                                            v-model="nome_projeto"
                                            class="form-control"
                                            maxlength="100"
                                            />
                                            <span class="input-group-btn">
                                                    <button type="button" v-on:click="gera_corte" class="btn btn-info btn-sm pull-right" v-on:disabled="!enableCorte"  >
                                                                <i class="fa fa-cut"></i> Gerar Recorte
                                                                </button> 
                                            </span>
                      </div>
                    </td>
      </tr>

 </table>

   </div>
    </div>


    <div class="box box-info"  >
<div class="box-body">
<table v-if="form != null && form.arquivos_cortados != undefined  && form.arquivos_cortados != null " class="table table-striped table-bordered">
      <thead>
           <tr>
                <th colspan="3">
                      Recortes
                </th>    

           </tr>

    </thead>
        <tbody >
            <tr v-for="(item,index) in form.arquivos_cortados" :key="index" 
                 v-bind:class="current_video.id == item.id ? 'bg-light-blue-active': ''">
                  <td>{{item.titulo}}</td>
                  <td>{{item.nome}}</td>
                  <td>{{item.duracao}}</td>
                
                <td style="width: 30px"><a style="cursor:pointer"
                    v-on:click="openVideo(item, index)">
                    <span
                     v-bind:style="current_video.id == item.id ? 'color: white': '' "
                     class="glyphicon glyphicon-play-circle"></span></a></td>
                </tr>

                <tfoot v-if="form != null && form.arquivos != null && form.arquivos.length > 0 ">
        
        </tfoot>

    </tbody>
</table>

</div>
</div>

	

</div>

  <div class="col-xs-7">
      
    <div class="box box-primary">
            <div class="box-body">
                <div class="box-header with-border">
                <h3 class="box-title">Texto transcrito</h3>
                </div>
                <div id="divTexto" class="col-xs-12" v-if="current_video != null && current_text_list != null " style="max-height: 500px; overflow-y: scroll;">
                        
                        <span v-for="(item,index) in current_text_list" :key="index" v-bind:style="getStyle(item)" >
                                    <a href="#!" v-if="item.alternatives[0] != undefined " class="link_video_legenda" v-on:click="click_time(item)" v-html="item.alternatives[0].text + ' '"></a>
                        </span>

                </div>
    </div>
    </div>

<div class="box box-default" v-if="current_video != null && current_video.tipo == 'cut' ">
     <div class="box-body">
          <div class="box-header with-border">
                <h3 class="box-title">Dados do programa</h3>
                </div>
                    <div class="col-xs-12" >
                            <div class="form-group">
                        <b>Hora Início: </b> {{current_video.hora_inicio}}
                        &nbsp;&nbsp; <b>Duração: </b> {{current_video.duracao}}
                        &nbsp;&nbsp; <b>Veículo: </b> {{form.meta.emissora}}
                        &nbsp;&nbsp; <b>Programa: </b> {{form.meta.programa}}
                    </div>

                    </div>
                    
                    <div  v-if="current_video.id_materia_radiotv_jornal != null" >
                        
                    <div class="col-xs-12" >
                            <div class="form-group">
                                 <label style="color: red">Matéria Gerada = </label>
                                        <b>ID: </b> {{current_video.id_materia_radiotv_jornal}}
                                        &nbsp;&nbsp; <b>Título: </b> {{current_video.titulo_materia}}
                                        &nbsp;&nbsp; <b>Apresentador: </b> {{current_video.jornalista}}
                                        &nbsp;&nbsp; <b>Dt Cadastro: </b> {{current_video.data_insert_materia | datetime_show}}

                           </div>

                    </div>

                    </div>
                    
                    <div  v-if="current_video.id_materia_radiotv_jornal == null" >
                        <div class="col-xs-6">
                            <div class="form-group">
                         <label>Título</label>
                         <input type="text" name="materia_titulo" id="materia_titulo"   class="form-control" v-model="materia.titulo" >
                      
                          </div>

                      </div>
                    <div class="col-xs-6">
                            <div class="form-group">
                         <label>Apresentador</label>
                         <select name="materia_id_apresentador" id="materia_id_apresentador" class="form-control" v-model="materia.id_apresentador">
                              <option v-for="(item,index) in list_apresentador" :key="index" :value="item.id">{{item.nome}}</option>

                         </select>
                    </div>

                    </div>

                         <div class="col-xs-12">
                            <div class="form-group">
                                    <label>Sinpose</label>
                                    <textarea v-model="materia.sinopse"  class="form-control">
                                    </textarea>
                                </div>

                         </div>
                         
                         <div class="col-xs-12">
                              <button type="button" v-on:click="gera_materia" :disabled="salvando_materia" class="btn btn-danger pull-right"   >
                                            <i class="fa fa-tv"></i> {{msg_salvando_materia}} 
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

 <!--

 onloadedmetadata="obj_corteaudiovideo.loadControls(this)" onplay="obj_corteaudiovideo.setPlayVideo(this)" onpause="obj_corteaudiovideo.onpause(this)"

-->

  </div>
  </div>
</template>
<style>
a.link_video_legenda{
	color: #333;
}
a.link_video_legenda:hover{
	background-color: #c3dcde;
}
</style>

<script>
import grid_cliente from "./GridCliente.vue";

 export default {
  components: {
    grid_cliente
  },
      props: ['id_load', 'id_pai', 'post_type', 'show_back_button','onBack', 'onSave', 'id_load_arquivo', 'tempo_seg'],
       data: function() {
            return {
            	
				form: null,
				current_time: -1,
                duracao_atual: "00:00:00",

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
                max_video_time: 100,
                current_video_index: -1,

                arquivos_ids: [],
                enableCorte: true,

                salvando_materia: false,
                msg_salvando_materia: "Gerar Matéria",

                materia: {

                        id_apresentador: null,
                        titulo: "",
                        sinopse: "",
                        id: null
                },
                list_apresentador: [],
                clientes: [],
                show_video: false,
                msg_botao_voltar: "Voltar para o programa",

                nome_projeto: "",




            }
        },
        mounted() {

                let self = this;
   
                            if ( this.show_back_button != null && this.show_back_button != undefined  ){
                                     this.botao_voltar_visible = this.show_back_button;
                            }


                          if ( this.id_load == null || this.id_load == ""){
                            return;
                          }


                          var url =  "eventos/" + this.id_load; console.log("monted post url: " + url );
                          var method = "get";

                          this.disableButton = true;
                           self.loading = true;

                          var data = { }


			              var fn_return = function (retorno){

                                            console.log("Retorno? ");
                                            console.log( retorno );
                                            
			              	     self.form = retorno.item;
                                 self.disableButton = false;
                                 self.loading = false;

                                 if (self.id_load_arquivo != null && self.id_load_arquivo != undefined && self.id_load_arquivo != ""){

                                     for ( var o = 0; o < self.form.arquivos_cortados.length ; o++ ){
                                         if ( self.form.arquivos_cortados[o].id.toString() ==  self.id_load_arquivo.toString()  ){

                                                self.openVideo( self.form.arquivos_cortados[o] ,  o );

                                         }
                                     }

                                     self.msg_botao_voltar = "Voltar para a lista";

                                 }else{

                                       if ( self.form.arquivos.length == 1 ){
                                            self.openVideo( self.form.arquivos[0], 0 );
                                        }

                                 }
                                 

                                 if ( self.form.id_programa != ""){
                                     obj_api.call("midiaclip_cadastros?acao=apresentador&id_programa="+self.form.id_programa,"GET", {},
                                       function (retorno){
                                           self.list_apresentador = retorno.data;
                                       });
                                 }

			              }

                          obj_api.call(url, method, data , fn_return);
                          

                          $(".content-wrapper").css({"max-height": "auto", "height": "1100px"});



         },
         computed: {
           
         },
         methods:{


             gera_materia(){

                 var self = this;

                 if ( this.materia.titulo == ""){
                     obj_alert.show("Atenção","Informe o título","warning");
                     return false;
                 }


                 var new_id = "";

                 self.salvando_materia = false;
                 self.msg_salvando_materia = "Salvando...";

//

          var compl_api4 ="";

          if ( this.clientes != null && this.clientes.length > 0 ){
              compl_api4 = "&clientes=" +this.clientes.length.toString();
          }

           obj_api.call_midiaclip("midiaclip","?acao=path_rtv","GET", function(retorno3){

                 obj_api.call_midiaclip("api4","materiartv/getnewid?arquivos=1"+compl_api4,"GET", function(retorno){

                     retorno.path = retorno3.Codigo;
                          
                           var data = {
                                       id_materia: retorno.id,
                                       id_materia_frags:  JSON.stringify(retorno),
                                       id: self.current_video.id,
                                       json_data: JSON.stringify(  self.materia ),
                                       clientes: JSON.stringify( self.clientes )
                           }     
                         //  console.log("Retorno do newID? " + "materiartv/getnewid?arquivos=1"+compl_api4 );
                         //  console.log( JSON.stringify( data ) ); return false;

                           obj_api.call("materia_new","POST",data, function (retorno2){
                               //self.materia.id = retorno2.id;
                               self.current_video.data_insert_materia = retorno2.data_insert_materia;
                               self.current_video.sinopse_materia = retorno2.sinopse_materia;
                               self.current_video.titulo_materia = retorno2.titulo_materia;
                               self.current_video.jornalista = retorno2.jornalista;
                               self.current_video.id_materia_radiotv_jornal = retorno2.id_materia_radiotv_jornal;
                        
                              Vue.set(self.form.arquivos_cortados, self.current_video_index, retorno2);

                              
             

                               console.log("Retorno de salvar matéria? ");
                               console.log(retorno2);
                               obj_alert.show("Sucesso!","Matéria criada com sucesso!", "success");

                               
                                 self.materia.id_apresentador = null;
                                 self.materia.titulo = "";
                                 self.materia.sinopse = "";

                           });

                           self.salvando_materia = false;
                           self.msg_salvando_materia = "Gerar Matéria";
                           
                           });



           });
    

             },

             gera_corte(){
                 var self = this;
                 this.enableCorte = false;

           

                            if ( document.getElementById("rg_start") == null ){
                                // alert("Não localizei o campo de tempo início");
                                 obj_alert.show("Atenção", "Campo de tempo início não localizado!", "warning");
                                 false;
                            }

                            if ( this.nome_projeto == ""){
                                 obj_alert.show("Atenção", "Informe o nome do recorte", "warning");
                                 false;
                            }

                            // console.log("Gera_corte: "); console.log(data); return false;

                  var data = {id_evento: this.id_load,
                              inicio:  $("#rg_start").val(),
                              fim: $("#rg_end").val(),
                              titulo: this.nome_projeto
                             };
                             
                 var fn_return = function (retorno){

                             self.form.arquivos_cortados.push(retorno.data);   
                             self.enableCorte = true;  
                             self.nome_projeto  = "";        
                             console.log("Retorno do corte!");
                             console.log(retorno);                             

			     }

			    obj_api.call("project_corte","POST", data, fn_return );

             },


         	novoProjeto(){

         		var arquivos = [];

         		for ( var i = 0; i < this.form.arquivos.length; i++ ){
         			var item = this.form.arquivos[i];

         			if ( this.arquivos_ids.indexOf(item.id) > -1 ){
         				arquivos[arquivos.length] = {id: item.id, nome: item.nome};
         			}
         		}

         		var data = {arquivos: JSON.stringify(arquivos), id_operador: $("#id_operador").val() };

         		// console.log(data); return false;

                 var fn_return = function (retorno){

                                            console.log("Retorno? ");
                                            console.log( retorno );
                                            

			    }

			    obj_api.call("project/" +this.form.id,"POST", data, fn_return );

         		//obj_api.callFormData("project/" +this.form.id,"POST", data, fn_return );




         	},



         	add_video(id, index){

         		

         		var check = document.getElementById("chk_" + id);

                if (! check.checked ){
                	var inde = this.arquivos_ids.indexOf(id);
                	if ( inde > -1 ){
                		this.arquivos_ids.splice(inde, 1 );
                	}
                }else {

                    var inde = this.arquivos_ids.indexOf(id);
                	if ( inde < 0  ){
                		this.arquivos_ids.push(id);
                	}
                }

                console.log("add? "); console.log(id); console.log(this.arquivos_ids); console.log(check.checked);

         	},

         	getStyle (item){

         		if ( item.start_time < this.current_time && item.end_time >= this.current_time){
         			return "background-color: #CCCCCC";
         		}



         		return "";
         	},

         	click_time(item){

         		  if ( this.obj_video == null ){
                	this.obj_video =document.getElementById("video_main");
                }

                    this.obj_video.currentTime = item.start_time;
                     this.obj_video.play();
         	},

         	setCurrentTime( ){

                if ( this.obj_video == null ){
                	this.obj_video =document.getElementById("video_main");
                }

         		this.current_time = this.obj_video.currentTime;
                this.duracao_atual = this.duracaoAtual();
         	},

            
               duracaoAtual(){

                var segundos = this.current_time;

                if ( segundos == null )
                    segundos = 0;

                   var horas = parseInt( parseInt(segundos) / 3600 );
                   var minutos = parseInt(  (parseInt(segundos)  % 3600) / 60 ); 
                    var seg = parseInt(  (parseInt(segundos)  % 3600) % 60 );
                  
                   var tempo = obj_corteaudiovideo.sTempo( horas ) + ":" + obj_corteaudiovideo.sTempo( minutos ) + ":" + obj_corteaudiovideo.sTempo( seg);

                    return tempo;

              },

         	openVideo(item, index){
                var self = this;

                this.show_video = false;

                if ( self.obj_video != null ){
                    self.obj_video.pause();
                }

         		this.current_text_list = null;

                if (  item.duracao  != undefined && item.duracao  != null ){
                    this.max_video_time = obj_corteaudiovideo.ConverteTextoParaSegundos(  item.duracao );

                } 
                 this.current_video = item;
                 this.current_video_index = index;
                 this.current_text_list = JSON.parse( item.json );
                 
                 this.clientes = [];

                if (item.meta_dados != null && item.meta_dados != undefined) {
                    var obj = JSON.parse(item.meta_dados);

                    if (obj.clientes != null) {
                    this.clientes = obj.clientes;
                    }
                }

               
                setTimeout( function() { 

                            self.show_video = true;
                            self.obj_video =document.getElementById("video_main");

                            if ( self.obj_video != null ){
                                 self.obj_video.play();

                            } }, 1300) ;

         	},
		 
		 
            carregaForm(item){
                     var self = this;
			   
		    },
		 
		 
            exibe_error( tipo ){
                    
            },

         	getClassFirstSection(){

         		if ( this.id != "")
         			return "col-xs-9";

         		return "col-xs-12";
         	},

         	validar(){
			
			             if ( obj_alert.isvazioInput("f_data","Informe o Data!"))
                       	         return false;   

             
                    	return true;
         	},


        	botao_voltar(){
        		var self = this;

                 if ( this.onBack != null && this.onBack != undefined ){
                 	 //console.log("clique no voltar!");
                 	 this.onBack( self.form );
                 }
        	},

        	salvar (tipo ){
        	},

        	clear_message(){
                 this.show_message = "off";
                 clearInterval(this.interval_message);
        	},

        	excluir(){
        		let self = this;
        	}
         }

    }


</script>
