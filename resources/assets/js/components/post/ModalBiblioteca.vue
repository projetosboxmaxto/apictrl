<template>
	<div>
<!-- The Modal class="modal" -->
<div v-bind:id="modal_id" >

  <!-- Modal content -->
  <div class="modal-content">
    <span class="close">&times;</span>
  
   

      <imagelist pPageSize="12" 
           pColumnSize="4" 
            pUniqueID="" pEhModal="1"></imagelist>

  </div>

</div>

</div>
</template>

<script>
    export default {
        props: ['modal_id', 'onClose', 'onSave'],

        data: function() {
            return {


            	}
            }
        }
  </script>      
