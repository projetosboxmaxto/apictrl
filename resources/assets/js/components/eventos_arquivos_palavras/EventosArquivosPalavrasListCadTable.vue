<template>
  <div>
<!--

import EventosArquivosPalavrasListCadTable from './components/eventos_arquivos_palavras/EventosArquivosPalavrasListCadTable'
import EventosArquivosPalavrasListConsTable from './components/eventos_arquivos_palavras/EventosArquivosPalavrasListConsTable'


Vue.component('eventos_arquivos_palavras_list_cad_table', EventosArquivosPalavrasListCadTable);
Vue.component('eventos_arquivos_palavras_list_cons_table', EventosArquivosPalavrasListConsTable );


-->
         <table class="table table-bordered table-striped">
             <thead>
             	<tr>
                     
                           <th>id</th>  
      <th>data</th>  
      <th>id_evento</th>  
      <th>id_evento_arquivo</th>  
      <th>id_cliente</th>  
      <th>cita_diretamente</th>  
      <th>palavra</th>  
      <th>tempo</th>  
      <th>tempo_seg</th>  
      <th>id_dicionario_tag</th>  
      <th>status</th>  
      <th>operador</th>  
      <th>id_operador</th>  
      <th>id_materia_radiotv_jornal</th>  
                     <th>
                       <button class="btn btn-default" v-on:click="add" type="button"
                       ><i class="fa fa-plus"></i></button>

                     </th>
             	</tr>
             </thead>
             <tbody>
                 <tr v-for="(item, index) in items">
				   <td>  
								
									 <input type="text" v-bind:value="item.data"
                                            v-on:change="change_value"
                                            maxlength="10"
                                            class="form-control"
                                            onkeypress="return obj_mask.MascaraData(event, this)"
                                            v-bind:id="getIDItem('data', index)"
                                            onfocus="obj_editor.setcalendar(this, event)"
                                              
                                            style="width: 100px">
									
			 </td> 
 <td>  
		                         <input type="text" v-model="item.id_evento"
                                            v-on:change="change_value"
                                            maxlength="10"
                                            onkeypress="return obj_mask.mascaraInteiro(this, event)"
                                            class="form-control"
                                            v-bind:id="getIDItem('id_evento', index)"
                                            style="width: 100px">
		  
									
									
									
			 </td> 
 <td>  
		                         <input type="text" v-model="item.id_evento_arquivo"
                                            v-on:change="change_value"
                                            maxlength="10"
                                            onkeypress="return obj_mask.mascaraInteiro(this, event)"
                                            class="form-control"
                                            v-bind:id="getIDItem('id_evento_arquivo', index)"
                                            style="width: 100px">
		  
									
									
									
			 </td> 
 <td>  
		                         <input type="text" v-model="item.id_cliente"
                                            v-on:change="change_value"
                                            maxlength="10"
                                            onkeypress="return obj_mask.mascaraInteiro(this, event)"
                                            class="form-control"
                                            v-bind:id="getIDItem('id_cliente', index)"
                                            style="width: 100px">
		  
									
									
									
			 </td> 
 <td>  
		                         <input type="text" v-model="item.cita_diretamente"
                                            v-on:change="change_value"
                                            maxlength="10"
                                            onkeypress="return obj_mask.mascaraInteiro(this, event)"
                                            class="form-control"
                                            v-bind:id="getIDItem('cita_diretamente', index)"
                                            style="width: 100px">
		  
									
									
									
			 </td> 
 <td>  <input type="text" 
                                            v-model="item.palavra"
                                            v-on:change="change_value"
                                            maxlength="300"
                                            class="form-control"
                                            v-bind:id="getIDItem('palavra', index)"
                                            style="width: 90px">
                  
    </td> 
 <td>  <input type="text" 
                                            v-model="item.tempo"
                                            v-on:change="change_value"
                                            maxlength="30"
                                            class="form-control"
                                            v-bind:id="getIDItem('tempo', index)"
                                            style="width: 90px">
                  
    </td> 
 <td>  
		                         <input type="text" v-model="item.tempo_seg"
                                            v-on:change="change_value"
                                            maxlength="10"
                                            onkeypress="return obj_mask.mascaraInteiro(this, event)"
                                            class="form-control"
                                            v-bind:id="getIDItem('tempo_seg', index)"
                                            style="width: 100px">
		  
									
									
									
			 </td> 
 <td>  
		                         <input type="text" v-model="item.id_dicionario_tag"
                                            v-on:change="change_value"
                                            maxlength="10"
                                            onkeypress="return obj_mask.mascaraInteiro(this, event)"
                                            class="form-control"
                                            v-bind:id="getIDItem('id_dicionario_tag', index)"
                                            style="width: 100px">
		  
									
									
									
			 </td> 
 <td>  
		                         <input type="text" v-model="item.status"
                                            v-on:change="change_value"
                                            maxlength="10"
                                            onkeypress="return obj_mask.mascaraInteiro(this, event)"
                                            class="form-control"
                                            v-bind:id="getIDItem('status', index)"
                                            style="width: 100px">
		  
									
									
									
			 </td> 
 <td>  <input type="text" 
                                            v-model="item.operador"
                                            v-on:change="change_value"
                                            maxlength="300"
                                            class="form-control"
                                            v-bind:id="getIDItem('operador', index)"
                                            style="width: 90px">
                  
    </td> 
 <td>  
				                   <input type="text" 
                                            v-model="item.id_operador"
                                            v-on:change="change_value"
                                            class="form-control"
                                            v-bind:id="getIDItem('id_operador', index)"
                                            style="width: 90px">
                 
				 
			
    </td> 
 <td>  
				                   <input type="text" 
                                            v-model="item.id_materia_radiotv_jornal"
                                            v-on:change="change_value"
                                            class="form-control"
                                            v-bind:id="getIDItem('id_materia_radiotv_jornal', index)"
                                            style="width: 90px">
                 
				 
			
    </td> 
                    
                     <td>
                       <button class="btn btn-default" type="button" 
                          v-on:click="excluir(item, index)">
                          <i class="fa fa-trash"></i>
                       </button>
                     </td>

                 </tr>
             </tbody>
             <tfoot >
              <tr v-if="items.length <= 0">
                <td colspan="11">
                  <i>Sem registros</i></td>
              </tr>
               <tr >
                <td colspan="11">
                 
                     <button class="btn btn-primary pull-right"
                             type="button"
                             v-on:click="salvar">Salvar</button>
                 </td>
              </tr>
             </tfoot>

         </table>


  </div>



</template>

<script>
    export default {
        props: ['equipamento_id', 'projeto_id'],
        data: function() {
            return {
                
              items: [],
			  
			  
			  
              ids_excluir: [],

              action: "list",
              id: "",
              table: null,
              filtro_titulo: "",
              filtro_status: "",

              show_new_button: true,

              button_new_text: "" //<i class=\"fa fa-file\" ></i> NOVA POST"
            }
        },
        methods: {
            
            getIDItem(nome, index){
                return "eventos_arquivos_palavras_"+nome+"_L" +index.toString();
            },
            change_data_item(id_campo, value){

                   if ( id_campo.indexOf("data") > -1 ){

                        var ar = id_campo.split("_L");
                        var index = ar[1];
                        var item = this.items[ parseInt(index) ];
                        
                        var arcampo = id_campo.split("eventos_arquivos_palavras_");
                        var arcampo2 = arcampo[1].split("_L");
                        var nome_campo = arcampo2[0];

                        console.log("Localizado nome do campo " + nome_campo + " e data"+ value);
                        
                        item[nome_campo] = value;
                        Vue.set(this.items, parseInt(index), item);
                        console.log("Consegui alterar para o indice : " + index );
                    }



            },
            change_value(event){

              this.change_data_item( event.target.id, event.target.value );

        
            },
            excluir(item, index){

              console.log("Excluir? ");

                if (item.id != null &&  item.id != ""){

                     this.ids_excluir.unshift(  item.id );
                }

                this.items.splice(index, 1);
            },
            datepicker_return(dateText, inst){
                         console.log("Cheguei aqui? ");
            },
            salvar(){
                      var self = this;
                      var hd_json = JSON.stringify(this.items);
                      var ids_delete_json = JSON.stringify(this.ids_excluir);


                      var url =  "eventos_arquivos_palavras/salvargrid"; 
                      var method = "POST";

                      var data = {
                             hd_json: hd_json,
                             ids_delete_json: ids_delete_json,

                            // equipamento_id: this.equipamento_id,
                            // projeto_id: this.projeto_id
                      };
           
                      var fn_return = function (retorno){

                                     self.items = retorno.data;

                                     if (retorno.success){
                                        obj_alert.show("Sucesso!",
                                           "eventos_arquivos_palavras salvo com sucesso",
                                           "success", null, 3000);
                                     }    
                      }

                      obj_api.call(url, method, data , fn_return);
            },
            add(){
                  this.items.push( this.getArrayItem() );
            },
            getArrayItem(){
                      var data = {
                        id: "",
						              id: "",  
              data: "",  
              id_evento: "",  
              id_evento_arquivo: "",  
              id_cliente: "",  
              cita_diretamente: "",  
              palavra: "",  
              tempo: "",  
              tempo_seg: "",  
              id_dicionario_tag: "",  
              status: "",  
              operador: "",  
              id_operador: "",  
              id_materia_radiotv_jornal: "",  
                      };

                       // if ( this.equipamento_id != null ){
                       //        data["equipamento_id"] = this.equipamento_id;

                       // } 
                       // if ( this.projeto_id != null ){
                       //        data["projeto_id"] = this.projeto_id;

                       // } 


                      return data;
            }
            
        },
        mounted() {

                let self = this;

                obj_editor.onSelectEvent = function (dateText, inst){
                     console.log("DateText a partir do objeto VUE");
                     console.log(inst);
                     var id_input = inst.input[0].id;

                    self.change_data_item( id_input, dateText );
                }

                var url =  "eventos_arquivos_palavras/gridcad"; // +
                // this.id_load; console.log("monted post url: " + url );
                var method = "POST";
                this.disableButton = true;

                var data = { }

                //if ( this.equipamento_id != null ){
                //       data["equipamento_id"] = this.equipamento_id;

                //} 
                //if ( this.projeto_id != null ){
                //       data["projeto_id"] = this.projeto_id;

                //} 
                
                var fn_return = function (retorno){

                                 self.items = retorno.data;
                                 self.disableButton = false;

                }

                obj_api.call(url, method, data , fn_return);

                
  
        }
    }
    </script>