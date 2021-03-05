<template>
  <div>
<!--

import ElasticQueriesListCadTable from './components/elastic_queries/ElasticQueriesListCadTable'
import ElasticQueriesListConsTable from './components/elastic_queries/ElasticQueriesListConsTable'


Vue.component('elastic_queries_list_cad_table', ElasticQueriesListCadTable);
Vue.component('elastic_queries_list_cons_table', ElasticQueriesListConsTable );


-->
         <table class="table table-bordered table-striped">
             <thead>
             	<tr>
                     
                           <th>ID</th>  
                            <th>Título</th>  
                            <th>Querie (Elastic Search)</th>  
                            <th>Praça</th> 
                            <th>Ativo?</th>  
                            <th>
                              <button class="btn btn-default" v-on:click="add" type="button"
                              ><i class="fa fa-plus"></i></button>

                            </th>
             	</tr>
             </thead>
             <tbody>
                 <tr v-for="(item, index) in items" :key="index">
                   <td>{{item.id}}</td>
				   <td>  <input type="text" 
                                            v-model="item.titulo"
                                            v-on:change="change_value"
                                            maxlength="300"
                                            class="form-control"
                                            v-bind:id="getIDItem('titulo', index)">
                  
    </td> 
 <td>  
                   <textarea class="form-control" v-model="item.querie" v-bind:id="getIDItem('querie', index)"
                      style="height: 100px"></textarea> 
		 </td> 
      <td>   <select v-model="item.id_praca" v-bind:id="getIDItem('id_praca', index)">
              <option :value="-1">Todas</option>
              <option v-for="(item, index) in items_praca" :key="index">{{item.descricao}}</option>
            </select>
		  
									
									
									
			 </td> 
 <td>   <select v-model="item.ativo" v-bind:id="getIDItem('ativo', index)">
              <option :value="0">Não</option>
              <option :value="1">Sim</option>
            </select>
		  
									
									
									
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
                <td colspan="5">
                  <i v-if="!loading">Sem registros</i>
                  <i v-if="loading">Carregando</i>
                  
                  </td>
              </tr>
               <tr v-if="!loading">
                <td colspan="5">
                 
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
        props: ['id_load'],
        data: function() {
            return {
                
              items: [],
			  
			  
			  
              ids_excluir: [],
              items_praca: [],

              action: "list",
              id: "",
              table: null,
              filtro_titulo: "",
              filtro_status: "",

              show_new_button: true,
              loading: false,

              button_new_text: "" //<i class=\"fa fa-file\" ></i> NOVA POST"
            }
        },
        methods: {
            
            getIDItem(nome, index){
                return "elastic_queries_"+nome+"_L" +index.toString();
            },
            change_data_item(id_campo, value){

                   if ( id_campo.indexOf("data") > -1 ){

                        var ar = id_campo.split("_L");
                        var index = ar[1];
                        var item = this.items[ parseInt(index) ];
                        
                        var arcampo = id_campo.split("elastic_queries_");
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


                      var url =  "elastic_querie_grid"; 
                      var method = "POST";

                      var data = {
                             hd_json: hd_json,
                             ids_delete_json: ids_delete_json,
                             id_cliente: this.id_load
                      };
           
                      var fn_return = function (retorno){

                                     self.items = retorno.data;

                                     if (retorno.success){
                                        obj_alert.show("Sucesso!",
                                           "elastic_queries salvo com sucesso",
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
                                  titulo: "",  
                                  querie: "",  
                                  ativo: 0,  
                                  data: null,  
                                  id_cliente: this.id_cliente,  
                                  id_praca: null, 
                      };


                      return data;
            }
            
        },
        mounted() {

                let self = this;

                var url = "elastic_queries_edit/" + this.id_load; 
                //var url =  "elastic_queries/gridcad"; // +
                // this.id_load; console.log("monted post url: " + url );
                var method = "GET";
                this.disableButton = true;
                this.loading = true;

                var data = { }

                
                var fn_return = function (retorno){

                  console.log("carreguei? ", retorno );

                                 self.items = retorno.data;
                                 self.disableButton = false;
                                 self.items_praca = retorno.praca;
                                 self.loading = false;

                }

                obj_api.call(url, method, data , fn_return);

                
  
        }
    }
    </script>