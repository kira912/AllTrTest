<template>
  <div id="app">
    <b-row>
      <b-col></b-col>
      <b-col cols="4">
        <b-form-select name="product-size" :options="options" v-model="selectedOption">
        </b-form-select>
        <h3 class="text-danger" v-if="selected"> {{selected.value.dataPrice}} </h3>
      </b-col>
      <b-col></b-col>
    </b-row>
  </div>
</template>

<script>

const deliveryMessage = 'Chez vous dès vendredi'
export default {
  name: 'app',
  computed: {
    selected() {
      return this.options.find((option) => {
        return option.value === this.selectedOption ? option.value.dataPrice : null
      })
    }
  },
  data() {
    return {
      selectedOption: '',
      options: [
        {
          value: '',
          text: "Veuillez sélectionner une taille"
        },
        {
          value: {
            value: "10",
            dataStock: "2",
            dataPrice: "26.00 €",
            size: "S"
          },
          get text(){
            return this.value.size + "  " + deliveryMessage + "  " + this.value.dataPrice.fontcolor('red')
          }
        },
        {
          value: {
            value: "11",
            dataStock: "5",
            dataPrice: "15.00 €",
            size: "M"
          },
          get text(){
            return this.value.size + "  " + deliveryMessage + "  " + this.value.dataPrice
          }
        },
        {
          value: {
            value: "12",
            dataStock: "0",
            dataPrice: "18.00 €",
            size: "L"
          },
          get text(){
            return this.value.size + "  " + deliveryMessage + "  " + this.value.dataPrice
          }
        },
        {
          value: {
            value: "13",
            dataStock: "2",
            dataPrice: "22.00 €",
            size: "XL"
          },
          get text(){
            return this.value.size + "  " + deliveryMessage + "  " + this.value.dataPrice
          }
        }
      ]
    }
  }
};
</script>

<style>
#app {
  font-family: 'Avenir', Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  color: #2c3e50;
  margin-top: 60px;
}

</style>
