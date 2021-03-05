<template v-if="visible">
  <div>
    <div >
        <div v-bind:className="classe_div">
             TESTE  {{mensagem}}
        </div>
    </div>
  </div>
</template>

<script>

export default {
  props: ['msg', 'type', 'show'],

  data() {
    return {
      visible: this.show != null && this.show == "on" ? true: false,
      classe_div: "alert " + (  props.type != null ? props.type : ""),
      mensagem: props.msg != null ? props.msg : ""
    };
  },
};
</script>