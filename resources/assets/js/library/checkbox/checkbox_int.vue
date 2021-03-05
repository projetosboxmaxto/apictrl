<template>
  	   <input  v-bind:class="class_name != null && class_name != undefined ? class_name : 'md-accent'"
            :name="name" :id="name" v-model="checked"
							           type="checkbox" > 
   
</template>
<style scoped>
/*
.green {
  color: white;

  background-color: #b1c32f;
  border-color: #b1c32f;
}
.md-checked,
.green {
  color: white;

  background-color: #b1c32f;
  border-color: #b1c32f;
}
*/
</style>
<script>
export default {
  props: ["value", "class_name", "name"],
  model: {
    prop: "value",
    event: "selected"
  },
  watch: {
    checked(val) {
      this.$emit("selected", this.checked ? 1 : 0);
      this.$emit("change", this.checked);
      this.$emit("checked");
    }
  },
  data() {
    return {
      checked: false
    };
  },

  mounted() {
    if (this.value != null && this.value != undefined) {
      this.checked = this.value.toString() == "1";
    }
  }
};
</script>