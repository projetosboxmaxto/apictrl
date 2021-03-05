<template>
<div>
    
     <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        Emails Cadastrados
      </h4>
    </div>
     <div class="panel-body" >
         
          <div v-if="total <= 0" class="box-body">
               <i>Ainda não há emails cadastrados</i>
       </div>
         
          <div v-if="total > 0" class="box-body">
           <table  class="table table-bordered table-striped">
           	  <thead>
           	  	<tr>
                  <th>Últimos (  Total: {{total}} )</th>
           	  	</tr>
           	  </thead>
           	  <tbody> 
           	  	 <tr v-for="item in items">
                      <td>
                      	   {{item.email_clean}}

                      </td>
           	  	 </tr>

           	  </tbody>

           </table>
       </div>

            
        </div>
     </div>
    

</div>
</template>

<script>
    export default {
		data: function() {
			            return {
			            	total: 0,
			            	items: []


			            }
			        },

		mounted() {

		 	console.log("Eu fui montado?? ");

                          let self = this;

                          var fn_return = function(retorno){
                                    self.total = retorno.data.length;
                                    self.items = retorno.data;
                          }

                          obj_api.call("emailnews","get",{limit: " limit 0, 10"}, fn_return);


       },

        methods: {

        }

    }
</script>