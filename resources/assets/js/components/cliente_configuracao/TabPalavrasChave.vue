<template>
    
<div>
<ul class="nav nav-tabs" id="myTab" role="tablist">

    <li class="nav-item active">
    <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="true" aria-expanded="true">Filtro por Praça</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="false">Palavras-Chave</a>
  </li>

 
</ul>

<!-- Tab panes -->
<div class="tab-content" style="background: white">
  <div class="tab-pane active" id="profile" role="tabpanel" aria-labelledby="profile-tab">

   <search_queries_list_cad_table  v-bind:id_load="id_load" 
          :nome_cliente="nome_cliente"
          show_back_button="true" ></search_queries_list_cad_table> <!-- v-bind:onBack="onBack" -->

  </div>
  
  <div class="tab-pane" id="home" role="tabpanel" aria-labelledby="home-tab"></div>
</div>

</div>
</template>

<script>

    export default {

        props: ["id_load", "nome_cliente"],

        data: function() {
            return {

              action: "list",
              id: "",
              table: null,
              filtro_titulo: "",
              filtro_status: "",

              show_new_button: false,

              button_new_text: "" //<i class=\"fa fa-file\" ></i> NOVA POST"
            }
        },
        methods: {
        },
        mounted(){

                $('#myTab a').on('click', function (e) {
                      e.preventDefault()
                      $(this).tab('show')
                });

        }
    }
</script>