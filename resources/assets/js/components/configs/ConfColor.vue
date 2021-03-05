<template>
	<div>
	        <div v-bind:id="trataId(idDiv)" class="colorSelector">
	        	<div v-bind:style="getStyleInicial()">
	        		
	        	</div>
	        </div>
                <label>{{color_name}}</label>
	  


     </div>
</template>
<script>  
 export default {
        props: ['idDiv','initialCor','onSave', 'color_name' ],

        data: function() {
            return {

              atual_cor: '',
              style_initial: '',
              widt: false
            }
        },
        computed: {
              
        },
        methods: {
            
            trataId(color){
                
                if ( color == undefined ){
                    return "cor";
                }
                    return "cor" + color.replace("@","");  
            },

        	getStyleInicial(){

        		if ( this.atual_cor != null ){
          	              this.style_initial = "background-color: " + this.atual_cor;

                    }

                    return this.style_initial;
        	}
                  
        },
        mounted() {
        	console.log("carregando cor: " + this.initialCor  + " " + this.idDiv );
		   if ( this.initialCor != null ){
		   	      this.atual_cor = this.initialCor;
		   }
        	

         
        	let self = this;

        	console.log("vou exibir cor? " + this.idDiv  +" " + this.atual_cor);
        	console.log( $('#' + self.trataId( this.idDiv ) )  );

                 $('#' + self.trataId( this.idDiv ) ).ColorPicker({
						color: this.atual_cor.toString().replace("#",""),
						onShow: function (colpkr) {
							$(colpkr).fadeIn(500);
							return false;
						},
						onHide: function (colpkr) {
							$(colpkr).fadeOut(100);

							console.log("OnSave?"); console.log( self.onSave );

							if ( self.onSave != null ){
								self.onSave( self.atual_cor, self.idDiv);
							}



							return false;
						},
						onChange: function (hsb, hex, rgb) {
							//console.log("Mudei a cor? " + hex );

							$('#'+self.trataId(this.idDiv) +' div').css('backgroundColor', '#' + hex);
							self.atual_cor = '#' + hex;

							//console.log( $('#'+this.idDiv +' div') );

							
						}
					});

                 $('#' + self.trataId(this.idDiv) ).bind('click', function() {
						$('#' + self.trataId(this.idDiv) ).stop().animate({height: self.widt ? 0 : 173}, 500);
						self.widt = !self.widt;
				 });

          }
     }

</script>