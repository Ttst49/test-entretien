<template>
  <div>
    <div class="mb-2">
      <h1 class="text-4xl font-bold">Bienvenue sur ma belle application</h1>
      <p class="text-xl">Listing des demandes cliniques</p>
    </div>
    <div class="flex gap-2 flex-col w-full">
      <div 
        v-for="depot in depots"
        :key="depot.id"
        class="bg-white rounded-xl shadow-sm p-4"
      >
        <p class="text-base font-semibold">Titre: <span class="text-base text-gray-700 font-light">{{ depot.titre }}</span></p>
        <p class="text-base font-semibold">Description: <span class="text-base text-gray-700 font-light">{{ depot.description }}</span></p>
        <p class="text-base font-semibold">Date de création: <span class="text-base text-gray-700 font-light">{{ depot.date_creation }}</span></p>


        <div class="my-4 p-2 border border-gray rounded-xl bg-gray-100 flex flex-col gap-2" v-if="depot.reponses.length">

          <div v-if="selectedItemsId.includes(reponse.id)" class="border border-solid border-4 bg-blue-200 px-4 py-2" v-for="reponse in depot.reponses" :key="reponse.id">
            <p class="text-base font-semibold text-red-500">Type: <span class="text-base text-gray-700 font-light">{{ getTypeLabel(reponse.type) }}</span></p>
            <p class="text-base font-semibold">Titre: <span class="text-base text-gray-700 font-light">{{ reponse.titre }}</span></p>
            <p class="text-base font-semibold">Description: <span class="text-base text-gray-700 font-light">{{ reponse.description }}</span></p>
            <p class="text-base font-semibold">Date de création: <span class="text-base text-gray-700 font-light">{{ reponse.date_creation }}</span></p>
            <div class="mt-3 flex row relative h-11 w-full min-w-[200px]">
              <input placeholder="Raison"
                     class="peer h-full w-full border-b border-blue-gray-200 bg-transparent pt-4 pb-1.5 font-sans text-sm font-normal text-blue-gray-700 outline outline-0 transition-all placeholder-shown:border-blue-gray-200 focus:border-gray-900 focus:outline-0 disabled:border-0 disabled:bg-blue-gray-50"
                     v-model:value="reason"
              />
                <button v-if="reason===''" class="disabled bg-transparent hover:bg-gray-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded">
                  Valider
                </button>
              <button @click="addToValidatesReponses(reponse.id,reason)" v-if="reason!==''" class="bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded">
                Valider
              </button>
            </div>
          </div>

          <div v-if="!validatedItemsId.includes(reponse.id) && !selectedItemsId.includes(reponse.id) && reponse.state === false" class="cursor-pointer border border-dashed border-2 bg-white px-4 py-2" v-for="reponse in depot.reponses" @click="addToSelectedItems(reponse)" :key="reponse.id">
            <p class="text-base font-semibold text-red-500">Type: <span class="text-base text-gray-700 font-light">{{ getTypeLabel(reponse.type) }}</span></p>
            <p class="text-base font-semibold">Titre: <span class="text-base text-gray-700 font-light">{{ reponse.titre }}</span></p>
            <p class="text-base font-semibold">Description: <span class="text-base text-gray-700 font-light">{{ reponse.description }}</span></p>
            <p class="text-base font-semibold">Date de création: <span class="text-base text-gray-700 font-light">{{ reponse.date_creation }}</span></p>
          </div>

          <div v-if="!validatedItemsId.includes(reponse.id) && !selectedItemsId.includes(reponse.id) && reponse.state === true" class="border border-dashed border-2 bg-gray-400 px-4 py-2" v-for="reponse in depot.reponses" :key="reponse.id">
            <p class="text-base font-semibold">Type: <span class="text-base text-gray-700 font-light">{{ getTypeLabel(reponse.type) }}</span></p>
            <p class="text-base font-semibold">Titre: <span class="text-base text-gray-700 font-light">{{ reponse.titre }}</span></p>
            <p class="text-base font-semibold">Description: <span class="text-base text-gray-700 font-light">{{ reponse.description }}</span></p>
            <p class="text-base font-semibold">Date de création: <span class="text-base text-gray-700 font-light">{{ reponse.date_creation }}</span></p>
            <p class="text-base font-semibold text-white">Raison de validation:<br><span class="text-base text-white font-light">{{ reponse.ValidationReason }}</span></p>
          </div>

          <div v-if="validatedItemsId.includes(reponse.id)" class="border border-solid border-2 bg-green-200 px-4 py-2" v-for="reponse in depot.reponses" :key="reponse.id">
            <p class="text-base font-semibold text-red-500">Type: <span class="text-base text-gray-700 font-light">{{ getTypeLabel(reponse.type) }}</span></p>
            <p class="text-base font-semibold">Titre: <span class="text-base text-gray-700 font-light">{{ reponse.titre }}</span></p>
            <p class="text-base font-semibold">Description: <span class="text-base text-gray-700 font-light">{{ reponse.description }}</span></p>
            <p class="text-base font-semibold">Date de création: <span class="text-base text-gray-700 font-light">{{ reponse.date_creation }}</span></p>
          </div>

        </div>

        <div class="flex items-center justify-center" v-else>
          <p class="text-base font-semibold">Aucune réponse</p>
        </div>
        <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-2" @click="$router.push(`/depots/${depot.id}`)">Répondre à la demande</button>
      </div>
    </div>
    <button @click="validateAndSendToDatabase()" v-if="validatedItems.length" class="specialButton">
      Tout valider
    </button>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';
import { getLabel } from '@/enum/demande_clinique/reponse/type';
import axios from "axios";
import api from "../api";

export default {
  name: 'Index',
  data(){
    return{
      reason: "",
      selectedItemsId: [],
      validatedItems: [] ,
      validatedItemsId: []
    }
  },
  computed: {
    ...mapGetters({
      depots: 'demande_clinique/depots',
    }),
  },
  methods: {
    getTypeLabel: getLabel,
    addToSelectedItems(reponse){
      this.selectedItemsId = []
      this.selectedItemsId.push(reponse.id)
    },
    addToValidatesReponses(reponseId,reason){
      this.validatedItems.push( {
        "id":reponseId,
        "reason":reason
      })
        this.validatedItemsId.push(reponseId)
      this.selectedItemsId = []
      this.reason = ""
    },
    validateAndSendToDatabase(){
      let data = this.validatedItems
      api.demande_clinique.reponses.valider(data)
      $router.push('/')
    }
  }
};




</script>

<style scoped>
.disabled{
  cursor: not-allowed;
}

.specialButton{
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 20px;
  background-color: limegreen;
  border: none;
  color: white;
  font-weight: bolder;
  border-radius: 25px;
  position: fixed;
  bottom: 10vh;
  right: 10vh;
}
</style>