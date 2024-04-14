<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm,usePage,Link,router,Head } from "@inertiajs/vue3";
import EditGroupCardModal from '@/Components/GroupCards/EditGroupCardModal.vue';
import AddGroupCardModal from '@/Components/GroupCards/AddGroupCardModal.vue';
import { ref,onMounted,onUnmounted } from "vue";

defineProps({ groupCards: Object });
const showModal = ref(false);
const editModalVisible = ref(false); 
const selectedGroupCard = ref(null); 
const form = useForm({});
const groupCards = usePage().props.groupCards.data;
const groupCardsPagination = usePage().props.groupCards.links;
const clickedMenuIndex = ref(null);

const toggleMenu = (id) => {
    if (clickedMenuIndex.value === null || clickedMenuIndex.value !== id) {
        groupCards.forEach((item) => {
            item.isMenuOpen = false;
        });
    }
    groupCards[id].isMenuOpen = !groupCards[id].isMenuOpen;
    clickedMenuIndex.value = groupCards[id].isMenuOpen ? id : null;
};

const editGroupCard = (groupCard) => {
  if (groupCard) {
    selectedGroupCard.value = groupCard; 
    editModalVisible.value = true; 
  }
};

const deleteGroupCard = async (id) => {
  if (confirm("Are you sure you want to delete this activity?")) {
    try {
        form.delete(`groupCards/${id}`); 
        setTimeout(()=>{
            router.get('/groupCards')            
        },400)
    } catch (error) {
      console.error("Error deleting :", error);
    }
  }
};

const handleClickOutside = (event) => {
    if (!event.target.closest(".menu-container")) {
        groupCards.forEach((item) => {
            item.isMenuOpen = false;
        });
        clickedMenuIndex.value = null;
    }
};
onMounted(() => {
    document.addEventListener("click", handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener("click", handleClickOutside);
});
</script>
<template>
    <Head title="Group Cards" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">What is your focus for today?</h2>
                <button @click="showModal = true" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4">
          + New category
        </button>
            </div>
        </template>
        <AddGroupCardModal :is-visible="showModal" @close="showModal = false" />
        <EditGroupCardModal v-if="selectedGroupCard" :is-visible="editModalVisible" @close="editModalVisible = false" :groupCard="selectedGroupCard" />
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6 text-gray-900">
                        <ul role="list"  class="grid grid-cols-1 gap-6  lg:grid-cols-3 ">
                            <li v-for="(groupCard, index) in groupCards" :key="groupCard.id"
                             class="h-[120px] border border-gray-200 rounded-lg"
                              :style="{ backgroundColor: groupCard.color }">
                                <div class="flex justify-between items-center gap-x-4 h-full" >
                                    <a :href="route('cards.index',groupCard.id)" class="p-6 h-full w-full flex items-center text-lg font-medium leading-6 text-gray-100">{{ groupCard.name }}</a>
                                    <div class="relative ml-auto p-6">
                                        <button type="button" @click="()=>toggleMenu(index)"
                                            class="-m-2.5 block p-2.5 text-gray-200 hover:text-gray-500 menu-container"
                                            id="options-menu-0-button" aria-expanded="false" aria-haspopup="true">
                                            <span class="sr-only">Open options</span>
                                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"
                                                aria-hidden="true">
                                                <path
                                                    d="M3 10a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0zM8.5 10a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0zM15.5 8.5a1.5 1.5 0 100 3 1.5 1.5 0 000-3z" />
                                            </svg>
                                        </button>
                                        <div v-show="groupCard.isMenuOpen" class="absolute right-0 z-10 mt-0.5 w-32 origin-top-right rounded-md bg-white py-2 shadow-lg ring-1 ring-gray-900/5 focus:outline-none"
                                            role="menu" aria-orientation="vertical"
                                            :aria-labelledby="'options-menu-0-button-' + index" tabindex="-1">
                                            <button @click="editGroupCard(groupCard)" class="block px-3 py-1 text-sm leading-6 text-gray-900"
                                                role="menuitem" tabindex="-1" :id="'options-menu-0-item-1-' + index">Edit<span
                                                    class="sr-only">,
                                                    Tuple</span></button>
                                                    <button  class="block px-3 py-1 text-sm leading-6 text-gray-900" @click="()=>deleteGroupCard(groupCard.id)"
                                                role="menuitem" tabindex="-1" :id="'options-menu-0-item-1-' + index">Delete<span
                                                    class="sr-only">,
                                                    Tuple</span></button>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                        <div class="px-1 pt-10 flex w-full justify-center gap-1">
                        <Link
                            v-for=" (link, index) in groupCardsPagination" 
                            :key="index" 
                            :href="link.url"
                            class="p-2 border border-gray-300 rounded-md hover:bg-gray-400"
                            v-html="link.label"
                        />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
