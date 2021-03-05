<template>
	 <div>
           <span v-if="exibe_carregando"> Obtendo rodapé
                        <img v-bind:src="img_loading | base_url" />
           </span>

         <div class="col-lg-12"  style="margin-top: 15px">
               <div  style="padding-left: 15px">
               	 <h3 style="margin-left: 0px; margin-top: 0px">Rodapé</h3>
                
               </div>


          
	 	         <bottom-site botao_add="1" v-bind:onSave="onSave"></bottom-site>


           </div>

        <div v-if="!exibe_carregando" class="col-xs-12">
           <div class="col-xs-6" style="margin-top: 10px;" 
                       v-for="item in items">
           	
           	   <div class="box box-default">
                   <div class="box-body">
	 	             <bottom-site botao_add="0" v-bind:item="item"
	 	                   v-bind:onSave="onSave"></bottom-site>
	 	           </div>
           	   </div>
           	
           </div>
        </div>


       
	 </div>

</template>
<script>
    export default {
    	   //props: ['item'],

           data: function() {
            return {
                   items: [],
                   exibe_carregando: false,
                   img_loading: "loading.gif"
            }
           },
           mounted() {
                       
                this.load_data();

           },


	        methods: {

	        	   load_data(){

                     var data = { type: "bottom", orderby: "p.id asc" };
                     let self = this;

                       this.exibe_carregando = true;
                       var fn_return = function (retorno){
                       	console.log("Obteve dados de rodapé");
                       	console.log(retorno.data);

                                       //self.items = self.items.slice();


                                       self.items = retorno.data;
                                       self.exibe_carregando = false;
                        }

                      obj_api.call("postgrid", "get", data , fn_return);
                    },

                    onSave(retorno, option){

                    	let self = this;


                       	console.log("On salvo: ");
                       	console.log(option );

                    	if ( option == "save"){
                    		 self.items = self.items.slice();
                    		 this.load_data();
                          // self.items.push(retorno.item);
                    	}

                    	if ( option == "delete"){
                    		 self.items = self.items.slice();

                       	        console.log("On delete: ");
                       	         console.log( self.items );
                    		 this.load_data();
                    	}
                    }

	        }


    }
</script>