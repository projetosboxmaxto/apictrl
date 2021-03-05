<template>
	<div>
          <upload v-bind:ptype="ptype" v-bind:onSave="onSave" msg_tamanho="COM TAMANHO DE 2000x600px"></upload>

          <span v-if="exibe_carregando"> Obtendo background
                        <img v-bind:src="img_loading | base_url" />
          </span>
          <div class="col-xs-12">

          	<div class="col-xs-2" v-for="(item, index) in items">
                      
                      <div>
                           <img v-bind:src="item.thumbnail" />

                      </div>
                      <span class="input-group-btn">

                                     
                              <button type="submit" class="btn btn-default btn-flat" v-on:click="change_description(item)">
                                    <i v-bind:class="getClassChecked(item)"></i>
                                </button>


				                        <button type="submit" class="btn btn-default btn-flat"
                                   v-on:click="setIndexConf(index)">
				                            <i class="fa fa-cogs"></i>
				                        </button>

				                        <button type="submit" class="btn btn-default btn-flat"
                                   v-on:click="remove_item(item)">
				                            <i class="fa fa-trash"></i>
				                        </button>
				      </span>
				      <div v-if="index == index_data">
                           <div class="form-group">
                           	<h3>Programar Exibição</h3>
                           	    <div class="col-xs-6" style="padding-left: 0px; margin-left: 0px">
                                 <input type="text" class="form-control" 
                                          placeholder="Data Início" 
                                          v-bind:id="getItemName(index, 'dtini')"
                                          v-bind:value="item.prog_dtini | date_show"
                                          v-on:focus="setdatepicker"
                                           style="width: 100px" 
                                   />
                               </div>
                           	    <div class="col-xs-6" style="padding-left: 0px; margin-left: 0px">
                           	    	<div class="input-group">
                                   <input type="text" class="form-control" 
                                          v-on:focus="setdatepicker"
                                          v-bind:value="item.prog_dtfim | date_show"
                                           v-bind:id="getItemName(index, 'dtfim')"
                                          placeholder="Data Fim"  style="width: 100px"
                                   />

                                          <div class="input-group-addon" style="border: none !important; padding: 0px 0px 0px 0px">
                                   <button type="submit" class="btn btn-default btn-flat"
                                   v-on:click="saveDatas(index)">
				                            <i class="fa fa-save"></i>
				                        </button>
				                    </div>
				                    </div>

                               </div>
                           </div>

				      </div>

          	</div>


          </div>

	</div>

</template>

<script>
    export default {
        props: ['ptype', 'p_parentid', 'show_drop','msg_tamanho'],

        data: function() {
            return {
            	items: [],

                exibe_carregando: false,
                img_loading: "loading.gif",
                index_data: -1
            }
        },
        methods:{

        	getItemName(index, nm){
        	       return nm+index.toString();	
        	},

        	    onSave(retorno, tip){

                    console.log("ON save..");
        	    	console.log( retorno );

        	    	for ( var i = 0 ; i < retorno.length; i++){

                                    this.items.unshift(retorno[i]);
        	    	}

                      

        	    },

	            getClassChecked(item){

	                 if ( item.caption =="1")
	                     return "fa fa-check-square";


	                  return "fa fa-stop";
	            },
	            change_description(item){

			                let new_description = item.caption;
			                let self = this;

			                if ( new_description == "1"){
			                    new_description = "0";
			                }else{
			                    new_description = "1";

			                }

			                var index = this.items.indexOf(item);

			                var fn_return = function(retorno){

			                	    item.caption = new_description;

			                         Vue.set(self.items, index, item);
			                }

			                let title = item.title;
			                let post_type = this.ptype;

			                if ( item.title_clean != null && item.title_clean != undefined){
			                     title = item.title_clean;
			                }

			                var data = {caption: new_description , content: item.content,
			                         alt: item.alt};

			                obj_api.call("midia/"+item.id, "put", data , fn_return);

              },

              	remove_item(item){
                              let self = this;

                              var fn_return = function (retorno){

                              	if ( retorno.code == "1"){

                                        self.items.splice(self.items.indexOf(item), 1);
                              	}

				              }


				                obj_api.call("midia/"+item.id, "delete", {} , fn_return);
		        	},

		        	setIndexConf(index){

		        		if ( this.index_data == index){
		        			//já estou aberto.
		        			this.index_data = -1;
		        			return;
		        		}


		        		this.index_data = index;
		        	},

		        	setdatepicker(event){

		        		var obj_id = event.target.id;

		        		 if ( ! $("#"+obj_id).hasClass("hasDatepicker") ){
				                      obj_editor.loadCalendar("#"+obj_id); 
				                      //$("#"+obj_id).datepicker();
				                      $("#"+obj_id).focus();
				                      $("#"+obj_id).click();
				                      console.log("tentando adicionar o datepicker");
				           }
		        	},
		        	saveDatas(index){

		        		var prog_dtini = $("#" +this.getItemName(index, 'dtini')).val();
		        		var prog_dtfim = $("#" +this.getItemName(index, 'dtfim')).val();
                        var item = this.items[index];
		        		


		        		 let self = this;

                              var fn_return = function (retorno){

                              	self.index_data = -1;
                              	obj_alert.show("Sucesso!","Programação atualizada com sucesso!",
                              		"success", null, 2500 );

				              }


				             obj_api.call("midia/prog/"+item.id, "put", {prog_dtini: prog_dtini, prog_dtfim: prog_dtfim } , fn_return);
		        	}


        },

        mounted() {

        	
					
                let self = this;

                this.exibe_carregando = true;

                 var fn_return = function (retorno){

                 	      console.log("retorno midia"); console.log( retorno );

				             self.items = retorno;       

                             self.exibe_carregando = false;

                             if ( self.items == undefined || self.items == null ){
                             	self.items = [];
                             }

				  }

                  console.log("TYPEIMAGE BACK: " + this.ptype );
				 obj_api.call("midia?type_image="+this.ptype, "get", {type_image: this.ptype} , fn_return);


        }
   

}


 </script>       
