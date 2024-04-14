<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useForm, usePage, Link, router, Head } from "@inertiajs/vue3";
import { ref,onMounted,onUnmounted } from "vue";
import AddCardModal from '@/Components/Cards/AddCardModal.vue';
import EditCardModal from '@/Components/Cards/EditCardModal.vue';

defineProps({ cards: Object, id: Object, groupCardName: Object });
const form = useForm({});
const showModal = ref(false);
const selectedGroupCard = ref(null);
const cards = usePage().props.cards.data;
const cardsPagination = usePage().props.cards.links;
const groupCardId = usePage().props.id;
const editModalVisible = ref(false);
const newTaskDescription = ref(null);
const showInput = ref({});
const inputTaskId = ref(null);
const clickedMenuIndex = ref(null);

cards.forEach((card) => {
    showInput.value[card.id] = false;
    inputTaskId.value = null;
});

const toggleInput = (taskId) => {
    showInput.value[taskId] = !showInput.value[taskId];
    inputTaskId.value = taskId;
    if (!showInput.value[taskId]) {
        newTaskDescription.value = null;
    }
};


const toggleMenu = (id) => {
    if (clickedMenuIndex.value === null || clickedMenuIndex.value !== id) {
        cards.forEach((item) => {
            item.isMenuOpen = false;
        });
    }
    cards[id].isMenuOpen = !cards[id].isMenuOpen;
    clickedMenuIndex.value = cards[id].isMenuOpen ? id : null;
};

const handleClickOutside = (event) => {
    if (!event.target.closest(".menu-container")) {
        cards.forEach((item) => {
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

const editGroupCard = (groupCard) => {
    if (groupCard) {
        selectedGroupCard.value = groupCard;
        editModalVisible.value = true;
    }
};

const deleteCard = async (id) => {
    if (confirm("Are you sure you want to delete this card?")) {
        try {
            router.delete(route('cards.destroy', id));
            setTimeout(() => {
                router.get('/all-cards/' + usePage().props.id)
            }, 300);
        } catch (error) {
            console.error("Error deleting :", error);
        }
    }
};

const completeTask = async (task) => {
    if (confirm("Have you completed this task?")) {
        try {
            router.put(route('task.toggleCompleted', task.id));
            setTimeout(() => {
                router.get('/all-cards/' + usePage().props.id)
            }, 400);
        } catch (error) {
            console.error("Error  :", error);
        }
    }
};

const editDescription = (task) => {
    task.editing = true;
    task.originalDescription = task.description;
};

const saveDescription = async (task) => {
    try {
        const requestData = {
            description: task.description,
            card_id: task.card_id
        };

        await router.put(route('task.update', task.id), requestData);
        task.editing = false;
        setTimeout(() => {
            router.get('/all-cards/' + usePage().props.id)
        }, 400);
    } catch (error) {
        console.error("Error updating task description:", error);
    }
};

const addNewTask = async () => {
    try {
        const requestData = {
            description: newTaskDescription.value,
            card_id: inputTaskId.value
        };
        router.post(route('task.store'), requestData);
        setTimeout(() => {
            router.get('/all-cards/' + usePage().props.id)
        }, 200);
    } catch (error) {
        console.error("Error adding new task:", error);
    }
};
</script>

<template>

    <Head title="Cards" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{
                    usePage().props.groupCardName[0] }}</h2>
                <button @click="showModal = true"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4">
                    + New card
                </button>
            </div>
        </template>
        <AddCardModal :is-visible="showModal" :id="groupCardId" @close="showModal = false" />
        <EditCardModal v-if="selectedGroupCard" :is-visible="editModalVisible" @close="editModalVisible = false"
            :card="selectedGroupCard" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6 text-gray-900">
                        <ul role="list" class="grid grid-cols-1 gap-6  lg:grid-cols-3 ">
                            <li v-for="(card, index) in cards" :key="card.id"
                                class="p-6 min-h-[120px] border border-gray-200 rounded-lg "
                                :style="{ backgroundColor: card.color }">
                                <div class="flex justify-between items-center gap-x-4 border-b border-gray-300">
                                    <p class="text-lg font-medium leading-6 text-slate-900">{{ card.name }}</p>
                                    <div class="relative ml-auto">
                                        <button type="button" @click="() => toggleMenu(index)"
                                            class="-m-2.5 block p-2.5 text-gray-500 hover:text-gray-600 menu-container"
                                            id="options-menu-0-button" aria-expanded="false" aria-haspopup="true">
                                            <span class="sr-only">Open options</span>
                                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"
                                                aria-hidden="true">
                                                <path
                                                    d="M3 10a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0zM8.5 10a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0zM15.5 8.5a1.5 1.5 0 100 3 1.5 1.5 0 000-3z" />
                                            </svg>
                                        </button>
                                        <div v-show="card.isMenuOpen"
                                            class="absolute right-0 z-10 mt-0.5 w-32 origin-top-right rounded-md bg-white py-2 shadow-lg ring-1 ring-gray-900/5 focus:outline-none"
                                            role="menu" aria-orientation="vertical"
                                            :aria-labelledby="'options-menu-0-button-' + index" tabindex="-1">
                                            <button @click="editGroupCard(card)"
                                                class="block px-3 py-1 text-sm leading-6 text-gray-900" role="menuitem"
                                                tabindex="-1" :id="'options-menu-0-item-1-' + index">Edit<span
                                                    class="sr-only">,
                                                    Tuple</span></button>
                                            <button class="block px-3 py-1 text-sm leading-6 text-gray-900"
                                                @click="() => deleteCard(card.id)" role="menuitem" tabindex="-1"
                                                :id="'options-menu-0-item-1-' + index">Delete<span class="sr-only">,
                                                    Tuple</span></button>
                                        </div>
                                    </div>
                                </div>
                                <div v-for="(task, index) in card.tasks" :key="task.id" class="flex flex-col gap-4">
                                    <div>
                                        <div class="flex items-center gap-2 mt-1.5">
                                            <div v-if="!task.is_completed" @click="completeTask(task)">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M11.9999 23.6667C5.55659 23.6667 0.333252 18.4433 0.333252 12C0.333252 5.55672 5.55659 0.333374 11.9999 0.333374C18.4432 0.333374 23.6666 5.55672 23.6666 12C23.6666 18.4433 18.4432 23.6667 11.9999 23.6667ZM11.9999 21.3334C17.1546 21.3334 21.3333 17.1547 21.3333 12C21.3333 6.84538 17.1546 2.66671 11.9999 2.66671C6.84526 2.66671 2.66659 6.84538 2.66659 12C2.66659 17.1547 6.84526 21.3334 11.9999 21.3334ZM10.8363 16.6667L5.88657 11.717L7.5365 10.067L10.8363 13.3669L17.436 6.76721L19.0859 8.41713L10.8363 16.6667Z"
                                                        fill="#000" />
                                                </svg>
                                            </div>
                                            <div v-if="!task.editing" @dblclick="editDescription(task)"
                                                class="bg-gray-200 p-3 text-center rounded-md w-full text-slate-900"
                                                :class="{ 'line-through': task.is_completed }">
                                                {{ task.description }}
                                            </div>
                                            <input v-else v-model="task.description" @blur="saveDescription(task)"
                                                @keydown.enter="saveDescription(task)"
                                                class="bg-gray-200 p-3 text-center rounded-md w-full text-slate-900" />
                                            <div v-if="task.is_completed" @click="completeTask(task)">
                                                <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M6.99976 5.46871L12.362 0.106445L13.8941 1.63851L8.53181 7.00076L13.8941 12.3629L12.362 13.895L6.99976 8.53281L1.63754 13.895L0.105469 12.3629L5.46771 7.00076L0.105469 1.63851L1.63754 0.106445L6.99976 5.46871Z"
                                                        fill="#000" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="inputTaskId !== null && card.id === inputTaskId" class="bg-gray-200 p-3 text-center rounded-md w-full text-slate-900 mt-4">
        <input v-if="showInput[card.id]" v-model="newTaskDescription"  @keydown.enter="addNewTask(card)" type="text" class="block w-full px-4 py-2 mt-4 text-gray-900 border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" placeholder="Enter task description...">
    </div>
                        <button @click="toggleInput(card.id)" class="block w-full py-2 text-center text-gray-900 rounded-md bg-gray-300 mt-4">
        {{ showInput[card.id] ? 'Cancel' : 'Add New Task' }}
    </button>
                            </li>
                        </ul>
                        <div class="px-1 pt-10 flex w-full justify-center gap-1">
                            <Link v-for=" (link, index) in cardsPagination" :key="index" :href="link.url"
                                class="p-2 border border-gray-300 rounded-md hover:bg-gray-400" v-html="link.label" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
