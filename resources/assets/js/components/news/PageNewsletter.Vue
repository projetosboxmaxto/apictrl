<template>
	<div>

     <section class="content-header">

<section class="col-lg-9">
      <h1>NEWSLETTER

      </h1>
      <ol class="breadcrumb" style="display: none">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Forms</a></li>
        <li class="active">General Elements</li>
      </ol>
    </section>
</section>


<section class="col-lg-9">

   <div class="box">
                 <div class="box-body">
               
                     <post-simples slug="newsletter" ></post-simples>

                </div>

   </div>



</section>
<section class="col-lg-3">


<upload-unico 
   v-if="loading"
v-bind:id_parent="id" title="Imagem Background" ptype="p:backg"></upload-unico>

<email-last    ></email-last>




</section>

</div>
</template>

<script>
    export default {
           data: function() {
            return {
              id: "",
              loading: false,
              slug: "newsletter"
            }
          },
           mounted() {

                let self = this;

                 var fn_return = function (retorno){

                             var data = retorno.data;       
                              self.id = data.id;
                              self.loading = true;
                  }

                 obj_api.call("postslug/" + this.slug, "get", {} , fn_return);


           }


      }
</script>
