<template>
	<post  post_type="page" v-if="bl_mostra_form" v-bind:id_load="id_load"></post>

</template>
<script>
    export default {
              props: ['id_load'],

              data: function() {
		            return {
                         bl_mostra_form: false
		            }
		        },
              beforeRouteUpdate(to) {

              	console.log("estou aqui?  before route; "); console.log(to );

		         this.bl_mostra_form = false;
		          

		          this.post_type = to.params.post_type;
		         // this.type =  to.params.post_type;
		          this.id_load = to.params.id_load;
		         // this.id = to.params.id_load;
		         // this.load_component();

		         this.bl_mostra_form = true;

		        }

    }
</script>